<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi;

use Markocupic\ContaoContentApi\DependencyInjection\Compiler\ApiResourcePass;
use Markocupic\ContaoContentApi\DependencyInjection\MarkocupicContaoContentApiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MarkocupicContaoContentApi.
 */
class MarkocupicContaoContentApi extends Bundle
{
    public function getContainerExtension(): MarkocupicContaoContentApiExtension
    {
        return new MarkocupicContaoContentApiExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ApiResourcePass());
    }
}
