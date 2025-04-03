<?php

namespace HeimrichHannot\CompanyBundle\Security;

use Contao\Controller;

class CompanyPermission
{
    public static function accessRightFields(array &$dca): void
    {
        $dca['fields']['companys'] = [
            'label' => &$GLOBALS['TL_LANG']['tl_user']['companys'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'foreignKey' => 'tl_company_archive.title',
            'eval' => [
                'multiple' => true,
            ],
            'sql' => 'blob NULL',
        ];

        $dca['fields']['companyp'] = [
            'label' => &$GLOBALS['TL_LANG']['tl_user']['companyp'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'options' => ['create', 'delete'],
            'reference' => &$GLOBALS['TL_LANG']['tl_user']['companyp'],
            'eval' => [
                'multiple' => true,
            ],
            'sql' => 'blob NULL',
        ];

        $dca['onload_callback'][] = static function () {
            Controller::loadLanguageFile('tl_user');
        };
    }
}
