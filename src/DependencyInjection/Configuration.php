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

namespace Markocupic\ContaoContentApi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROOT_KEY = 'markocupic_contao_content_api';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::ROOT_KEY);

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->arrayNode('resources')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->cannotBeEmpty()->end()
                            ->scalarNode('type')->cannotBeEmpty()->end()
                            ->scalarNode('model_class')->cannotBeEmpty()->end()
                            ->scalarNode('verbose_name')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
