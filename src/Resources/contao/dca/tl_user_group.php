<?php

$dca = &$GLOBALS['TL_DCA']['tl_user_group'];

/**
 * Palettes
 */
$dca['palettes']['default'] = str_replace('fop;', 'fop;{company_legend},companys,companyp;', $dca['palettes']['default']);

/**
 * Fields
 */
$dca['fields']['companys'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_user']['companys'],
    'exclude'    => true,
    'inputType'  => 'checkbox',
    'foreignKey' => 'tl_company_archive.title',
    'eval'       => ['multiple' => true],
    'sql'        => "blob NULL"
];

$dca['fields']['companyp'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['companyp'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'options'   => ['create', 'delete'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval'      => ['multiple' => true],
    'sql'       => "blob NULL"
];