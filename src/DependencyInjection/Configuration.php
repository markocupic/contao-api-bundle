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

namespace Markocupic\ContaoContentApi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('markocupic');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('contao_content_api')
                    ->children()
                        ->arrayNode('resources')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('name')->cannotBeEmpty()->end()
                                    ->scalarNode('type')->cannotBeEmpty()->end()
                                    ->scalarNode('modelClass')->cannotBeEmpty()->end()
                                    ->scalarNode('verboseName')->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
