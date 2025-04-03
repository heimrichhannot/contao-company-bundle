<?php

use HeimrichHannot\UtilsBundle\Dca\DateAddedField;

DateAddedField::register('tl_company_archive');

$GLOBALS['TL_DCA']['tl_company_archive'] = [
    'config'      => [
        'dataContainer'     => \Contao\DC_Table::class,
        'ctable'            => ['tl_company'],
        'switchToEdit'      => true,
        'enableVersioning'  => true,
        'onload_callback'   => [
            [\HeimrichHannot\CompanyBundle\DataContainer\CompanyArchiveContainer::class, 'checkPermission'],
        ],
        'sql'               => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],
    'list'        => [
        'label'             => [
            'fields' => ['title'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'         => 1,
            'fields'       => ['title'],
            'headerFields' => ['title'],
            'panelLayout'  => 'filter;search,limit'
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ],
        ],
        'operations'        => [
            'edit'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_company_archive']['edit'],
                'href'  => 'table=tl_company',
                'icon'  => 'edit.gif'
            ],
            'editheader' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_company_archive']['editheader'],
                'href'            => 'act=edit',
                'icon'            => 'header.gif',
                'button_callback' => [\HeimrichHannot\CompanyBundle\DataContainer\CompanyArchiveContainer::class, 'editHeader']
            ],
            'copy'       => [
                'label'           => &$GLOBALS['TL_LANG']['tl_company_archive']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.gif',
                'button_callback' => [\HeimrichHannot\CompanyBundle\DataContainer\CompanyArchiveContainer::class, 'copyArchive']
            ],
            'delete'     => [
                'label'           => &$GLOBALS['TL_LANG']['tl_company_archive']['copy'],
                'href'            => 'act=delete',
                'icon'            => 'delete.gif',
                'attributes'      => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"',
                'button_callback' => [\HeimrichHannot\CompanyBundle\DataContainer\CompanyArchiveContainer::class, 'deleteArchive']
            ],
            'show'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_company_archive']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            ],
        ]
    ],
    'palettes'    => [
        'default'      => '{general_legend},title;'
    ],
    'fields'      => [
        'id'        => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp'    => [
            'label' => &$GLOBALS['TL_LANG']['tl_company_archive']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'title'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company_archive']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ]
    ]
];
