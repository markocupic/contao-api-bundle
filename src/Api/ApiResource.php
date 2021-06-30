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

namespace Markocupic\ContaoContentApi\Api;

use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\ContaoContentApi\DependencyInjection\Configuration;
use Markocupic\ContaoContentApi\Model\ApiModel;
use Markocupic\ContaoContentApi\User\Contao\ContaoFrontendUser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiResource
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var ContaoFramework
     */
    public $framework;

    /**
     * @var ContaoFrontendUser
     */
    public $user;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var array
     */
    private $data;

    /**
     * @var ApiModel
     */
    private $apiModel;

    public function __construct(ContainerInterface $container, ContaoFramework $framework, ContaoFrontendUser $user)
    {
        $this->container = $container;
        $this->framework = $framework;
        $this->user = $user;
    }

    public function get(string $strAlias, Request $request)
    {
        $this->request = $request;

        if ($this->setResourceFromAlias($strAlias)) {
            $resType = $this->getResourceTypeFromAlias($strAlias);

            if (null !== $resType && isset($resType['class'])) {
                $strClass = $resType['class'];

                if (class_exists($strClass)) {
                    return new $strClass($this);
                }
            }
        }
    }

    public function getData(): ?array
    {
        return $this->data;
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
                    $this->data = $resource;
                    $this->apiModel = $apiRes;

                    return true;
                }
            }
        }

        return false;
    }

    private function getResourceTypeFromAlias(string $strAlias): ?array
    {
        $this->setResourceFromAlias($strAlias);
        $resource = $this->getData();

        if (null !== $resource && isset($resource['type'])) {
            $ns = Configuration::ROOT_KEY;

            $resourceTypes = $this->container->getParameter($ns.'.resource_types');

            foreach ($resourceTypes as $resourceType) {
                if ($resourceType['name'] === $resource['type']) {
                    return $resourceType;
                }
            }
        }

        return null;
    }
}
