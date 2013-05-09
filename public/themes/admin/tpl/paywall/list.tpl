{extends file="base/admin.tpl"}

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

    .premium-users h3, .latest-purchases h3 {
        display:inline-block;
    }
    .more {
        float:right;
        display:inline-block;
        margin-top:20px;
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

            <h3>{t}Premium users{/t}</h3>

            <a href="{url name=admin_paywall_users}" class="more">{t}Show all…{/t}</a>

            {include file="paywall/partials/user_listing.tpl"}
        </div>

        <div class="latest-purchases">

            <h3>{t}Lastest purchases{/t}</h3>

            <a href="{url name=admin_paywall_purchases}" class="more">{t}Show all…{/t}</a>

            {include file="paywall/partials/purchases_listing.tpl"}

        </div>

    </div>
</form>
{/block}
