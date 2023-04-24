<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-api-bundle
 */

namespace Markocupic\ContaoApiBundle;

use Markocupic\ContaoApiBundle\Model\ApiAppModel;

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
