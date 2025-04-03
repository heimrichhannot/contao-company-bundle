<?php

namespace HeimrichHannot\CompanyBundle\DataContainer;

use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Database;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CompanyArchiveContainer
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $auth,
    ) {
    }

    public function checkPermission(): void
    {
        $user = BackendUser::getInstance();
        $database = Database::getInstance();

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

        /** @var SessionInterface $objSession */
        $objSession = System::getContainer()->get('request_stack')->getSession();

        // Check current action
        switch (Input::get('act')) {
            case 'create':
            case 'select':
                // Allow
                break;

            case 'edit':
                // Dynamically add the record to the user profile
                if (!in_array(Input::get('id'), $root)) {
                    /** @var AttributeBagInterface $sessionBag */
                    $sessionBag = $objSession->getBag('contao_backend');

                    $arrNew = $sessionBag->get('new_records');

                    if (is_array($arrNew['tl_company_archive']) && in_array(Input::get('id'), $arrNew['tl_company_archive'])) {
                        // Add the permissions on group level
                        if ('custom' != $user->inherit) {
                            $objGroup = $database->execute('SELECT id, companys, companyp FROM tl_user_group WHERE id IN(' . implode(',', array_map('intval', $user->groups)) . ')');

                            while ($objGroup->next()) {
                                $arrModulep = StringUtil::deserialize($objGroup->companyp);

                                if (is_array($arrModulep) && in_array('create', $arrModulep)) {
                                    $arrModules = StringUtil::deserialize($objGroup->companys, true);
                                    $arrModules[] = Input::get('id');

                                    $database->prepare('UPDATE tl_user_group SET companys=? WHERE id=?')->execute(serialize($arrModules), $objGroup->id);
                                }
                            }
                        }

                        // Add the permissions on user level
                        if ('group' != $user->inherit) {
                            $user = $database->prepare('SELECT companys, companyp FROM tl_user WHERE id=?')
                                ->limit(1)
                                ->execute($user->id);

                            $arrModulep = StringUtil::deserialize($user->companyp);

                            if (is_array($arrModulep) && in_array('create', $arrModulep)) {
                                $arrModules = StringUtil::deserialize($user->companys, true);
                                $arrModules[] = Input::get('id');

                                $database->prepare('UPDATE tl_user SET companys=? WHERE id=?')
                                    ->execute(serialize($arrModules), $user->id);
                            }
                        }

                        // Add the new element to the user object
                        $root[] = Input::get('id');
                        $user->companys = $root;
                    }
                }
                // no break;

            case 'copy':
            case 'delete':
            case 'show':
                if (!in_array(Input::get('id'), $root) || ('delete' == Input::get('act') && !$user->hasAccess('delete', 'companyp'))) {
                    throw new AccessDeniedException('Not enough permissions to ' . Input::get('act') . ' company_archive ID ' . Input::get('id') . '.');
                }
                break;

            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
                $session = $objSession->all();
                if ('deleteAll' == Input::get('act') && !$user->hasAccess('delete', 'companyp')) {
                    $session['CURRENT']['IDS'] = [];
                } else {
                    $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
                }
                $objSession->replace($session);
                break;

            default:
                if (strlen((string) Input::get('act'))) {
                    throw new AccessDeniedException('Not enough permissions to ' . Input::get('act') . ' company_archives.');
                }
                break;
        }
    }

    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->auth->isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELDS_OF_TABLE, 'tl_company_archive') ? '<a href="' . Controller::addToUrl($href . '&amp;id=' . $row['id']) . '&rt=' . System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue() . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', (string) $icon)) . ' ';
    }

    public function copyArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return BackendUser::getInstance()->hasAccess('create', 'companyp') ? '<a href="' . Controller::addToUrl($href . '&amp;id=' . $row['id']) . '&rt=' . System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue() . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', (string) $icon)) . ' ';
    }

    public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return BackendUser::getInstance()->hasAccess('delete', 'companyp') ? '<a href="' . Controller::addToUrl($href . '&amp;id=' . $row['id']) . '&rt=' . System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue() . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', (string) $icon)) . ' ';
    }
}
