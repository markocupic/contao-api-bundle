<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi\Manager;

use Markocupic\ContaoContentApi\DependencyInjection\Configuration;
use Markocupic\ContaoContentApi\Model\ApiModel;
use Markocupic\ContaoContentApi\User\Contao\ContaoFrontendUser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiResourceManager
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var ContaoFrontendUser
     */
    public $user;

    /**
     * @var Request
     */
    public $request;

    private $resources = [];

    private $services = [];

    private $resource;

    /**
     * @var array
     */
    private $apiConfig;

    /**
     * @var ApiModel
     */
    private $apiModel;

    public function __construct(ContainerInterface $container, ContaoFrontendUser $user)
    {
        $this->container = $container;
        $this->user = $user;
    }

    /**
     * Add a resource for given alias.
     *
     * @param ResourceInterface $resource
     */
    public function add($resource, string $alias, string $id): void
    {
        $this->resources[$alias] = $resource;
        $this->services[$alias] = $id;
    }

    public function get(string $strAlias)
    {
        if ($this->setResourceFromAlias($strAlias)) {
            return $this;
        }

        return null;
    }

    public function show()
    {
        return $this->resource->get($this);
    }

    /**
     * Return data current resource in config.yml
     * @return array|null
     */
    public function getApiConfig(): ?array
    {
        return $this->apiConfig;
    }

    public function getApiModel(): ?ApiModel
    {
        return $this->apiModel;
    }

    public function getFrontendUser(): ?ContaoFrontendUser
    {
        return $this->user;
    }

    private function setResourceFromAlias(string $strAlias): bool
    {
        $apiRes = ApiModel::findOneByAlias($strAlias);

        if (null !== $apiRes) {
            $ns = Configuration::ROOT_KEY;
            $resources = $this->container->getParameter($ns.'.resources');

            foreach ($resources as $resource) {
                if ($resource['name'] === $apiRes->resourceType) {
                    if (isset($resource['type'], $this->resources[$resource['type']])) {
                        $this->resource = $this->resources[$resource['type']];
                        $this->apiConfig = $resource;
                        $this->apiModel = $apiRes;

                        return true;
                    }
                }
            }
        }

        return false;
    }
}
