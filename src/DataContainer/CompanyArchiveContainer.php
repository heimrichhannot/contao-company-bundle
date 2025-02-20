<?php

namespace HeimrichHannot\CompanyBundle\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\System;

class CompanyArchiveContainer
{
    public function checkPermission(): void
    {
        $user = \Contao\BackendUser::getInstance();
        $database = \Contao\Database::getInstance();

        if ($user->isAdmin) {
            return;
        }

        // Set root IDs
        if (!is_array($user->companys) || empty($user->companys)) {
            $root = [0];
        } else {
            $root = $user->companys;
        }

        $GLOBALS['TL_DCA']['tl_company_archive']['list']['sorting']['root'] = $root;

        // Check permissions to add archives
        if (!$user->hasAccess('create', 'companyp')) {
            $GLOBALS['TL_DCA']['tl_company_archive']['config']['closed'] = true;
        }

        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $objSession */
        $objSession = \Contao\System::getContainer()->get('session');

        // Check current action
        switch (\Contao\Input::get('act')) {
            case 'create':
            case 'select':
                // Allow
                break;

            case 'edit':
                // Dynamically add the record to the user profile
                if (!in_array(\Contao\Input::get('id'), $root)) {
                    /** @var \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface $sessionBag */
                    $sessionBag = $objSession->getBag('contao_backend');

                    $arrNew = $sessionBag->get('new_records');

                    if (is_array($arrNew['tl_company_archive']) && in_array(\Contao\Input::get('id'), $arrNew['tl_company_archive'])) {
                        // Add the permissions on group level
                        if ('custom' != $user->inherit) {
                            $objGroup = $database->execute('SELECT id, companys, companyp FROM tl_user_group WHERE id IN(' . implode(',', array_map('intval', $user->groups)) . ')');

                            while ($objGroup->next()) {
                                $arrModulep = \Contao\StringUtil::deserialize($objGroup->companyp);

                                if (is_array($arrModulep) && in_array('create', $arrModulep)) {
                                    $arrModules = \Contao\StringUtil::deserialize($objGroup->companys, true);
                                    $arrModules[] = \Contao\Input::get('id');

                                    $database->prepare('UPDATE tl_user_group SET companys=? WHERE id=?')->execute(serialize($arrModules), $objGroup->id);
                                }
                            }
                        }

                        // Add the permissions on user level
                        if ('group' != $user->inherit) {
                            $user = $database->prepare('SELECT companys, companyp FROM tl_user WHERE id=?')
                                ->limit(1)
                                ->execute($user->id);

                            $arrModulep = \Contao\StringUtil::deserialize($user->companyp);

                            if (is_array($arrModulep) && in_array('create', $arrModulep)) {
                                $arrModules = \Contao\StringUtil::deserialize($user->companys, true);
                                $arrModules[] = \Contao\Input::get('id');

                                $database->prepare('UPDATE tl_user SET companys=? WHERE id=?')
                                    ->execute(serialize($arrModules), $user->id);
                            }
                        }

                        // Add the new element to the user object
                        $root[] = \Contao\Input::get('id');
                        $user->companys = $root;
                    }
                }
            // no break;

            case 'copy':
            case 'delete':
            case 'show':
                if (!in_array(\Contao\Input::get('id'), $root) || ('delete' == \Contao\Input::get('act') && !$user->hasAccess('delete', 'companyp'))) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . \Contao\Input::get('act') . ' company_archive ID ' . \Contao\Input::get('id') . '.');
                }
                break;

            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
                $session = $objSession->all();
                if ('deleteAll' == \Contao\Input::get('act') && !$user->hasAccess('delete', 'companyp')) {
                    $session['CURRENT']['IDS'] = [];
                } else {
                    $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
                }
                $objSession->replace($session);
                break;

            default:
                if (strlen((string) \Contao\Input::get('act'))) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . \Contao\Input::get('act') . ' company_archives.');
                }
                break;
        }
    }

    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return System::getContainer()->get('security.helper')->isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELDS_OF_TABLE, 'tl_company_archive') ? '<a href="' . Controller::addToUrl($href . '&amp;id=' . $row['id']) . '&rt=' . \Contao\System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue() . '" title="' . \Contao\StringUtil::specialchars($title) . '"' . $attributes . '>' . \Contao\Image::getHtml($icon, $label) . '</a> ' : \Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', (string) $icon)) . ' ';
    }

    public function copyArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return \Contao\BackendUser::getInstance()->hasAccess('create', 'companyp') ? '<a href="' . Controller::addToUrl($href . '&amp;id=' . $row['id']) . '&rt=' . \Contao\System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue() . '" title="' . \Contao\StringUtil::specialchars($title) . '"' . $attributes . '>' . \Contao\Image::getHtml($icon, $label) . '</a> ' : \Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', (string) $icon)) . ' ';
    }

    public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return \Contao\BackendUser::getInstance()->hasAccess('delete', 'companyp') ? '<a href="' . Controller::addToUrl($href . '&amp;id=' . $row['id']) . '&rt=' . \Contao\System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue() . '" title="' . \Contao\StringUtil::specialchars($title) . '"' . $attributes . '>' . \Contao\Image::getHtml($icon, $label) . '</a> ' : \Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', (string) $icon)) . ' ';
    }
}
