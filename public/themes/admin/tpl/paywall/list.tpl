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
    .statistics .purchases {
        float:right;
    }
    .statistic-element {
        padding-top: 5px;
        color: #5C5C5C;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 14px;
        line-height: 20px;
        text-align: justify;
        display:inline-block;
        border:1px solid #ccc;
        border-radius:5px;
        padding:30px 40px;
        margin-right:5px;
    }
    .statistic-element .header {
        margin: 0 0 6px;
        line-height: 1.1;
        text-transform: uppercase;
        font-size: 15px;
        color: #999;
        font-family: "HelveticaNeue-CondensedBold", "Helvetica Neue", "Arial Narrow", Arial, sans-serif;
        font-weight: bold;
        font-stretch: condensed;
        -webkit-font-smoothing: antialiased;
    }
    .statistic-element .number {
        font-family: "HelveticaNeue-CondensedBold", "Helvetica Neue", "Arial Narrow", Arial, sans-serif;
        font-weight: bold;
        font-stretch: condensed;
        -webkit-font-smoothing: antialiased;
        line-height: 0.7;
        color: #333;
        font-size: 52px;
        text-align:right;
    }

    .premium-users, .latest-purchases {
        display:inline-block;
        width:49%;
    }
    .premium-users {
        margin-right:10px;
    }
</style>
{/block}

{block name="content"}
<form action="{url name=admin_paywall_settings_save}" method="post">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Paywall{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{url name=admin_paywall_settings}" class="admin_add" title="{t}Config newsletter module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Settings{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content clearfix">

        {render_messages}

        <div class="statistics">
            <div class="statistic-element">
                <div class="header">{t}Subscribed users{/t}</div>
                <div class="number">{$count_users_paywall}</div>
            </div>

            <div class="statistic-element purchases">
                <div class="header">{t}Purchases last month{/t}</div>
                <div class="number">{$count_purchases_last_month}</div>
            </div>
        </div>


        <div class="premium-users">
            <table class="table table-hover table-condensed">

                <h3>{t}Premium users{/t}</h3>

                {if count($users) > 0}
                <thead>
                    <tr>
                        <th class="center">{t}User id{/t}</th>
                        <th class="right">{t}User name{/t}</th>
                        <th class="right">{t}End of subscription{/t}</th>
                    </tr>
                </thead>
                {else}
                <thead>
                    <tr>
                        <th colspan="11">
                            &nbsp;
                        </th>

                    </tr>
                </thead>
                {/if}
                <tbody class="sortable">
                {foreach from=$users item=user}
                <tr data-id="{$user->id}">
                    <td class="center">
                        {$user->id|clearslash}
                    </td>
                    <td class="right">
                        {$user->name|clearslash}
                    </td>
                    <td class="right">
                        {$user->meta['paywall_time_limit']|clearslash}
                    </td>

                </tr>
                {foreachelse}
                <tr>
                    <td class="empty" colspan="11">{t}No users with paywall{/t}</td>
                </tr>
                {/foreach}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="11" class="center">
                            <div class="pagination">
                                {*$pagination->links*}
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="latest-purchases">
            <table class="table table-hover table-condensed">

                <h3>{t}Lastest purchases{/t}</h3>

                {if count($purchases) > 0}
                <thead>
                    <tr>
                        <th class="left">{t}Order id{/t}</th>
                        <th class="left">{t}Created{/t}</th>
                        <th class="left">{t}User{/t}</th>
                    </tr>
                </thead>
                {else}
                <thead>
                    <tr>
                        <th colspan="11">
                            &nbsp;
                        </th>

                    </tr>
                </thead>
                {/if}
                <tbody class="sortable">
                {foreach from=$purchases item=purchase}
                <tr data-id="{$order->id}">
                    <td class="left">
                        {$purchase->payment_id|clearslash}
                    </td>
                    <td class="left">
                        {$purchase->created}
                    </td>
                    <td class="left">
                        {$purchase->id|clearslash}
                    </td>

                </tr>
                {foreachelse}
                <tr>
                    <td class="empty" colspan="11">{t}No purchases available{/t}</td>
                </tr>
                {/foreach}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="11" class="center">
                            <div class="pagination">
                                {*$pagination->links*}
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</form>
{/block}
