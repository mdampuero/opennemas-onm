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
            </ul>
        </div>
    </div>
    <div class="wrapper-content clearfix">
        {render_messages}

        <div class="form-horizontal panel">

            <div id="money" class="control-group">
                <label for="paypal_user_email" class="control-label">{t}Paypal user email{/t}</label>
                <div class="controls">
                    <input type="email" name="settings[paypal_user_email]" value="{$settings['paypal_user_email']}">
                    <div class="help-block">{t}Fill this entry with your Paypal account where you want to receive the paywall payments.{/t}</div>
                </div>
            </div>

            <div id="money" class="control-group">
                <label for="money_unit" class="control-label">{t}Money unit{/t}</label>
                <div class="controls">
                    {html_options name="settings[money_unit]" options=$money_units selected=$settings['money_unit']}
                </div>
            </div>

            <div id="payment_modes" class="control-group">
                <label class="control-label" for="subtitle">{t}Payment modes{/t}</label>
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

        </div>
    </div>
</form>
{/block}
