<?php

use HeimrichHannot\CompanyBundle\DataContainer\CompanyContainer;
use HeimrichHannot\UtilsBundle\Dca\DateAddedField;

DateAddedField::register('tl_company');

$GLOBALS['TL_DCA']['tl_company'] = [
    'config'      => [
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_company_archive',
        'enableVersioning'  => true,
        'sql'               => [
            'keys' => [
                'id'                       => 'primary',
                'pid,start,stop,published' => 'index'
            ]
        ]
    ],
    'list'        => [
        'label'             => [
            'fields' => ['title'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'                  => 2,
            'fields'                => ['title'],
            'headerFields'          => ['title'],
            'panelLayout'           => 'filter;sort,search,limit',
            'child_record_callback' => [CompanyContainer::class, 'listChildren']
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
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_company']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.svg'
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_company']['copy'],
                'href'  => 'act=paste&amp;mode=copy',
                'icon'  => 'copy.svg'
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_company']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"'
            ],
            'toggle' => [
                'href'            => 'act=toggle&amp;field=published',
                'icon'            => 'visible.svg',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_company']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.svg'
            ],
        ]
    ],
    'palettes'    => [
        '__selector__' => ['addLogo', 'addMemberContacts', 'addUserContacts', 'addMemberEditors', 'addUserEditors', 'published'],
        'default'      => '{general_legend},title,addLogo;{address_legend},street,street2,postal,city,state,country,coordinates;{contact_legend},phone,fax,email,website;{contact_person_legend},addContacts,addMemberContacts,addUserContacts;{editor_legend},addUserEditors,addMemberEditors;{publish_legend},published;'
    ],
    'subpalettes' => [
        'addLogo'           => 'logo',
        'addMemberContacts' => 'memberContacts',
        'addUserContacts'   => 'userContacts',
        'addMemberEditors'  => 'memberEditors',
        'addUserEditors'    => 'userEditors',
        'published'         => 'start,stop'
    ],
    'fields'      => [
        'id'                => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'pid'               => [
            'foreignKey' => 'tl_company_archive.title',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => ['type' => 'belongsTo', 'load' => 'eager']
        ],
        'tstamp'            => [
            'label' => &$GLOBALS['TL_LANG']['tl_company']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'title'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['title'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'addLogo'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['addLogo'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'logo'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['logo'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => Config::get('validImageTypes'), 'mandatory' => true],
            'sql'       => "binary(16) NULL"
        ],
        'addMemberEditors'  => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'memberEditors'     => [
            'inputType'        => 'picker',
            'eval' => [
                'multiple' => true,
                'mandatory' => true,
            ],
            'relation' => [
                'type' => 'hasMany',
                'load' => 'lazy',
                'table' => 'tl_member',
            ],
            'sql' => [
                'type' => 'blob',
                'notnull' => false,
            ],
        ],
        'addUserEditors'    => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'userEditors'       => [
            'inputType'        => 'picker',
            'eval' => [
                'multiple' => true,
                'mandatory' => true,
            ],
            'relation' => [
                'type' => 'hasMany',
                'load' => 'lazy',
                'table' => 'tl_user',
            ],
            'sql' => [
                'type' => 'blob',
                'notnull' => false,
            ],
        ],
        'addMemberContacts' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['addMemberContacts'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'memberContacts'    => [
            'inputType'        => 'picker',
            'eval' => [
                'multiple' => true,
                'mandatory' => true,
            ],
            'relation' => [
                'type' => 'hasMany',
                'load' => 'lazy',
                'table' => 'tl_member',
            ],
            'sql' => [
                'type' => 'blob',
                'notnull' => false,
            ],
        ],
        'addUserContacts'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['addUserContacts'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'userContacts'      => [
            'inputType'        => 'picker',
            'eval' => [
                'multiple' => true,
                'mandatory' => true,
            ],
            'relation' => [
                'type' => 'hasMany',
                'load' => 'lazy',
                'table' => 'tl_user',
            ],
            'sql' => [
                'type' => 'blob',
                'notnull' => false,
            ],
        ],
        'street'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['street'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'street2'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['street2'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'postal'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['postal'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'city'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['city'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'state'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['state'],
            'exclude'   => true,
            'filter'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50', 'chosen' => true],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'country'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['country'],
            'exclude'   => true,
            'filter'    => true,
            'sorting'   => true,
            'inputType' => 'select',
            'options'   => System::getCountries(),
            'eval'      => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "varchar(2) NOT NULL default ''"
        ],
        'coordinates'       => [
            'label'         => &$GLOBALS['TL_LANG']['tl_company']['coordinates'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => [
                function ($value, \Contao\DataContainer $dc) {
                    if ($value) {
                        return $value;
                    }

                    $coordinates = System::getContainer()->get('huh.utils.location')->computeCoordinatesByArray([
                        'street'  => $dc->activeRecord->street,
                        'postal'  => $dc->activeRecord->postal,
                        'city'    => $dc->activeRecord->city,
                        'country' => $dc->activeRecord->country ? $GLOBALS['TL_LANG']['COUNTRIES'][$dc->activeRecord->country] : '',
                    ]);

                    if (isset($coordinates['lat']) && isset($coordinates['lng'])) {
                        return $coordinates['lat'] . ',' . $coordinates['lng'];
                    }

                    return $value;
                }
            ],
            'eval'          => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'           => "varchar(255) NOT NULL default ''"
        ],
        'phone'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['phone'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'fax'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['fax'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'email'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['email'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'website'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['website'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'published'         => [
            'exclude'   => true,
            'toggle'    => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['doNotCopy' => true, 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'start'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['start'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''"
        ],
        'stop'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['stop'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''"
        ]
    ]
];

System::getContainer()->get('huh.utils.dca')->addAliasToDca('tl_company', function($value, \Contao\DataContainer $dc) {
    return System::getContainer()->get('huh.utils.dca')->generateAlias($value, $dc->id, 'tl_company', $dc->activeRecord->title);
}, 'title');
