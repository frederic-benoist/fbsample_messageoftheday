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

require_once _PS_MODULE_DIR_.'fbsample_messageoftheday/vendor/autoload.php';

use Fbenoist\FbSample_MessageOfTheDay\Model\MessageOfDay;

class AdminMessageOfTheDayController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'messageofday';
        $this->className = 'Fbenoist\FbSample_MessageOfTheDay\Model\MessageOfDay';
        $this->lang = true;
        $this->bootstrap = true;
        parent::__construct();

        // HelperList Fields params
        $this->fields_list = array(
            'id_messageofday' => array('title' => '#'),
            'title' => array(
                'title' => $this->module->l('Title', 'AdminMessageOfTheDayController')
            ),
            'date_published_on' => array(
                'title' => $this->module->l('Published On', 'AdminMessageOfTheDayController'),
                'type' => 'date'
            ),
            'date_published_off' => array(
                'title' => $this->module->l('Published Off', 'AdminMessageOfTheDayController'),
                'type' => 'date'
            ),
            'active' => array(
                'title' => $this->module->l('Active', 'AdminMessageOfTheDayController'),
                'active' => 'status'
            )
        );

        // TODO: Add custom action
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->module->l('Delete selected', 'AdminMessageOfTheDayController'),
                'confirm' => $this->module->l('Delete selected items?', 'AdminMessageOfTheDayController')
            ),
            'enableSelection' => array(
                'text' => $this->module->l('Enable selection', 'AdminMessageOfTheDayController')
            ),
            'disableSelection' => array(
                'text' => $this->module->l('Disable selection', 'AdminMessageOfTheDayController')
                )
        );
    }

    /**
     * Add Edit and Delete on each line
     *
     * @return void
     */
    public function renderList()
    {
        // TODO: Add custom action
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    /**
     * Create form
     *
     * @return void
     */
    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
               'title' => $this->module->l('Edit Message', 'AdminMessageOfTheDayController')
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Title', 'AdminMessageOfTheDayController'),
                    'name' => 'title',
                    'size' => 120,
                    'lang' => true,
                    'required' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Published', 'AdminMessageOfTheDayController'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Yes', 'AdminMessageOfTheDayController')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('No', 'AdminMessageOfTheDayController')
                        )
                    )
                ),
                array(
                    'type' => 'date',
                    'label' => $this->module->l('Published From', 'AdminMessageOfTheDayController'),
                    'name' => 'date_published_on',
                    'size' => 10,
                    'required' => true,
                ),
                array(
                    'type' => 'date',
                    'label' => $this->module->l('Published To', 'AdminMessageOfTheDayController'),
                    'name' => 'date_published_off',
                    'size' => 10,
                    'required' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Message', 'AdminMessageOfTheDayController'),
                    'name' => 'message',
                    'autoload_rte' => true,
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 100
                )
            ),
            'submit' => array(
                'title' => $this->module->l('Save')
            )
        );
        return parent::renderForm();
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
        $new_menu->class_name = 'AdminMessageOfTheDay'; // Class Name (Without "Controller")
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
        $remove_id = Tab::getIdFromClassName('AdminMessageOfTheDay');
        if ($remove_id) {
            $to_remove = new Tab((int)$remove_id);
            if (validate::isLoadedObject($to_remove)) {
                return $to_remove->delete();
            }
        }
        return true;
    }
}
