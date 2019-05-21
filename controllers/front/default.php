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

class FbSample_MessageOfTheDayDefaultModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->context->smarty->assign(array(
            'messages' =>  MessageOfDay::getAllActiveMessage(),
        ));
        $this->setTemplate('module:fbsample_messageoftheday/views/templates/front/default.tpl');
        parent::initContent();
    }

    public function setMedia()
    {
        $this->registerStylesheet(
            'messageoftheday-default',
            'modules/fbsample_messageoftheday/views/css/default.css',
            array('media' => 'all', 'priority' => 150)
        );
        $this->context->controller->registerJavascript(
            'messageoftheday-default',
            'modules/fbsample_messageoftheday/views/js/default.js',
            ['position' => 'bottom', 'priority' => 150]
        );
        parent::setMedia();
    }
}
