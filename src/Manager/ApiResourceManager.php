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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiResourceManager
{
    private array $resources = [];
    private array $services = [];

    private readonly Adapter $stringUtil;
    private readonly Adapter $apiAppModel;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly RequestStack $requestStack,
    ) {
        $this->stringUtil = $this->framework->getAdapter(StringUtil::class);
        $this->apiAppModel = $this->framework->getAdapter(ApiAppModel::class);
    }

    public function add(ApiInterface $apiResource, string $serviceId, string $alias, string $type, string|null $modelClass = null, string|null $verboseName = null): void
    {
        $this->resources[$alias] = $apiResource;
        $this->services[$alias] = [
            'serviceId' => $serviceId,
            'alias' => $alias,
            'type' => $type,
            'modelClass' => $modelClass,
            'verboseName' => $verboseName,
        ];
    }

    public function get(string $apiKey, Request $request): ApiInterface|null
    {
        $model = $this->apiAppModel->findOneByKey($apiKey);

        if (null === $model) {
            throw new \Exception(sprintf('Could not find an API configuration related to the key "%s". Please check your API configuration in the Contao Backend.', $apiKey));
        }

        $apiResource = $this->getResourceByAlias($model->resourceAlias);

        if (null === $apiResource) {
            throw new \Exception(sprintf('Could not find an API resource for the resource alias "%s".', $model->resourceAlias));
        }

        return $apiResource;
    }

    public function hasValidKey(string $apiKey, Request $request): bool
    {
        $model = $this->apiAppModel->findOneByKey($apiKey);

        return null !== $model;
    }

    public function isUserAllowed(string $apiKey, Request $request, FrontendUser|null $user): bool
    {
        if (null === ($model = $this->apiAppModel->findOneByKey($apiKey))) {
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

    public function getResourceByAlias(string $alias): ApiInterface|null
    {
        return $this->resources[$alias] ?? null;
    }

    public function getResourceConfigByAlias(string $alias): array|null
    {
        return $this->services[$alias] ?? null;
    }

    public function getResourceTypes(): array
    {
        $types = [];

        foreach ($this->services as $arrProp) {
            $types[] = $arrProp['type'];
        }

        return array_filter(array_unique($types));
    }

    public function getResourceAliasesByType(string $type): array
    {
        $aliases = [];

        foreach ($this->services as $arrProp) {
            if ($arrProp['type'] === $type) {
                $aliases[] = $arrProp['alias'];
            }
        }

        return array_filter(array_unique($aliases));
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function getResources(): array
    {
        return $this->resources;
    }
}
