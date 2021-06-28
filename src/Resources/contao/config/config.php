<?php

/*
 * This file is part of Contao Content Api.
 * 
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi;

$GLOBALS['TL_HOOKS']['apiModuleGenerated'][] = [Hooks::class, 'apiModuleGenerated'];
