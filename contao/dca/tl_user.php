<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use HeimrichHannot\CompanyBundle\Security\CompanyPermission;

$dca = &$GLOBALS['TL_DCA']['tl_user'];

/*
 * Palettes
 */
PaletteManipulator::create()
    ->addLegend('company_legend', 'amg_legend')
    ->addField(['companys', 'companyp'], 'company_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('extend', 'tl_user')
    ->applyToPalette('custom', 'tl_user');

foreach (array_keys($dca['palettes']) as $strPalette) {
    if (is_array($dca['palettes'][$strPalette])) {
        continue;
    }

    $dca['palettes'][$strPalette] = str_replace('email', 'email,userCompanies', (string) $dca['palettes'][$strPalette]);
}

/*
 * Fields
 */
CompanyPermission::accessRightFields($dca);

$dca['fields']['userCompanies'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user']['userCompanies'],
    'filter' => true,
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_company.title',
    'relation' => [
        'type' => 'belongsToMany',
        'load' => 'eager',
    ],
    'eval' => [
        'tl_class' => 'clr w100',
        'chosen' => true,
        'includeBlankOption' => true,
        'multiple' => true,
        'style' => 'width: 100%',
    ],
    'sql' => 'blob NULL',
];
