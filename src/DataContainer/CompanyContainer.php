<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\CompanyBundle\DataContainer;

use Contao\Controller;
use Contao\System;
use Contao\Versions;

class CompanyContainer
{
    public function initPalette()
    {
        $company = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_company', \Input::get('id'));

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
            \Date::parse(\Contao\Config::get('datimFormat'), trim($arrRow['dateAdded'])).']</span></div>';
    }

    public function checkPermission()
    {
        $user = \Contao\BackendUser::getInstance();
        $database = \Contao\Database::getInstance();

        if ($user->isAdmin) {
            return;
        }

        // Set the root IDs
        if (!is_array($user->companys) || empty($user->companys)) {
            $root = [0];
        } else {
            $root = $user->companys;
        }

        $id = strlen(\Contao\Input::get('id')) ? \Contao\Input::get('id') : CURRENT_ID;

        // Check current action
        switch (\Contao\Input::get('act')) {
            case 'paste':
                // Allow
                break;

            case 'create':
                if (!strlen(\Contao\Input::get('pid')) || !in_array(\Contao\Input::get('pid'), $root)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to create company items in company archive ID '.\Contao\Input::get('pid').'.');
                }
                break;

            case 'cut':
            case 'copy':
                if (!in_array(\Contao\Input::get('pid'), $root)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to '.\Contao\Input::get('act').' company item ID '.$id.' to company archive ID '.\Contao\Input::get('pid').'.');
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
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Invalid company item ID '.$id.'.');
                }

                if (!in_array($objArchive->pid, $root)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to '.\Contao\Input::get('act').' company item ID '.$id.' of company archive ID '.$objArchive->pid.'.');
                }
                break;

            case 'select':
            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                if (!in_array($id, $root)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to access company archive ID '.$id.'.');
                }

                $objArchive = $database->prepare('SELECT id FROM tl_company WHERE pid=?')
                    ->execute($id);

                if ($objArchive->numRows < 1) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Invalid company archive ID '.$id.'.');
                }

                /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
                $session = \System::getContainer()->get('session');

                $session = $session->all();
                $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objArchive->fetchEach('id'));
                $session->replace($session);
                break;

            default:
                if (strlen(\Contao\Input::get('act'))) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Invalid command "'.\Contao\Input::get('act').'".');
                } elseif (!in_array($id, $root)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to access company archive ID '.$id.'.');
                }
                break;
        }
    }

    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $user = \Contao\BackendUser::getInstance();

        if (strlen(\Contao\Input::get('tid'))) {
            $this->toggleVisibility(\Contao\Input::get('tid'), ('1' === \Contao\Input::get('state')), (@func_get_arg(12) ?: null));
            Controller::redirect(System::getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$user->hasAccess('tl_company::published', 'alexf')) {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="'.Controller::addToUrl($href).'" title="'.\StringUtil::specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
    }

    public function toggleVisibility($intId, $blnVisible)
    {
        $objUser = \BackendUser::getInstance();
        $objDatabase = \Database::getInstance();

        // Check permissions to publish
        if (!$objUser->isAdmin && !$objUser->hasAccess('tl_company::published', 'alexf')) {
            Controller::log('Not enough permissions to publish/unpublish item ID "'.$intId.'"', 'tl_company toggleVisibility', TL_ERROR);
            Controller::redirect('contao/main.php?act=error');
        }

        $objVersions = new Versions('tl_company', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_company']['fields']['published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_company']['fields']['published']['save_callback'] as $callback) {
                $blnVisible = System::importStatic($callback[0])->{$callback[1]}($blnVisible, $this);
            }
        }

        // Update the database
        $objDatabase->prepare('UPDATE tl_company SET tstamp='.time().", published='".($blnVisible ? 1 : '')."' WHERE id=?")->execute($intId);

        $objVersions->create();
        Controller::log('A new version of record "tl_company.id='.$intId.'" has been created',
            'tl_company toggleVisibility()', TL_GENERAL);
    }
}
