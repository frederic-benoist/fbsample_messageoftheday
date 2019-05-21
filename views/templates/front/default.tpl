{**
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
 *}

{extends file=$layout}

{block name='content'}
  <section id="main" class="messages-list">
        <h1>{l s='Messages' mod='fbsample_messageoftheday'}</h1>
        <div class="card-columns">
        {foreach from=$messages item=message}
          <div class="card">
            <div class="card-header">{$message.title}</div>
            <div class="card-body">{$message.message|cleanHtml nofilter}</div>
          </div>
        {/foreach}
        </div>
    </section>
{/block}
