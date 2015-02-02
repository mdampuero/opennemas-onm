{extends file="base/admin.tpl"}

{block name="header-css" append}
<style>

</style>
{/block}

{block name="content"}
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-paypal"></i>
                        {t}Paywall{/t}
                    </h4>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <h4>{t}Statistics{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name=admin_paywall_settings}" class="btn btn-primary" title="{t}Config newsletter module{/t}">
                            <span class="fa fa-cog"></span>
                            {t}Settings{/t}
                        </a>
                    </li>
            </div>
        </div>
    </div>
</div>
<div class="content paywall">

    {render_messages}

    <div class="premium-users grid simple col-md-6">
        <div class="grid-body">
            <h4><span class="fa fa-users"></span> {t}Premium users{/t}</h4>
            <hr>

            <div class="statistic-element">
                <div class="header">{t}Subscribed users{/t}</div>
                <div class="number">{$count_users_paywall}</div>
            </div>

            {include file="paywall/partials/user_listing.tpl"}
            <a class="pull-right" href="{url name=admin_paywall_users}" class="more">{t}Show all…{/t}</a>
        </div>
    </div>

    <div class="latest-purchases grid simple col-md-6">

        <div class="grid-body">
            <h4><span class="fa fa-users"></span> {t}Lastest purchases{/t}</h4>
            <hr>

            <div class="statistic-element purchases">
                <div class="header">{t}Purchases last month{/t}</div>
                <div class="number">{$count_purchases_last_month}</div>
            </div>

            {include file="paywall/partials/purchases_listing.tpl"}

            <a href="{url name=admin_paywall_purchases}" class="pull-right">{t}Show all…{/t}</a>
        </div>

    </div>

</div>
{/block}
