<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocuic\ContaoContentApi;

use Contao\DataContainer;
use Contao\DC_Table;
use Markocupic\ContaoContentApi\Api\ApiContaoEntity;
use Markocupic\ContaoContentApi\Api\ApiContaoFrontendModule;

$GLOBALS['TL_DCA']['tl_api_app'] = [
    'config'      => [
        'dataContainer'    => DC_Table::class,
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'        => [
        'sorting'           => [
            'mode'        => DataContainer::MODE_SORTABLE,
            'fields'      => ['title'],
            'flag'        => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label'             => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ],
            'copy'   => [
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ],
            'delete' => [
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'href'       => 'act=show',
                'icon'       => 'show.gif',
                'attributes' => 'style="margin-right:3px"',
            ],
        ],
    ],
    'palettes'    => [
        '__selector__'                => ['mProtect'],
        'default'                     => '{title_legend},title,resourceType',
        'contao_frontend_module' => '{title_legend},title,resourceType,resourceAlias;{resource_legend},allowedModules;{security_legend},key,mProtect',
        'contao_entity'         => '{title_legend},title,resourceType,resourceAlias;{security_legend},key,mProtect',
        //'contao_content_element' => '{title_legend},title,resourceType;{resource_legend},allowedContentElements;{security_legend},key,mProtect',
        //'contao_logged_in_frontend_user' => '{title_legend},title,resourceType;{resource_legend};{security_legend},key',
    ],
    'subpalettes' => [
        'mProtect' => 'mGroups',
    ],
    'fields'      => [
        'id'                     => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
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
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'resourceType'           => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'allowedModules'         => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'includeBlankOption' => true, 'multiple' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'       => 'blob NULL',
        ],
        'allowedContentElements' => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'includeBlankOption' => true, 'multiple' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'       => 'blob NULL',
        ],
        'resourceAlias'        => [
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => ['mandatory' => true, 'includeBlankOption' => true, 'multiple' => false, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'       => 'blob NULL',
        ],
        'key'                    => [
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'tl_class' => 'clr long', 'unique' => true],
            'sql'       => "varchar(255) NOT NULL default ''",
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
            'eval'       => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'w50'],
            'sql'        => 'blob NULL',
            'relation'   => ['type' => 'hasMany', 'load' => 'lazy'],
        ],
    ],
];
