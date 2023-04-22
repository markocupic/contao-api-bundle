<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi\Manager;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Contao\StringUtil;
use Markocupic\ContaoContentApi\Api\ApiInterface;
use Markocupic\ContaoContentApi\Model\ApiAppModel;
use Markocupic\ContaoContentApi\Util\ApiUtil;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiResourceManager
{
    /**
     * Assigned via compile pass during compilation.
     *
     * @var array <ApiInterface>
     */
    private array $resources = [];

    /**
     * Assigned via compile pass during compilation.
     *
     * @var array <string>
     */
    private array $services = [];

    private readonly Adapter $stringUtil;
    private readonly Adapter $apiAppModel;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly RequestStack $requestStack,
        private readonly ApiUtil $apiUtil,
    ) {
        $this->stringUtil = $this->framework->getAdapter(StringUtil::class);
        $this->apiAppModel = $this->framework->getAdapter(ApiAppModel::class);
    }

    public function add(ApiInterface $apiResource, string $alias, string $serviceId): void
    {
        $this->resources[$alias] = $apiResource;
        $this->services[$alias] = $serviceId;
    }

    public function get(string $strKey): ApiInterface|null
    {
        $model = $this->apiAppModel->findOneByKey($strKey);

        if (null === $model) {
            throw new \Exception(sprintf('Could not find a assigned API configuration for the key "%s" Please check the API configuration in the Contao Backend.', $strKey));
        }

        $resConfig = $this->apiUtil->getResourceConfigByName($model->resourceType);

        if (null === $resConfig) {
            throw new \Exception(sprintf('Could not find an API configuration for the resource type "%s".', $model->resourceType));
        }

        return $this->resources[$resConfig['type']];
    }

    public function hasValidKey(string $strKey): bool
    {
        $model = $this->apiAppModel->findOneByKey($strKey);

        return null !== $model;
    }

    public function isUserAllowed(string $strKey, FrontendUser|null $user): bool
    {
        if (null === ($model = $this->apiAppModel->findOneByKey($strKey))) {
            return false;
        }

        if ($model->mProtect) {
            if (!$user) {
                return false;
            }

            $memberGroups = $this->stringUtil->deserialize($user->groups, true);
            $allowedGroups = $this->stringUtil->deserialize($model->mGroups, true);

            return (bool) array_intersect($allowedGroups, $memberGroups);
        }

        return true;
    }
}
