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
                {t}Account settings{/t}
              </h4>
            </li>
          </ul>
        </div>
      </div>
    </div>
    {render_messages}
    <div class="row" id="info-page" >
      <div class="col-xs-12 col-sm-8">
        <div class="row">
          <div class="col-xs-12 m-b-15">
            <div class="tiles white">
              <div class="tiles green">
                <div class="tiles-body">
                  <br>
                  <br>
                  <br>
                  <br>
                  <br>
                  <br>
                  <h3 class="text-white semi-bold">{$instance->name}</h3>
                  <h5 class="text-white ">
                    <i class="fa fa-globe"></i>
                    {implode(', ',$instance->domains)}
                  </h5>
                </div>
                <div class="tile-footer clearfix">
                  <h6 class="no-margin pull-left">
                    <a class="text-white" href="mailto://{$instance->contact_mail}">
                      <i class="fa fa-envelope"></i>
                      {$instance->contact_mail}
                    </a>
                  </h6>
                  <span class="pull-right">
                    <i class="fa fa-calendar"></i>
                    {$instance->created}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-4">
        <div class="row">
          <div class="col-md-6">
            <div class="tiles purple m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Media size{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last transparent">
                    <span class="item-count">{$instance->media_size|string_format:"%.2f"} MB</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tiles red m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Support plan{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last transparent">
                    <div class="item-count">
                      {$instance->support_plan}
                    </div>
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
                <div class="widget-stats">
                  <div class="wrapper transparent">
                    <span class="item-title">{t}Available{/t}</span>
                    <span class="item-count">{$max_users - $instance->users}</span>
                  </div>
                </div>
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
            <div class="clearfix b-grey b-b tiles-body">
              <div class="pull-left">
                <h4>{t}Plans & Modules{/t}</h4>
                <p class="hidden-xs">{t}Here you can see a list of activated modules by plan{/t}</p>
              </div>
              <div class="upgrade pull-right">
                <button class="btn btn-large btn-success" ng-disabled="hasChanges || !changed()" type="submit">
                  <span ng-if="!hasChanges">{t}Upgrade{/t}</span>
                  <span class="ng-cloak" ng-if="hasChanges">{t}Waiting for upgrade{/t}</span>
                </button>
              </div>
            </div>
            <div class="tiles-body" style="overflow: auto;" ng-init="hasChanges = ({$hasChanges} ? 1: 0 );instance = {json_encode($instance)|replace:'"':'\''};plans = {$plans};modules = {$available_modules}">
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
          </div>
          <input name="hasChanges" ng-value="hasChanges" type="hidden">
          <input name="modules" ng-value="activatedModules" type="hidden">
        </form>
      </div>
    </div>
  </div>
{/block}

