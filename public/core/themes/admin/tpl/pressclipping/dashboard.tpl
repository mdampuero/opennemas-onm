{extends file="base/admin.tpl"}
{block name="content"}
<div ng-controller="PressClippingCtrl" ng-init="init();">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-envelope m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_pressclipping_dashboard}" title="{t}Go back to list{/t}">
                {t}PressClipping{/t}
              </a>
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=backend_pressclipping_settings}" class="admin_add" title="{t}Config newsletter module{/t}">
                <span class="fa fa-cog fa-lg"></span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">

    <div class="grid simple">
      <div class="grid-body no-padding">
          {is_module_activated name="es.openhost.module.newsletter_scheduling"}
          <uib-tabset active="active">
            <uib-tab heading="{t}Sendings{/t}" ng-click="selectType(0)">
          {/is_module_activated}
            <div class="listing-no-contents ng-cloak" ng-hide="!flags.http.loading">
              <div class="text-center p-b-15 p-t-15" ng-show="selectedType == 0">
                <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
                <h3 class="spinner-text">{t}Loading{/t}...</h3>
              </div>
            </div>
            <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && items.length == 0">
              <div class="text-center p-b-15 p-t-15">
                <i class="fa fa-4x fa-warning text-warning"></i>
                <h3>{t}Unable to find any item that matches your search.{/t}</h3>
                <h4>{t}Maybe changing any filter could help.{/t}</h4>
              </div>
            </div>
            <div class="table-wrapper ng-cloak" ng-if="!flags.http.loading && items.length > 0">
              <table class="table table-hover no-margin">
                <thead>
                    <tr>
                      <th>{t}Title{/t}</th>
                      <th class="hidden-xs hidden-xs text-center">{t}Sent{/t}</th>
                    </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="item in data.items">
                    <td>
                      <div ng-if="item.title != ''">[% item.title %]</div>
                      <div class="small-text">
                        <strong>{t}Created:{/t}</strong> [% item.send_date | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %] <br>
                      </div>
                    </td>
                    <td class="hidden-xs text-center">
                      <div>
                        <i class="fa fa-check text-success" ng-show="item.status != 0 && item.status != -1"></i>
                        <i class="fa fa-cogs text-info" ng-show="item.status == -1"></i>
                        <i class="fa fa-inbox" ng-show="item.status == 0"></i>
                        <i class="fa fa-clock text-info" ng-show="item.status == 0"></i>
                      </div>
                      <div ng-show="item.status == 0">{t}Not sent{/t}</div>
                      <div ng-show="item.status != 0 && item.status != -1">{t}Sent{/t}</div>
                      <div ng-show="item.status == -1">{t}Sending{/t}</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </uib-tab>
      </div>
    </div>
  </div>
</div>
{/block}
