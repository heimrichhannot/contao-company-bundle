<?php

$GLOBALS['TL_DCA']['tl_company_archive'] = [
    'config'      => [
        'dataContainer'     => 'Table',
        'ctable'            => ['tl_company'],
        'switchToEdit'      => true,
        'enableVersioning'  => true,
        'onload_callback'   => [
            ['huh.company.listener.company_archive_callback', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['huh.utils.dca', 'setDateAdded'],
        ],
        'oncopy_callback'   => [
            ['huh.utils.dca', 'setDateAddedOnCopy'],
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
                'button_callback' => ['huh.company.listener.company_archive_callback', 'editHeader']
            ],
            'copy'       => [
                'label'           => &$GLOBALS['TL_LANG']['tl_company_archive']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.gif',
                'button_callback' => ['huh.company.listener.company_archive_callback', 'copyArchive']
            ],
            'delete'     => [
                'label'           => &$GLOBALS['TL_LANG']['tl_company_archive']['copy'],
                'href'            => 'act=delete',
                'icon'            => 'delete.gif',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                'button_callback' => ['huh.company.listener.company_archive_callback', 'deleteArchive']
            ],
            'show'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_company_archive']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            ],
            'toggle'     => [
                'label' => &$GLOBALS['TL_LANG']['tl_company_archive']['toggle'],
                'href'  => 'act=toggle',
                'icon'  => 'toggle.gif'
            ],
        ]
    ],
    'palettes'    => [
        '__selector__' => ['published'],
        'default'      => '{general_legend},title;{publish_legend},published;'
    ],
    'subpalettes' => [
        'published' => 'start,stop'
    ],
    'fields'      => [
        'id'        => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp'    => [
            'label' => &$GLOBALS['TL_LANG']['tl_company_archive']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'dateAdded' => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'"
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
        ],
        'published' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company_archive']['published'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['doNotCopy' => true, 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'start'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company_archive']['start'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''"
        ],
        'stop'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company_archive']['stop'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''"
        ]
    ]
];