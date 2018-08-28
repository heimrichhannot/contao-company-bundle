<?php

/**
* Backend modules
*/
$GLOBALS['BE_MOD']['accounts']['company'] = [
    'tables' => ['tl_company_archive', 'tl_company'],
];

/**
* Permissions
*/
$GLOBALS['TL_PERMISSIONS'][] = 'companys';
$GLOBALS['TL_PERMISSIONS'][] = 'companyp';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_company']         = 'HeimrichHannot\CompanyBundle\Model\CompanyModel';
$GLOBALS['TL_MODELS']['tl_company_archive'] = 'HeimrichHannot\CompanyBundle\Model\CompanyArchiveModel';