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

namespace Markocupic\ContaoApiBundle\DependencyInjection\Compiler;

use Markocupic\ContaoApiBundle\Manager\ApiResourceManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

class TaggedApiResourcePass implements CompilerPassInterface
{
    /**
     * @throws ServiceNotFoundException
     */
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(ApiResourceManager::class);

        $taggedServices = $container->findTaggedServiceIds('markocupic_contao_api.resource', true);

        $mandatoryKeys = ['alias', 'type'];

        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($mandatoryKeys as $key) {
                if (!isset($tags[0][$key])) {
                    throw new InvalidArgumentException(sprintf('Missing tag information "%s" on markocupic_contao_api.resource tagged service "%s".', $key, $serviceId));
                }
            }

            $definition->addMethodCall(
                'add',
                [
                    new Reference($serviceId),
                    $serviceId,
                    $tags[0]['alias'],
                    $tags[0]['type'],
                    $tags[0]['modelClass'],
                    $tags[0]['verboseName'],
                ],
            );
        }
    }
}
