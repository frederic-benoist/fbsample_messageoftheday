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

use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Fbenoist\FbSample_MessageOfTheDay\Model\MessageOfDay;
use Fbenoist\FbSample_MessageOfTheDay\Controller\Admin\AdminSFMessageOfTheDayController;

class FbSample_MessageOfTheDay extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'fbsample_messageoftheday';
        $this->tab = 'front_office_features';
        $this->version = '1.7.0';
        $this->author = 'Frédéric BENOIST';
        $this->bootstrap = true;
        $this->controllers = array('default');

        parent::__construct();

        $this->displayName = $this->l('Sample BO legacy module');
        $this->description = $this->l('Show message of the day to your customer');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        // Legacy BO Controller does not use namespaces
        include_once dirname(__FILE__).'/controllers/admin/adminmessageofthedayController.php';

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
            || !MessageOfDay::createDbTable()
            || !AdminMessageOfTheDayController::installInBO($this->l('Manage day message'))
            || !AdminSFMessageOfTheDayController::installInBO($this->l('SF Manage day message'))
            || !$this->registerHook('displayLeftColumn')
            || !$this->registerHook('displayHeader')) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        // Legacy BO Controller does not use namespaces
        include_once dirname(__FILE__).'/controllers/admin/adminmessageofthedayController.php';

        return parent::uninstall()
            && AdminMessageOfTheDayController::removeFromBO()
            && AdminSFMessageOfTheDayController::removeFromBO()
            && MessageOfDay::removeDbTable();
    }

    public function renderWidget($hookName, array $params)
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        return $this->fetch(
            'module:'.$this->name.'/views/templates/hook/messageoftheday.tpl'
        );
    }

    public function getWidgetVariables($hookName, array $params)
    {
        $serializer = new ObjectPresenter;
        $messageofday = MessageOfDay::getTodayMessage();

        if (Validate::isLoadedObject($messageofday)) {
            return array(
                'messageofday' => $serializer->present($messageofday),
                'no_validator_warning' => count($params) > 0 ? $hookName : 'empty'
            );
        }
        return array();
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'modules-messageoftheday',
            'modules/'.$this->name.'/views/css/messageoftheday.css',
            array('media' => 'all', 'priority' => 150)
        );
    }
}
