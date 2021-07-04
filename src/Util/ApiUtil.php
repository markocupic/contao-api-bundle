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

namespace Markocupic\ContaoContentApi\Util;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\System;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ApiUtil implements FrameworkAwareInterface, ContainerAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    public function getResourceConfigByName(string $resourceName): ?array
    {
        $resources = System::getContainer()->getParameter('markocupic_contao_content_api');

        if (!isset($resources['resources'])) {
            return null;
        }

        foreach ($resources['resources'] as $resource) {
            if ($resource['name'] === $resourceName) {
                return $resource;
            }
        }

        return null;
    }

    /*
     * public function getResourceConfigByModelClass(string $modelClass)
     * {
     * $resources = System::getContainer()->getParameter('markocupic_contao_content_api');
     *
     * if (!isset($resources['api']['resources'])) {
     * return false;
     * }
     *
     * foreach ($resources['api']['resources'] as $resource) {
     * if ($resource['modelClass'] === $modelClass) {
     * return $resource;
     * }
     * }
     *
     * return false;
     * }
     *
     * public function getResourceFieldOptions(string $resourceName)
     * {
     * $resourceConfig = $this->container->get('huh.api.util.api_util')->getResourceConfigByName($resourceName);
     *
     * if (!\is_array($resourceConfig) || !class_exists($resourceConfig['modelClass'])) {
     * return [];
     * }
     *
     * return $this->container->get('huh.utils.choice.field')->getCachedChoices([
     * 'dataContainer' => $resourceConfig['modelClass']::getTable(),
     * ]);
     * }
     *
     * public function getEntityTableByApp(ApiAppModel $app)
     * {
     * $config = $this->getResourceConfigByName($app->resource);
     *
     * if (!isset($config['modelClass']) || !class_exists($config['modelClass'])) {
     * return false;
     * }
     *
     * return $config['modelClass']::getTable();
     * }
     */
}
