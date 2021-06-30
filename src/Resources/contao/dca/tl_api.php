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

use Contao\Backend;
use Contao\DC_Table;
use Contao\Input;
use Contao\System;
use Markocupic\ContaoContentApi\DependencyInjection\Configuration;

/**
 * Table tl_api
 */
$GLOBALS['TL_DCA']['tl_api'] = array(
	// Config
	'config'      => array(
		'dataContainer'    => 'Table',
		'enableVersioning' => true,
		'sql'              => array(
			'keys' => array(
				'id' => 'primary'
			)
		),
		'onload_callback'  => array(
			array('tl_api', 'setPalette'),
		)
	),
	'edit'        => array(
		'buttons_callback' => array(
			array('tl_api', 'buttonsCallback')
		)
	),
	'list'        => array(
		'sorting'           => array(
			'mode'        => 2,
			'fields'      => array('title'),
			'flag'        => 1,
			'panelLayout' => 'filter;sort,search,limit'
		),
		'label'             => array(
			'fields' => array('title'),
			'format' => '%s',
		),
		'global_operations' => array(
			'all' => array(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations'        => array(
			'edit'   => array(
				'label' => &$GLOBALS['TL_LANG']['tl_api']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			),
			'copy'   => array(
				'label' => &$GLOBALS['TL_LANG']['tl_api']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif'
			),
			'delete' => array(
				'label'      => &$GLOBALS['TL_LANG']['tl_api']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show'   => array(
				'label'      => &$GLOBALS['TL_LANG']['tl_api']['show'],
				'href'       => 'act=show',
				'icon'       => 'show.gif',
				'attributes' => 'style="margin-right:3px"'
			),
		)
	),
	// Palettes
	'palettes'    => array(
		'__selector__'                      => array('addSubpalette'),
		'default'                           => '{first_legend},title,resourceType',
		'contao_frontend_module'            => '{first_legend},title,resourceType;{resource_settings},alias,allowedModules',
		'contao_logged_in_frontend_user'    => '{first_legend},title,resourceType;{resource_settings},alias',
	),

	// Subpalettes
	'subpalettes' => array(
		'addSubpalette' => 'textareaField',
	),
	// Fields
	'fields'      => array(
		'id'             => array(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp'         => array(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'title'         => array(
			'inputType' => 'text',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'resourceType'  => array(
			'inputType' => 'select',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'options_callback' => array('tl_api', 'getResourceTypes'),
			'eval'      => array('includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'alias'         => array(
			'inputType' => 'text',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'eval'      => array('mandatory' => true, 'unique' => true, 'rgxp' => 'custom', 'customRgxp' => '/^[a-zA-Z1-9-_]+$/', 'maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'allowedModules' => array(
			'inputType' => 'select',
			'exclude'   => true,
			'search'    => true,
			'filter'    => true,
			'sorting'   => true,
			'options_callback' => array('tl_api', 'getFrontendModules'),
			'eval'      => array('includeBlankOption' => true, 'multiple' => true, 'chosen' => true, 'tl_class' => 'w50'),
			'sql'       => "blob NULL",
		)
	)
);

/**
 * Class tl_api
 */
class tl_api extends Backend
{
	/**
	 * @param $arrButtons
	 * @param  DC_Table $dc
	 * @return mixed
	 */
	public function buttonsCallback($arrButtons, DC_Table $dc)
	{
		if (Input::get('act') === 'edit')
		{
			$arrButtons['customButton'] = '<button type="submit" name="customButton" id="customButton" class="tl_submit customButton" accesskey="x">' . $GLOBALS['TL_LANG']['tl_api']['customButton'] . '</button>';
		}

		return $arrButtons;
	}

	/**
	 * @return array
	 */
	public function getResourceTypes(): array
	{
		$opt = array();
		$rootKey = Configuration::ROOT_KEY;
		$resources = System::getContainer()->getParameter($rootKey . '.resources');

		foreach ($resources as $resource)
		{
			$opt[$resource['name']] = $resource['name'];
		}

		return $opt;
	}

	/**
	 * @return array
	 */
	public function getFrontendModules(): array
	{
		$opt = array();
		$objDb = $this->Database->execute('SELECT * FROM tl_module ORDER BY name');

		while ($objDb->next())
		{
			$opt[$objDb->id] = $objDb->name;
		}

		return $opt;
	}

	/**
	 * Handle the profile page.
	 *
	 * @param DataContainer $dc
	 */
	public function setPalette(DataContainer $dc)
	{
		$id = $dc->id;

		if (!empty($id) && $id>0)
		{
			$objDb = $this->Database->prepare('SELECT * FROM ' . $dc->table . ' WHERE id = ?')->execute($id);

			if ($objDb->numRows)
			{
				if ($objDb->resourceType !== '')
				{
					$GLOBALS['TL_DCA'][$dc->table]['palettes']['default'] = $GLOBALS['TL_DCA'][$dc->table]['palettes'][$objDb->resourceType];
				}
			}
		}
	}
}
