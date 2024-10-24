<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use HeimrichHannot\CompanyBundle\Security\CompanyPermission;

$dca = &$GLOBALS['TL_DCA']['tl_user_group'];

/**
 * Palettes
 */
PaletteManipulator::create()
    ->addLegend('company_legend', 'amg_legend')
    ->addField(['companys', 'companyp'], 'company_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group');

/**
 * Fields
 */

CompanyPermission::accessRightFields($dca);