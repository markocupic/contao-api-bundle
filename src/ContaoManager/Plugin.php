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

namespace Markocupic\ContaoApiBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Markocupic\ContaoApiBundle\MarkocupicContaoApiBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(MarkocupicContaoApiBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }

	public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
	{
		return $resolver
			->resolve(__DIR__.'/../Controller')
			->load(__DIR__.'/../Controller')
			;
	}
}
