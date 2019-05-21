<?php
/**
 * 2007-2019 Frédéric BENOIST
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Frédéric BENOIST
 *  @copyright 2013-2019 Frédéric BENOIST <https://www.fbenoist.com/>
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace Fbenoist\FbSample_MessageOfTheDay\Controller\Admin;

use Language;
use Tab;
use Validate;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminSFMessageOfTheDayController extends FrameworkBundleAdminController
{
    /**
     * @see https://devdocs.prestashop.com/1.7/development/architecture/migration-guide/controller-routing/#security
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     message="You do not have permission to access Exemple page."
     * )
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render(
            '@Modules/fbsample_messageoftheday/views/templates/admin/AdminSFMessageOfTheDayController.html.twig',
            array(
                'layoutTitle' => 'Message Of Day'
            )
        );
    }

    /**
     * Install AdminDayMessage in customer back office menu
     * @return boolean true if success
     */
    public static function installInBO($menu_entry_title)
    {
        // Use Legacy
        $new_menu = new Tab();
        $new_menu->id_parent = Tab::getIdFromClassName('AdminParentCustomer');
        $new_menu->class_name = 'AdminSFMessageOfTheDayControllerIndexClass'; // legacy controller
        $new_menu->module = 'fbsample_messageoftheday'; // Module name
        $new_menu->active = true;

        // Set menu name in all active Language.
        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $new_menu->name[(int)$language['id_lang']] = $menu_entry_title;
        }
        return $new_menu->save();
    }

    /**
     * Remove AdminDayMessage in customer back office menu
     * @return boolean true if success
     */
    public static function removeFromBO()
    {
        $remove_id = Tab::getIdFromClassName('AdminSFMessageOfTheDayControllerIndexClass');
        if ($remove_id) {
            $to_remove = new Tab((int)$remove_id);
            if (validate::isLoadedObject($to_remove)) {
                return $to_remove->delete();
            }
        }
        return true;
    }
}
