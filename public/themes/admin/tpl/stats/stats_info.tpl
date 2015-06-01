{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="@AdminTheme/less/_account.less" filters="cssrewrite,less"}
    <link rel="stylesheet" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="content"}
  <div class="content my-account-page" ng-app="BackendApp" ng-controller="MyAccountCtrl">
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
    {render_messages}
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
                    <a class="text-white contact-email col-xs-12 col-md-6" href="mailto:{$instance->contact_mail}" tooltip="{t}This is the email used to create your newspaper{/t}" tooltip-placement="bottom">
                      <i class="fa fa-envelope"></i>
                      {$instance->contact_mail}
                    </a>
                    <a href="#" class="text-white created-at col-xs-12 col-md-6">
                      <i class="fa fa-calendar"></i>
                      <span tooltip="{t 1=$instance->created}Your newspaper was created on %1{/t}" tooltip-placement="bottom">{$instance->created}</span>
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
                  <div class="wrapper last transparent">
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
                  <div class="wrapper last transparent">
                    <div class="item-count">
                      {$instance->support_plan} <i class="fa fa-info-circle" tooltip="{t}Support by tickets{/t}" tooltip-placement="bottom"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="tiles purple m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Storage size{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last transparent">
                    <span class="item-count">{$instance->media_size|string_format:"%.2f"} MB</span>
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
                  <div class="wrapper transparent">
                    <span class="item-title">{t}Activated{/t}</span>
                    <span class="item-count">{$instance->users}</span>
                  </div>
                </div>
                <!-- <div class="widget-stats">
                  <div class="wrapper transparent">
                    <span class="item-title">{t}Available{/t}</span>
                    <span class="item-count">{$max_users - $instance->users}</span>
                  </div>
                </div> -->
                <div class="widget-stats">
                  <div class="wrapper last transparent">
                    <span class="item-title">{t}Max{/t}</span>
                    <span class="item-count">{$max_users}</span>
                  </div>
                </div>
                {if $max_users > 0}
                <div class="progress transparent progress-small no-radius m-t-20" style="width:90%">
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
      <div class="col-xs-12 m-b-15">
        <form id="upgrade-form" method="POST" action="{url name=admin_client_send_upgrade_mail}">
          <div class="tiles white">
            <div class="tiles-body clearfix">
              <div>
                <div class="more-plans clearfix">
                  <p class="col-xs-12 col-md-8">{t}Opennemas offers many more modules and solutions{/t}</p>
                  <a href="{url name=admin_market_list}" target="_blank" class="btn btn-primary btn-large col-xs-12 col-md-4">
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
            {*
            <div class="tiles-body hidden" style="overflow: auto;" ng-init="hasChanges = ({$hasChanges} ? 1: 0 );instance = {json_encode($instance)|clear_json};plans = {$plans};modules = {$available_modules}">
              <div class="plans-wrapper">
                <div class="inline p-r-30" ng-repeat="plan in plans">
                  <div class="checkbox">
                    <input id="select_[% plan.id %]" ng-model="selected[plan.id]" ng-change="togglePlan(plan.id)" ng-disabled="plan.id == 'Base' || isBlocked(plan.id)" type="checkbox">
                    <label for="select_[% plan.id %]">
                      <h5 class="no-margin p-b-15">
                        <span id="{$plan}">[% plan.title %] ([% plan.total %])</span>
                      </h5>
                    </label>
                  </div>
                  <div ng-repeat="item in modules | filter: { plan: plan.id }">
                    <div class="checkbox">
                      <input checklist-model="changes" checklist-value="item" id="module_[% item.id %]" ng-disabled="plan.id == 'Base' || isUpgraded(item.id) || isDowngraded(item.id)" type="checkbox"/>
                      <label for="module_[% item.id %]">
                        [% item.name %]
                        <span class="text-danger" ng-if="isUpgraded(item.id)">({t}pending activation{/t})</span>
                        <span class="text-danger" ng-if="isDowngraded(item.id)">({t}pending deactivation{/t})</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            *}
          </div>
          <input name="hasChanges" ng-value="hasChanges" type="hidden">
          <input name="modules" ng-value="activatedModules" type="hidden">
        </form>
      </div>
      <div class="col-xs-12 m-b-15 ng-cloak">
        <div class="tiles white">
            <div class="clearfix b-grey b-b tiles-body">
              <div class="pull-left">
                <p class="hidden-xs">{t}Here you can see a list of your activated modules{/t}</p>
                <h4>{t}Activated plans & modules{/t}</h4>
              </div>
              {*<div class="upgrade pull-right hidden">
                <button class="btn btn-large btn-success" ng-disabled="hasChanges || !changed()" type="submit">
                  <span ng-if="!hasChanges">{t}Upgrade{/t}</span>
                  <span class="ng-cloak" ng-if="hasChanges">{t}Waiting for upgrade{/t}</span>
                </button>
              </div>*}
            </div>
            <div class="tiles-body" style="overflow: auto;" ng-init="hasChanges = ({$hasChanges} ? 1: 0 );instance = {json_encode($instance)|clear_json};plans = {$plans};modules = {$available_modules}">
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

