<?php

$GLOBALS['TL_DCA']['tl_company'] = [
    'config'      => [
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_company_archive',
        'enableVersioning'  => true,
        'onload_callback'   => [
            [\HeimrichHannot\CompanyBundle\DataContainer\CompanyContainer::class, 'checkPermission'],
            [\HeimrichHannot\CompanyBundle\DataContainer\CompanyContainer::class, 'initPalette'],
        ],
        'onsubmit_callback' => [
            ['huh.utils.dca', 'setDateAdded'],
        ],
        'oncopy_callback'   => [
            ['huh.utils.dca', 'setDateAddedOnCopy'],
        ],
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
            'child_record_callback' => [\HeimrichHannot\CompanyBundle\DataContainer\CompanyContainer::class, 'listChildren']
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
                'icon'  => 'edit.gif'
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_company']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif'
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_company']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"'
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_company']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => [\HeimrichHannot\CompanyBundle\DataContainer\CompanyContainer::class, 'toggleIcon']
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_company']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
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
        'dateAdded'         => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'"
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
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['addMemberEditors'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'memberEditors'     => [
            'label'            => &$GLOBALS['TL_LANG']['tl_company']['memberEditors'],
            'inputType'        => 'select',
            'options_callback' => function (\Contao\DataContainer $dc) {
                return System::getContainer()->get('huh.utils.choice.model_instance')->getCachedChoices([
                    'dataContainer' => 'tl_member'
                ]);
            },
            'eval'             => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'              => "blob NULL"
        ],
        'addUserEditors'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['addUserEditors'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'userEditors'       => [
            'label'            => &$GLOBALS['TL_LANG']['tl_company']['userEditors'],
            'inputType'        => 'select',
            'options_callback' => function (\Contao\DataContainer $dc) {
                return System::getContainer()->get('huh.utils.choice.model_instance')->getCachedChoices([
                    'dataContainer' => 'tl_user',
                    'labelPattern'  => '%name% (ID %id%)'
                ]);
            },
            'eval'             => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50 clr', 'mandatory' => true],
            'sql'              => "blob NULL"
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
            'label'            => &$GLOBALS['TL_LANG']['tl_company']['memberContacts'],
            'inputType'        => 'select',
            'options_callback' => function (\Contao\DataContainer $dc) {
                return System::getContainer()->get('huh.utils.choice.model_instance')->getCachedChoices([
                    'dataContainer' => 'tl_member',
                ]);
            },
            'eval'             => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'              => "blob NULL"
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
            'label'            => &$GLOBALS['TL_LANG']['tl_company']['userContacts'],
            'inputType'        => 'select',
            'options_callback' => function (\Contao\DataContainer $dc) {
                return System::getContainer()->get('huh.utils.choice.model_instance')->getCachedChoices([
                    'dataContainer' => 'tl_user',
                    'labelPattern'  => '%name% (ID %id%)'
                ]);
            },
            'eval'             => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50 clr', 'mandatory' => true],
            'sql'              => "blob NULL"
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
            'label'     => &$GLOBALS['TL_LANG']['tl_company']['published'],
            'exclude'   => true,
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
