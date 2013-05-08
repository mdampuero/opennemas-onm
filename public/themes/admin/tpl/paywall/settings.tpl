{extends file="base/admin.tpl"}
{block name="footer-js" append}
    <script type="text/javascript">
    jQuery(document).ready(function ($){
        $('#payment_modes').on('click', '.del', function() {
            var button = $(this);
            button.closest('.payment_mode').each(function(){
                $(this).remove();
            });
        })

        $('#add_payment_mode').on('click', function(){
            var source = $('#payment-template').html();
            $('.nopaymentmodes').remove();
            $('#payment_modes .modes').append(source);
        });

    });
    </script>
<script id="payment-template" type="text/x-handlebars-template">
<div class="payment_mode">
    {html_options name="settings[payment_modes][time][]" options=$times required="required"}
    <input type="text" name="settings[payment_modes][description][]"  value="" placeholder="Name"  required="required">
    <div class="input-append" style="display:inline-block">
        <input type="number" name="settings[payment_modes][price][]" value="" step="any" min="0" placeholder="Set a price" required="required"  class="input-small"/>
        <div class="btn addon">
            {if $settings['money_unit']}
                {$money_units[$settings['money_unit']]}
            {else}
                <i class="icon-money"></i>
            {/if}
        </div>
    </div>
    <div class="btn del">
        <i class="icon-trash"></i>
    </div>
</div>
</script>
{/block}

{block name="header-css" append}
<style>
    /*#add_payment_mode { margin-top:15px;}*/
    .payment_mode {
        margin-bottom:10px;
    }
</style>
{/block}

{block name="content"}
<form action="{url name=admin_paywall_settings_save}" method="post">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Paywall{/t} :: {t}Settings{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_paywall}" title="{t}Go back to list{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content clearfix">

        {render_messages}

        <div class="form-horizontal panel">

            <fieldset>
                <h3 class="settings-header">{t}Paypal API authentication{/t}</h3>

                <p>{t escape=off}In order to <strong>connect Opennemas with Paypal</strong> you have to fill your Paypal API credentials below:{/t}</p>

                <div id="money" class="control-group">
                    <label for="paypal_username" class="control-label">{t}User name{/t}</label>
                    <div class="controls">
                        <input type="text" name="settings[paypal_username]" value="{$settings['paypal_username']}" class="input-xlarge" required>
                    </div>
                </div>

                <div id="money" class="control-group">
                    <label for="paypal_password" class="control-label">{t}Password{/t}</label>
                    <div class="controls">
                        <input type="text" name="settings[paypal_password]" value="{$settings['paypal_password']}" class="input-xlarge" required>
                    </div>
                </div>

                <div id="money" class="control-group">
                    <label for="paypal_signature" class="control-label">{t}Signature{/t}</label>
                    <div class="controls">
                        <input type="text" name="settings[paypal_signature]" value="{$settings['paypal_signature']}" class="input-xlarge" required>
                    </div>
                </div>

                <p class="help-block">
                    {t}If you don't have these identification params click on the next link{/t}
                    <a href="#" id="paypal-get-identification" class="btn btn-mini btn-warning">{t}Obtain your Paypal identification data{/t}</a>
                </p>
            </fieldset>

            <hr>

            <fieldset>
                <h3 class="settings-header"> {t}Use the testing environment Sandbox{/t}</h3>

                <div id="money" class="control-group">
                    <div class="controls">
                        <input type="radio" name="settings[developer_mode]" id="settings[developer_mode]" value="1" {if $settings['developer_mode'] == true}checked="checked"{/if}> {t}Real mode (recommended){/t}
                        <br>
                        <input type="radio" name="settings[developer_mode]" id="settings[developer_mode]" value="0" {if $settings['developer_mode'] == false}checked="checked"{/if}> {t}Testing mode{/t}
                    </div>
                </div>
                <div class="help-block">
                    <p>{t escape=off}Paypal allows you to enable a testing environment where <strong>all the transactions will not be real</strong>, so you can test if the paywall is working well.{/t}</p>
                    {t}Active a testing environment in your Paypal account (only if you are a developer){/t} <a href="https://developer.paypal.com/">{t}More information{/t}</a>
                </div>
            </fieldset>

            <hr>

            <fieldset>
                <h3 class="settings-header">{t}Transaction details{/t}</h3>

                <div id="money" class="control-group">
                    <label for="money_unit" class="control-label">{t}Money unit{/t}</label>
                    <div class="controls">
                        {html_options name="settings[money_unit]" options=$money_units selected=$settings['money_unit']}
                    </div>
                </div>

                <div id="money" class="control-group">
                    <label for="vat_percentage" class="control-label">{t}VAT %{/t}</label>
                    <div class="controls">
                        <input type="number" name="settings[vat_percentage]" value="{$settings['vat_percentage']}" step="any" min="0" required>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <h3 class="settings-header">{t}Payment modes{/t}</h3>
                <p>{t}Below you can add different payment modes by including the time range that the user can purchase, the description and the price{/t}</p>
                <div id="payment_modes" class="control-group">
                    <div class="controls">
                        <div class="modes">
                            {foreach name=i from=$settings['payment_modes'] item=payment_mode}
                            <div class="payment_mode">
                                {html_options name="settings[payment_modes][time][]" options=$times selected=$payment_mode.time}
                                <input type="text" name="settings[payment_modes][description][]"  value="{$payment_mode.description}" placeholder="{t}Name{/t}">
                                <div class="input-append" style="display:inline-block">
                                    <input type="number" name="settings[payment_modes][price][]" value="{$payment_mode.price}" step="any" min="0" placeholder="{t}Set a price{/t}" required="required" class="input-small"/>
                                    <div class="btn addon">
                                        {if $settings['money_unit']}
                                            {$money_units[$settings['money_unit']]}
                                        {else}
                                            <i class="icon-money"></i>
                                        {/if}
                                    </div>
                                </div>
                                <div class="btn del">
                                    <i class="icon-trash"></i>
                                </div>
                            </div>
                            {foreachelse}
                            <p class="nopaymentmodes">{t}No available payment modes. Add a new one with the button below.{/t}</p>
                            {/foreach}
                        </div>
                        <a id="add_payment_mode" class="btn">
                            <i class="icon-plus"></i>
                            {t}Add new payment mode{/t}
                        </a>
                    </div>
                </div>
            </fieldset>

        </div>
    </div>
</form>
{/block}

{block name="footer-js"}
<script>
    $(function() {
        $('#paypal-get-identification').on('click', function() {
            identificationButtonClicked = true;
            var url = 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true';
            var title = 'PayPal identification informations';
            window.open (url, title, config='height=500, width=360, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no');
            return false;
        });
    });
</script>
{/block}
