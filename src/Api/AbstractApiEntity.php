<?php

declare(strict_types=1);

/*
 * This file is part of Contao Api Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-api-bundle
 */

namespace Markocupic\ContaoApiBundle\Api;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Markocupic\ContaoApiBundle\Json\ContaoJson;
use Markocupic\ContaoApiBundle\Manager\ApiResourceManager;
use Markocupic\ContaoApiBundle\Model\ApiAppModel;
use Markocupic\ContaoApiBundle\Response\ResponseData\DefaultResponseData;
use Markocupic\ContaoApiBundle\Util\ApiUtil;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractApiEntity extends AbstractApi
{
    // Adapters
    private readonly Adapter $apiAppModel;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ApiUtil $apiUtil,
        private readonly ApiResourceManager $apiResourceManager,
    ) {
        $this->apiAppModel = $this->framework->getAdapter(ApiAppModel::class);

        $this->initializeResponseData(new DefaultResponseData());
    }

    public function getFromId(int $id, Request $request): ApiInterface
    {
        $apiKey = $request->attributes->get('apiKey');

        $resConfig = $this->apiUtil->getApiResourceConfigurationFromApiKey($apiKey);

        if (null === $resConfig) {
            return $this->returnError(sprintf('No Api found for api key "%s".', $apiKey));
        }

        $modelClass = $resConfig['modelClass'];

        if (empty($modelClass) || !class_exists($modelClass)) {
            return $this->returnError(sprintf('Class "%s" not found.', $modelClass));
        }

        $row = $modelClass::findByPk($id);

        $this->responseData->setRow(
            [
                'id' => $id,
                'type' => $resConfig['type'],
                'alias' => $resConfig['alias'],
                'data' => $row->row(),
            ]
        );

        $this->triggerApiModuleGeneratedHook();

        return $this;
    }

    public function get(string $apiKey, int $id, Request $request, FrontendUser|null $user): ApiInterface
    {
        if (null === ($model = $this->apiAppModel->findOneByKey($apiKey))) {
            return $this->returnError(sprintf('No App configuration found for key "%s".', $apiKey));
        }

        if (null === ($resConfig = $this->apiResourceManager->getResourceConfigByAlias($model->resourceAlias))) {
            return $this->returnError(sprintf('No Api found for alias "%s".', $model->resourceAlias));
        }

        if (!$this->isAllowed($model, $id, $request)) {
            return $this->returnError(sprintf('Access to resource with ID %s denied.', $id));
        }

        if (null === $resConfig['modelClass']::findByPk($id)) {
            return $this->returnError(sprintf('No entity found for ID %s.', $id));
        }

        return $this->getFromId($id, $request);
    }

    public function isAllowed(ApiAppModel $apiAppModel, int $id, Request $request): bool
    {
        return true;
    }

    public function toJson(): ContaoJson
    {
        if (!$this->responseData) {
            return new ContaoJson(null);
        }

        return new ContaoJson($this);
    }

    private function triggerApiModuleGeneratedHook(): void
    {
        if (isset($GLOBALS['TL_HOOKS']['apiModuleGenerated']) && \is_array($GLOBALS['TL_HOOKS']['apiModuleGenerated'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiModuleGenerated'] as $callback) {
                $callback[0]::{$callback[1]}($this);
            }
        }
    }
}
