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

use Markocupic\ContaoContentApi\Model\AppModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['api']['api_item'] = array(
	'tables' => array('tl_app')
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_app'] = AppModel::class;
