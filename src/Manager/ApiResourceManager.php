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

use Markocupic\ContaoContentApi\Api\ApiInterface;
use Markocupic\ContaoContentApi\DependencyInjection\Configuration;
use Markocupic\ContaoContentApi\Model\AppModel;
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
    private $resConfig;

    /**
     * @var AppModel
     */
    private $appModel;

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

    public function get(string $strAlias): ?self
    {
        if (null !== ($this->appModel = AppModel::findOneByAlias($strAlias))) {
            if (null !== ($this->resConfig = $this->getResConfigFromAlias($strAlias))) {
                $this->resource = $this->resources[$this->resConfig['type']];

                return $this;
            }
        }

        return null;
    }

    public function show()
    {
        return $this->resource->get($this);
    }

    /**
     * Return data current resource in config.yml.
     */
    public function getApiConfig(): ?array
    {
        return $this->resConfig;
    }

    public function getAppModel(): ?AppModel
    {
        return $this->appModel;
    }

    public function getFrontendUser(): ?ContaoFrontendUser
    {
        return $this->user;
    }

    private function getResConfigFromAlias(string $strAlias): ?array
    {
        $apiRes = AppModel::findOneByAlias($strAlias);

        if (null !== $apiRes) {
            $ns = Configuration::ROOT_KEY;
            $resources = $this->container->getParameter($ns.'.resources');

            foreach ($resources as $resource) {
                if ($resource['name'] === $apiRes->resourceType) {
                    if (isset($resource['type'], $this->resources[$resource['type']])) {
                        return $resource;
                    }
                }
            }
        }

        return null;
    }
}
