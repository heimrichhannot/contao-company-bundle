<?php

/**
 * Backend modules.
 */

use HeimrichHannot\CompanyBundle\Model\CompanyArchiveModel;
use HeimrichHannot\CompanyBundle\Model\CompanyModel;

$GLOBALS['BE_MOD']['accounts']['company'] = [
    'tables' => ['tl_company_archive', 'tl_company'],
];

/*
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'companys';
$GLOBALS['TL_PERMISSIONS'][] = 'companyp';

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_company'] = CompanyModel::class;
$GLOBALS['TL_MODELS']['tl_company_archive'] = CompanyArchiveModel::class;
