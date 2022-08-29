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

/**
 * Table tl_api_app
 */
$GLOBALS['TL_DCA']['tl_api_app'] = [
    // Config
    'config'      => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'onload_callback'  => [
            ['tl_api_app', 'setPalette'],
        ],
    ],
    'edit'        => [
        'buttons_callback' => [
            ['tl_api_app', 'buttonsCallback'],
        ],
    ],
    'list'        => [
        'sorting'           => [
            'mode'        => 2,
            'fields'      => ['title'],
            'flag'        => 1,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label'             => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_api_app']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_api_app']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_api_app']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label'      => &$GLOBALS['TL_LANG']['tl_api_app']['show'],
                'href'       => 'act=show',
                'icon'       => 'show.gif',
                'attributes' => 'style="margin-right:3px"',
            ],
        ],
    ],
    // Palettes
    'palettes'    => [
        '__selector__'                   => ['mProtect'],
        'default'                        => '{first_legend},title,resourceType',
        'contao_frontend_module'         => '{first_legend},title,resourceType;{resource_settings},allowedModules;{security_settings},key,mProtect',
        'contao_content_element'         => '{first_legend},title,resourceType;{resource_settings},allowedContentElements;{security_settings},key,mProtect',
        'contao_logged_in_frontend_user' => '{first_legend},title,resourceType;{resource_settings};{security_settings},key',
    ],

    // Subpalettes
    'subpalettes' => [
        'mProtect' => 'mGroups',
    ],
    // Fields
    'fields'      => [
        'id'                     => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'tstamp'                 => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'                  => [
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'resourceType'           => [
            'inputType'        => 'select',
            'exclude'          => true,
            'search'           => true,
            'filter'           => true,
            'sorting'          => true,
            'options_callback' => ['tl_api_app', 'getResourceTypes'],
            'eval'             => ['includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'              => "varchar(255) NOT NULL default ''",
        ],
        'allowedModules'         => [
            'inputType'        => 'select',
            'exclude'          => true,
            'search'           => true,
            'filter'           => true,
            'sorting'          => true,
            'options_callback' => ['tl_api_app', 'getFrontendModules'],
            'eval'             => ['includeBlankOption' => true, 'multiple' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'              => "blob NULL",
        ],
        'allowedContentElements' => [
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'key'                    => [
            'search'        => true,
            'inputType'     => 'text',
            'load_callback' => [
                ['markocupic_contao_content_api.backend.api_app', 'generateApiToken'],
            ],
            'eval'          => ['tl_class' => 'clr long', 'unique' => true],
            'sql'           => "varchar(255) NOT NULL default ''",
        ],
        'mProtect'               => [
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'mGroups'                => [
            'exclude'    => true,
            'inputType'  => 'checkbox',
            'foreignKey' => 'tl_member_group.name',
            'eval'       => ['multiple' => true, 'tl_class' => 'w50'],
            'sql'        => "blob NULL",
            'relation'   => ['type' => 'hasMany', 'load' => 'lazy'],
        ],
    ],
];

/**
 * Class tl_api_app
 */
class tl_api_app extends Backend
{
    /**
     * @param $arrButtons
     * @param DC_Table $dc
     * @return mixed
     */
    public function buttonsCallback($arrButtons, DC_Table $dc)
    {
        if (Input::get('act') === 'edit') {
            $arrButtons['customButton'] = '<button type="submit" name="customButton" id="customButton" class="tl_submit customButton" accesskey="x">'.$GLOBALS['TL_LANG']['tl_api_app']['customButton'].'</button>';
        }

        return $arrButtons;
    }

    /**
     * @return array
     */
    public function getResourceTypes(): array
    {
        $opt = [];
        $resources = System::getContainer()->getParameter('markocupic_contao_content_api');

        if (!isset($resources['resources'])) {
            throw new Exception('No resources set in markocupic_contao_content_api');
        }

        foreach ($resources['resources'] as $resource) {
            $opt[$resource['name']] = $resource['name'];
        }

        return $opt;
    }

    /**
     * @return array
     */
    public function getFrontendModules(): array
    {
        $opt = [];
        $objDb = $this->Database->execute('SELECT * FROM tl_module ORDER BY name');

        while ($objDb->next()) {
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

        if (!empty($id) && $id > 0) {
            $objDb = $this->Database->prepare('SELECT * FROM '.$dc->table.' WHERE id = ?')->execute($id);

            if ($objDb->numRows) {
                if ($objDb->resourceType !== '') {
                    $GLOBALS['TL_DCA'][$dc->table]['palettes']['default'] = $GLOBALS['TL_DCA'][$dc->table]['palettes'][$objDb->resourceType];
                }
            }
        }
    }
}
