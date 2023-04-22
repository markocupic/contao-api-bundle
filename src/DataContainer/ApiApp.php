<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Markocupic\ContaoContentApi\Model\ApiAppModel;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiApp
{
    private readonly Adapter $apiAppModel;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Connection $connection,
        private readonly RequestStack $requestStack,
        private readonly array $contaoContentApiResources,
    ) {
        $this->apiAppModel = $this->framework->getAdapter(ApiAppModel::class);
    }

    #[AsCallback(table: 'tl_api_app', target: 'fields.key.load', priority: 100)]
    public function generateApiToken(string $value, DataContainer $dc)
    {
        if ('' !== $value) {
            return $value;
        }

        if (null === ($model = $this->apiAppModel->findByPk($dc->id))) {
            return $value;
        }

        $model->key = md5(uniqid('', true));
        $model->save();

        return $model->key;
    }

    #[AsCallback(table: 'tl_api_app', target: 'fields.resourceType.options', priority: 100)]
    public function getResourceTypes(): array
    {
        $opt = [];

        if (empty($this->contaoContentApiResources)) {
            throw new \Exception('No resources set in markocupic_contao_content_api');
        }

        foreach ($this->contaoContentApiResources as $resource) {
            $opt[$resource['name']] = $resource['name'];
        }

        return $opt;
    }

    #[AsCallback(table: 'tl_api_app', target: 'fields.allowedModules.options', priority: 100)]
    public function getFrontendModules(): array
    {
        $opt = [];
        $result = $this->connection->executeQuery('SELECT id,name FROM tl_module ORDER BY name');

        while (false !== ($arrModules = $result->fetchAssociative())) {
            $opt[$arrModules['id']] = $arrModules['name'];
        }

        return $opt;
    }

    #[AsCallback(table: 'tl_api_app', target: 'config.onload', priority: 100)]
    public function setPalette(DataContainer $dc): void
    {
        $id = $dc->id;

        if (!empty($id) && $id > 0) {
            $record = $this->connection->fetchAssociative('SELECT * FROM '.$dc->table.' WHERE id = ?', [$id]);

            if (false !== $record) {
                if ('' !== $record['resourceType']) {
                    $GLOBALS['TL_DCA'][$dc->table]['palettes']['default'] = $GLOBALS['TL_DCA'][$dc->table]['palettes'][$record['resourceType']];
                }
            }
        }
    }
}
