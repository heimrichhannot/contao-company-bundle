<?php

$dca = &$GLOBALS['TL_DCA']['tl_user'];

/**
 * Palettes
 */
$dca['palettes']['extend'] = str_replace('fop;', 'fop;{company_legend},companys,companyp;', (string) $dca['palettes']['extend']);
$dca['palettes']['custom'] = str_replace('fop;', 'fop;{company_legend},companys,companyp;', (string) $dca['palettes']['custom']);

foreach (array_keys($dca['palettes']) as $strPalette) {
    if (is_array($dca['palettes'][$strPalette])) {
        continue;
    }

    $dca['palettes'][$strPalette] = str_replace('email', 'email,userCompanies', (string) $dca['palettes'][$strPalette]);
}

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

$dca['fields']['userCompanies'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_user']['userCompanies'],
    'filter'     => true,
    'exclude'    => true,
    'inputType'  => 'select',
    'foreignKey' => 'tl_company.title',
    'relation'   => ['type' => 'belongsToMany', 'load' => 'eager'],
    'eval'       => ['tl_class' => 'clr w100', 'chosen' => true, 'includeBlankOption' => true, 'multiple' => true, 'style' => 'width: 100%'],
    'sql'        => "blob NULL"
];
