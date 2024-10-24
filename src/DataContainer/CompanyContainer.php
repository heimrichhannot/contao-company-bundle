<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\CompanyBundle\DataContainer;

use Contao\BackendUser;
use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Database;
use Contao\DataContainer;
use Contao\Date;
use Contao\Input;
use Contao\System;

class CompanyContainer
{
    #[AsCallback(table: 'tl_company', target: 'config.onload')]
    public function onConfigOnloadCallback(DataContainer $dc = null): void
    {
        $this->initPalette();
        $this->checkPermission();
    }


    public function initPalette()
    {
        $company = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_company', \Input::get('id'));

        if (!$company) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_company'];

        switch ($company->country) {
            case 'de':
                $dca['fields']['state']['inputType'] = 'select';
                $dca['fields']['state']['eval']['includeBlankOption'] = true;
                $dca['fields']['state']['options'] = $GLOBALS['TL_LANG']['COUNTIES'][$company->country];
                asort($dca['fields']['state']['options']);
                break;
            default:
                break;
        }
    }

    public function listChildren($arrRow)
    {
        return '<div class="tl_content_left">'.($arrRow['title'] ?: $arrRow['id']).' <span style="color:#b3b3b3; padding-left:3px">['.
            Date::parse(Config::get('datimFormat'), trim((string) $arrRow['dateAdded'])).']</span></div>';
    }

    public function checkPermission()
    {
        $user = BackendUser::getInstance();
        $database = Database::getInstance();

        if ($user->isAdmin) {
            return;
        }

        // Set the root IDs
        if (!is_array($user->companys) || empty($user->companys)) {
            $root = [0];
        } else {
            $root = $user->companys;
        }

        $id = strlen((string) Input::get('id')) ? Input::get('id') : CURRENT_ID;

        // Check current action
        switch (Input::get('act')) {
            case 'paste':
                // Allow
                break;

            case 'create':
                if (!strlen((string) Input::get('pid')) || !in_array(Input::get('pid'), $root)) {
                    throw new AccessDeniedException('Not enough permissions to create company items in company archive ID '. Input::get('pid').'.');
                }
                break;

            case 'cut':
            case 'copy':
                if (!in_array(Input::get('pid'), $root)) {
                    throw new AccessDeniedException('Not enough permissions to '. Input::get('act').' company item ID '.$id.' to company archive ID '. Input::get('pid').'.');
                }
            // no break STATEMENT HERE

            case 'edit':
            case 'show':
            case 'delete':
            case 'toggle':
            case 'feature':
                $objArchive = $database->prepare('SELECT pid FROM tl_company WHERE id=?')
                    ->limit(1)
                    ->execute($id);

                if ($objArchive->numRows < 1) {
                    throw new AccessDeniedException('Invalid company item ID '.$id.'.');
                }

                if (!in_array($objArchive->pid, $root)) {
                    throw new AccessDeniedException('Not enough permissions to '. Input::get('act').' company item ID '.$id.' of company archive ID '.$objArchive->pid.'.');
                }
                break;

            case 'select':
            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                if (!in_array($id, $root)) {
                    throw new AccessDeniedException('Not enough permissions to access company archive ID '.$id.'.');
                }

                $objArchive = $database->prepare('SELECT id FROM tl_company WHERE pid=?')
                    ->execute($id);

                if ($objArchive->numRows < 1) {
                    throw new AccessDeniedException('Invalid company archive ID '.$id.'.');
                }

                /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
                $session = System::getContainer()->get('session');

                $sessionData = $session->all();
                $sessionData['CURRENT']['IDS'] = array_intersect($sessionData['CURRENT']['IDS'], $objArchive->fetchEach('id'));
                $session->replace($sessionData);
                break;

            default:
                if (strlen((string) Input::get('act'))) {
                    throw new AccessDeniedException('Invalid command "'. Input::get('act').'".');
                } elseif (!in_array($id, $root)) {
                    throw new AccessDeniedException('Not enough permissions to access company archive ID '.$id.'.');
                }
                break;
        }
    }
}
