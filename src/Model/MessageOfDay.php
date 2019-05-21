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

namespace Fbenoist\FbSample_MessageOfTheDay\Model;

use Context;
use Db;
use DbQuery;
use ObjectModel;
use Validate;

class MessageOfDay extends ObjectModel
{
    public $active;
    public $date_published_on;
    public $date_published_off;
    public $date_add;
    public $date_upd;
    public $title;
    public $message;
    public static $definition = array(
        'table' => 'messageofday',
        'primary' => 'id_messageofday',
        'multilang' => true,
        'fields' => array(
            'date_published_on' => array('type' => self::TYPE_DATE),
            'date_published_off' => array('type' => self::TYPE_DATE),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
            'active' => array('type' => self::TYPE_BOOL, 'required' => true),
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 120
            ),
            'message' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isString',
                'size' => 3999999999999
            ),
        )
    );

    public static function createDbTable()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'messageofday`(
                    `id_messageofday` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `active` TINYINT(1) NOT NULL DEFAULT \'0\',
                    `date_published_on` DATE,
                    `date_published_off` DATE,
                    `date_add` DATETIME,
                    `date_upd` DATETIME,
                    PRIMARY KEY (`id_messageofday`)
                    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')
            && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'messageofday_lang`(
                    `id_messageofday` INT(10) UNSIGNED NOT NULL,
                    `id_lang` INT(10) UNSIGNED NOT NULL,
                    `title` VARCHAR(120) DEFAULT NULL,
                    `message` TEXT,
                    PRIMARY KEY (`id_messageofday`,`id_lang`)
                    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
    }

    public static function removeDbTable()
    {
        return Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'messageofday`')
            && Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'messageofday_lang`');
    }

    public static function getTodayMessage($id_lang = null)
    {
        $id_lang = is_null($id_lang) ? (int)Context::getContext()->language->id : (int)$id_lang;

        $query = new DbQuery();
        $query->from('messageofday', 'm');
        $query->select('id_messageofday');
        $query->where('m.active = 1');
        $query->where('m.date_published_on <= now()');
        $query->where('m.date_published_off > now()');
        $query->orderBy('date_upd DESC');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        // TODO: (Opti) Use hydrate to remove second sql query
        if (1 == count($result)) {
            return new MessageOfDay((int)$result['id_messageofday'], $id_lang);
        }
    }

    public static function getAllActiveMessage($id_lang = null)
    {
        $id_lang = is_null($id_lang) ? (int)Context::getContext()->language->id : (int)$id_lang;

        $query = new DbQuery();
        $query->select('ml.title, ml.message');
        $query->from('messageofday', 'm');
        $query->innerJoin(
            'messageofday_lang',
            'ml',
            'm.id_messageofday = ml.id_messageofday AND ml.id_lang = '.(int)$id_lang
        );
        $query->where('m.active = 1');
        $query->where('m.date_published_on <= now()');
        $query->where('m.date_published_off > now()');
        $query->orderBy('date_upd DESC');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
