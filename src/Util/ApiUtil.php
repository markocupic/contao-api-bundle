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

namespace Markocupic\ContaoContentApi\Util;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\ContaoContentApi\Manager\ApiResourceManager;
use Markocupic\ContaoContentApi\Model\ApiAppModel;

class ApiUtil
{
    private Adapter $apiAppModel;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ApiResourceManager $apiResourceManager,
    ) {
        $this->apiAppModel = $this->framework->getAdapter(ApiAppModel::class);
    }

    public function getApiResourceConfigurationFromApiKey(string $apiKey): array|null
    {
        if (null === ($model = $this->apiAppModel->findOneByKey($apiKey))) {
            return null;
        }

        $services = $this->apiResourceManager->getServices();

        return $services[$model->resourceAlias] ?? null;
    }
}
