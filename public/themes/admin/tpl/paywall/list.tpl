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
        <li class="quicklinks hidden-xs">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs">
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


      <div class="well">
        {t}The paywall allows you to monetize your contents by enabling a subscription enabled model access to your contents.{/t}
      </div>

      <div class="row">
        <div class="col-sm-6">
          <div class="grid simple">
            <div class="grid-title">
              <h4><span class="fa fa-users"></span> {t}Premium users{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="statistic-element">
                <div class="element-header">{t}Subscribed users{/t}</div>
                <div class="number">{$count_users_paywall}</div>
              </div>

              {include file="paywall/partials/user_listing.tpl"}
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="grid simple">
            <div class="grid-title">
              <h4><span class="fa fa-dollar"></span> {t}Lastest purchases{/t}</h4>
            </div>

            <div class="grid-body">
              <div class="statistic-element purchases">
                <div class="element-header">{t}Purchases last month{/t}</div>
                <div class="number">{$count_purchases_last_month}</div>
              </div>

              {include file="paywall/partials/purchases_listing.tpl"}
            </div>
          </div>
        </div>
      </div>

    </div>
    {/block}
