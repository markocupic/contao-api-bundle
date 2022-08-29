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

use Markocupic\ContaoContentApi\Model\ApiAppModel;

/*
 * Backend modules
 */
$GLOBALS['BE_MOD']['api']['api_item'] = [
    'tables' => ['tl_api_app'],
];

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_api_app'] = ApiAppModel::class;
