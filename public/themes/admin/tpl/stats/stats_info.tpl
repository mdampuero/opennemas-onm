{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="@AdminTheme/less/_account.less" filters="cssrewrite,less"}
    <link rel="stylesheet" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="content"}
<div class="content my-account-page" ng-controller="AccountCtrl" ng-init="init({json_encode($instance)|clear_json}, {json_encode($plans)|clear_json}, {json_encode($available_modules)|clear_json})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-home fa-lg"></i>
                {t}My account{/t}
              </h4>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="row" id="info-page" >
      <div class="col-xs-12 col-sm-7">
        <div class="row instance-info">
          <div class="col-xs-12 m-b-15">
            <div class="tiles white">
              <div class="tiles green body-wrapper">
                <div class="tiles-body">
                  <div class="instance-name-wrapper">
                    <h3 class="text-white semi-bold">{$instance->name}</h3>
                    {foreach $instance->domains as $domain}
                    <h5 class="text-white">
                      <i class="fa fa-globe"></i>
                      <a href="http://{$domain}" target="_" class="text-white">{$domain}</a>
                    </h5>
                    {/foreach}
                  </div>
                </div>
                <div class="tile-footer clearfix">
                  <div class="row">
                    <a class="text-white contact-email col-xs-12 col-md-6" href="mailto:{$instance->contact_mail}" uib-tooltip="{t}This is the email used to create your newspaper{/t}" tooltip-placement="bottom">
                      <i class="fa fa-envelope"></i>
                      {$instance->contact_mail}
                    </a>
                    <a href="#" class="text-white created-at col-xs-12 col-md-6">
                      <i class="fa fa-calendar"></i>
                      <span uib-tooltip="{t 1=$instance->created}Your newspaper was created on %1{/t}" tooltip-placement="bottom">{$instance->created}</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-5">
        <div class="row">
          <div class="col-md-6">
            <div class="tiles red m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Plan{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last">
                    <div class="item-count ng-cloak">
                      <span ng-if="countActivatedModulesForPlan('Profesional') == 0 || countActivatedModulesForPlan('Gold') == 0 || countActivatedModulesForPlan('Other') == 0">{t}Base{/t}</span>
                      <span ng-if="countActivatedModulesForPlan('Profesional') > 0 || countActivatedModulesForPlan('Gold') > 0 || countActivatedModulesForPlan('Other') > 0">{t}Base + Modules{/t}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tiles green m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Support plan{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last">
                    <div class="item-count">
                      {$instance->support_plan} <i class="fa fa-info-circle" uib-tooltip="{t}Support by tickets{/t}" tooltip-placement="bottom"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="tiles purple m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Storage size{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last">
                    <span class="item-count">{$instance->media_size|string_format:"%.2f"} MB</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tiles yellow m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Page views this month{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last">
                    <span class="item-count">
                      {t}coming soon... work in progress...{/t}
                      <!-- {$instance->page_views|number_format}
                      <i class="fa fa-info-circle" uib-tooltip="{t}Note: this number has not being used for billing purpose. Billing pageviews count goes from 26th to 25th of each month.{/t}" tooltip-placement="left"></i> -->
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <div class="tiles blue m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Users{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper">
                    <span class="item-title">{t}Activated{/t}</span>
                    <span class="item-count">{$instance->users}</span>
                  </div>
                </div>
                <!-- <div class="widget-stats">
                  <div class="wrapper">
                    <span class="item-title">{t}Available{/t}</span>
                    <span class="item-count">{$max_users - $instance->users}</span>
                  </div>
                </div> -->
                <div class="widget-stats">
                  <div class="wrapper last">
                    <span class="item-title">{t}Max{/t}</span>
                    <span class="item-count">{$max_users}</span>
                  </div>
                </div>
                {if $max_users > 0}
                <div class="progress progress-small no-radius m-t-20" style="width:90%">
                  <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="{($instance->users * 100) / $max_users}%" style="width: {($instance->users * 100) / $max_users}%;"></div>
                </div>
                <div class="description">
                  <span class="text-white mini-description ">
                    {($instance->users * 100) / $max_users}%
                    <span class="blend">of total</span>
                  </span>
                </div>
                {/if}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-xs-12 m-b-15">
        <div class="tiles white">
          <div class="tiles-body clearfix">
            <div>
              <div class="more-plans clearfix">
                <p class="col-xs-12 col-md-8">{t}Opennemas offers many more modules and solutions{/t}</p>
                <a href="{url name=admin_store_list}" target="_blank" class="btn btn-primary btn-large col-xs-12 col-md-4">
                  {t}Check out our modules{/t}
                </a>
              </div>
              <div class="get-help clearfix">
                <p class="col-xs-12 col-md-8">{t}If you need a custom plan or you want to purchase a plan or module please click in the next link:{/t}</p>
                <a href="mailto:sales@openhost.es" class="btn btn-white btn-large col-xs-12 col-md-4">
                  {t}Contact Us{/t}
                </a>
              </div>
            </div>
          </div>
        </div>
        <input name="hasChanges" ng-value="hasChanges" type="hidden">
        <input name="modules" ng-value="activatedModules" type="hidden">
      </div>
      <div class="col-sm-6 col-xs-12 m-b-15">
        <div class="tiles white clearfix">
          <div class="tiles-body">
            <div class="tiles-title text-uppercase text-black">
              {t}Billing information{/t}
            </div>
            {if !empty($instance->metas) && array_key_exists('billing_name', $instance->metas) && !empty($instance->metas['billing_name'])}
              <div class="row p-b-15 p-t-15">
                <div class="col-sm-6">
                  <strong>{t}Name{/t}:</strong> {$instance->metas['billing_name']}
                </div>
                <div class="col-sm-6" ng-if="billing_company_name">
                  <strong>{t}Company{/t}:</strong> {$instance->metas['billing_company']}
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-6">
                  <strong>{t}VAT{/t}</strong> {$instance->metas['billing_vat']}
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-6">
                  <strong>{t}Email{/t}:</strong> {$instance->metas['billing_email']}
                </div>
                <div class="col-sm-6">
                  <strong>{t}Phone{/t}:</strong> {$instance->metas['billing_phone']}
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-8">
                  <strong>{t}Address{/t}:</strong> {$instance->metas['billing_address']}
                </div>
                <div class="col-sm-4">
                  <strong>{t}Postal code{/t}:</strong> {$instance->metas['billing_postal_code']}
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-4">
                  <strong>{t}City{/t}:</strong> {$instance->metas['billing_city']}
                </div>
                <div class="col-sm-4">
                  <strong>{t}State{/t}:</strong> {$instance->metas['billing_state']}
                </div>
                <div class="col-sm-4">
                  <strong>{t}Country{/t}:</strong> {$instance->metas['billing_country']}
                </div>
              </div>
              <div class="row p-t-15">
                <div class="col-md-12">
                  <h5>{t escape=off}Something wrong? Contact our <a href="javascript:UserVoice.showPopupWidget();">support team</a>.{/t}</h5>
                </div>
              </div>
            {else}
              <h4 class="p-t-30 text-center">{t}You have no billing information{/t}</h4>
              <h5 class="p-b-30 text-center">{t escape=off}You will be asked to add it during the checkout in our store{/t}</h5>
            {/if}
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 m-b-15 ng-cloak">
        <div class="tiles white">
          <div class="tiles-body" style="overflow: auto;">
            <div class="tiles-title text-uppercase text-black">
              {t}Activated plans & modules{/t}
            </div>
            {*<div class="upgrade pull-right hidden">
              <button class="btn btn-large btn-success" ng-disabled="hasChanges || !changed()" type="submit">
                <span ng-if="!hasChanges">{t}Upgrade{/t}</span>
                <span class="ng-cloak" ng-if="hasChanges">{t}Waiting for upgrade{/t}</span>
              </button>
            </div>*}
            <div class="plans-wrapper">
              <div class="plan-wrapper" ng-repeat="plan in plans" ng-if="countActivatedModulesForPlan(plan.id)" >
                <h5 class="plan-title">
                  [% plan.title %]
                </h5>
                <div ng-repeat="item in getActivatedModulesForPlan(plan.id)" style="display:inline-block; margin-right:5px" class="module-activated">
                  [% item.name %]
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

