<?php

declare(strict_types=1);

/*
 * This file is part of Contao Api Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-api-bundle
 */

namespace Markocupic\ContaoApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MarkocupicContaoApiExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return Configuration::ROOT_KEY;
    }

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );

        $loader->load('services.yaml');

        $rootKey = $this->getAlias();

        $container->setParameter($rootKey.'.enabled', $config['enabled']);
    }
}
