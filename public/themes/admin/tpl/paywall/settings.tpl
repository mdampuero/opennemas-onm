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
    .settings-header {
        border-bottom:1px solid #eeeeee;
        margin-bottom:20px;
        padding-bottom:10px;
    }
    fieldset {
        margin-bottom:40px;
    }
    .step-number {
        background:#ccc;
        border-radius:20px;
        padding:5px 10px;
        color:White;
        font-weight: bold;
        font-family: Arial, Verdana;
        font-size:1.2em;
        display:inline-block;
        line-height:1em;
    }
    ol li {
        margin-bottom:5px;
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

        <div id="warnings-validation"></div>
        {render_messages}

        <div class="form-horizontal">

            <fieldset>
                <h4 class="settings-header"><div class="step-number">1</div> {t}Paypal API authentication{/t}</h4>

                <p>{t escape=off}In order to <strong>connect Opennemas with Paypal</strong> you have to fill your Paypal API credentials below:{/t}</p>

                <div class="control-group">
                    <label for="paypal_username" class="control-label">{t}User name{/t}</label>
                    <div class="controls">
                        <input type="text" id="username" name="settings[paypal_username]" value="{$settings['paypal_username']}" class="input-xlarge" required>
                    </div>
                </div>

                <div class="control-group">
                    <label for="paypal_password" class="control-label">{t}Password{/t}</label>
                    <div class="controls">
                        <input type="text" id="password" name="settings[paypal_password]" value="{$settings['paypal_password']}" class="input-xlarge" required>
                    </div>
                </div>

                <div class="control-group">
                    <label for="paypal_signature" class="control-label">{t}Signature{/t}</label>
                    <div class="controls">
                        <input type="text" id="signature" name="settings[paypal_signature]" value="{$settings['paypal_signature']}" class="input-xlarge" required>
                    </div>
                </div>

                <p class="help-block">
                    {t}If you don't have these identification params click on the next link{/t}
                    <a href="#" id="paypal-get-identification" class="btn btn-mini btn-warning">{t}Obtain your Paypal identification data{/t}</a>
                </p>
            </fieldset>

            <fieldset>
                <h4 class="settings-header"><div class="step-number">2</div> {t}Use the testing environment Sandbox{/t}</h4>

                <div class="control-group">
                    <div class="controls">
                        <label for="developer_mode_yes">
                            <input type="radio" name="settings[developer_mode]" id="developer_mode_no" value="1" {if $settings['developer_mode'] == true}checked="checked"{/if}>
                            {t}Real mode (recommended){/t}
                        </label>
                        <label for="developer_mode_no">
                            <input type="radio" name="settings[developer_mode]" id="developer_mode_yes" value="0" {if $settings['developer_mode'] == false}checked="checked"{/if}>
                            {t}Testing mode{/t}
                        </label>
                    </div>
                </div>
                <p class="help-block">
                    {t}Validate here your Paypal API credentials in the selected mode{/t}
                    <a href="#" id="validate-credentials" class="btn btn-mini btn-danger">{t}Validate{/t}</a>
                    <img src="{$params.IMAGE_DIR}spinner.gif" alt="{t}Checking{/t}" style="display: none;" id="loading_image">
                </p>
                <br>
                <div class="help-block">
                    <p>{t escape=off}Paypal allows you to enable a testing environment where <strong>all the transactions will not be real</strong>, so you can test if the paywall is working well.{/t}</p>
                    {t}Active a testing environment in your Paypal account (only if you are a developer){/t} <a href="https://developer.paypal.com/">{t}More information{/t}</a>
                </div>
            </fieldset>

            <fieldset>
                <h4 class="settings-header"><div class="step-number">3</div> {t}Currency & taxes{/t}</h4>

                <div id="money" class="control-group">
                    <label for="money_unit" class="control-label">{t}Money unit{/t}</label>
                    <div class="controls">
                        {html_options name="settings[money_unit]" options=$money_units selected=$settings['money_unit']}
                    </div>
                </div>

                <div class="control-group">
                    <label for="vat_percentage" class="control-label">{t}VAT %{/t}</label>
                    <div class="controls">
                        <input type="number" name="settings[vat_percentage]" value="{$settings['vat_percentage']}" step="any" min="0" required>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <h4 class="settings-header"><div class="step-number">4</div> {t}Payment modes{/t}</h4>
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
{*
            <fieldset>
                <h4 class="settings-header"><div class="step-number">5</div> {t}Recurring payments (optional){/t}</h4>

                <p>
                    {t}Paypal allow your users to subscribe to your Paywall through recurring payments. This means that your users will be charged periodically without having to worry about payments and due dates, and will allow you to increase the user engagement.{/t}
                </p>

                <div class="control-group">
                    <div class="control-label">
                    </div>
                    <div class="controls">
                        <label for="recurring_checkbox">
                            <input type="checkbox" name="settings[recurring]" value="1" {if (isset($settings['recurring']) && $settings['recurring'] eq 1)}checked{/if} id="recurring_checkbox">
                            {t}Mark this if you want to enable recurring payments{/t}
                        </label>
                    </div>
                </div>
                {capture name=ipn}{setting name=valid_ipn}{/capture}
                <div class="control-group well recurring-paypal-help {if (!isset($settings['recurring']) || $settings['recurring'] eq 0)}hide{/if}">
                    <p>{t}You have to activate some options in the Paypal configuration to make recurring payments work. Please follow next steps:{/t}</p>
                    <ol>
                        <li>{t}Go to your merchant Paypal{/t} <a class="btn btn-mini" href="https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-ipn-notify" target="_blank">{t}IPN web configuration page {/t}<i class="icon icon-external-link"></i></a>{t} and log in with your merchant account{/t}.</li>
                        <li>{t}Click in the "Choose IPN configuration" button{/t}.</li>
                        <li>{t}Fill in the "Notification URL" field with this address{/t}<input type="text" class="input-xlarge" readonly="readonly" style="display:block" value="{url name='frontend_ws_paypal_ipn' absolute=true}"></li>
                        <li>{t}Enable the "Receive IPN messages" checkbox{/t}.</li>
                        <li>{t}Click on the validate button to check ipn is working fine and enable recurring payment{/t}.
                            {if $smarty.capture.ipn == 'valid'}
                            <a id="validate-ipn" class="btn btn-mini btn-success">{t}Valid{/t}</a>
                            {elseif $smarty.capture.ipn == 'waiting'}
                            <a id="validate-ipn" class="btn btn-mini btn-warning">{t}Waiting{/t}</a>
                            {else}
                            <a id="validate-ipn" class="btn btn-mini btn-danger">{t}Validate{/t}</a>
                            {/if}
                            <img src="{$params.IMAGE_DIR}spinner.gif" alt="{t}Checking{/t}" style="display: none;" id="loading_image_ipn">
                        </li>
                        <li>{t}Finally, click in the "Save" button to save this configuration{/t}.</li>
                    </ol>
                </div>
            </fieldset>
            <fieldset>
                <h4 class="settings-header"><div class="step-number">6</div> {t}Accept Opennemas payment agreements terms{/t}</h4>

                <div class="controls">
                    <label for="terms">
                        <input type="checkbox" name="settings[terms]" value="1" {if (isset($settings['terms']) && $settings['terms'] eq 1)}checked{/if} id="terms" required>
                        {t escape=off}Read and accept the <a href="http://help.opennemas.com/" target="_blank">payment agreements terms</a> of Opennemas{/t}
                    </label>
                </div>
            </fieldset>
*}
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

        $('#validate-ipn').on('click', function(e, ui) {
            var username = $("#username").val();
            var password = $("#password").val();
            var signature = $("#signature").val();

            if ($('#developer_mode_no').is(':checked')) {
                var mode = 'live';
            } else {
                var mode = 'sandbox';
            }

            $('#loading_image_ipn').show();
            $.ajax({
                url: '{url name=admin_paywall_set_validate_ipn}',
                type: "POST",
                data: {
                    username : username,
                    password : password,
                    signature : signature,
                    mode : mode
                }
            }).done(function(data) {
                window.location.href = data;
            }).fail(function () {
                $('#warnings-validation').html(
                    '<div class="alert alert-error">'+
                        '<button class="close" data-dismiss="alert">×</button>'+
                        '{t}Could not connect to PayPal. Validate your API credentials and try again{/t}'+
                    '</div>'
                );
                $('#loading_image_ipn').hide();
            });
        });

        $('#validate-credentials').on('click', function(e, ui) {
            var username = $("#username").val();
            var password = $("#password").val();
            var signature = $("#signature").val();

            if ($('#developer_mode_no').is(':checked')) {
                var mode = 'live';
            } else {
                var mode = 'sandbox';
            }

            $('#loading_image').show();
            $.ajax({
                url: '{url name=admin_paywall_validate_api}',
                type: "POST",
                data: {
                    username : username,
                    password : password,
                    signature : signature,
                    mode : mode
                }
            }).done(function() {
                $('#warnings-validation').html(
                    '<div class="alert alert-success">'+
                        '<button class="close" data-dismiss="alert">×</button>'+
                        '{t}Paypal API authentication is correct.{/t}'+
                    '</div>'
                );
                $('#loading_image').hide();
                $('#validate-credentials').removeClass('btn-danger').addClass('btn-success');
            }).fail(function() {
                $('#warnings-validation').html(
                    '<div class="alert alert-error">'+
                        '<button class="close" data-dismiss="alert">×</button>'+
                        '{t}Paypal API authentication is incorrect. Please try again.{/t}'+
                    '</div>'
                );
                $('#loading_image').hide();
            });
        });

        $('#recurring_checkbox').on('change', function(e, ui) {
            var checkbox = $(this);

            if (checkbox.is(':checked')) {
                $('.recurring-paypal-help').show();
            } else {
                $('.recurring-paypal-help').hide();
            }
        })

    });
</script>
{/block}
