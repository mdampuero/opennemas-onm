{extends file="base/admin.tpl"}
{block name="content"}
<div ng-controller="NewsletterListCtrl" ng-init="init();">
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
              <a class="no-padding" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
                {t}Newsletters{/t}
              </a>
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=backend_newsletters_config}" class="admin_add" title="{t}Config newsletter module{/t}">
                <span class="fa fa-cog fa-lg"></span>
              </a>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <a class="btn btn-primary" ng-show="selectedType == 0" href="{url name=backend_newsletters_create}" accesskey="N" tabindex="1" id="create-button">
                <i class="fa fa-plus"></i>
                {t}Create{/t}
              </a>
              <a class="btn btn-primary ng-cloak" ng-show="selectedType == 1" href="{url name=backend_newsletter_template_create}" accesskey="N" tabindex="1" id="create-button">
                <i class="fa fa-plus"></i>
                {t}Create template{/t}
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section filter-components">
          <li class="m-r-10 quicklinks">
            <div class="input-group input-group-animated">
              <span class="input-group-addon">
                <i class="fa fa-search fa-lg"></i>
              </span>
              <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.title }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.title" placeholder="{t}Search{/t}" type="text">
              <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('title')" ng-show="criteria.title">
                <i class="fa fa-times"></i>
              </span>
            </div>
          </li>
        </ul>
        <ul class="nav quick-section quick-section-fixed ng-cloak" ng-if="items.length > 0">
          <li class="quicklinks">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="message"><div class="alert alert-info">{$message}</div></div>
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
            <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && items.length == 0 && selectedType == 0">
              <div class="text-center p-b-15 p-t-15">
                <i class="fa fa-4x fa-warning text-warning"></i>
                <h3>{t}Unable to find any item that matches your search.{/t}</h3>
                <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
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
                  <tr ng-repeat="item in items">
                    <td>
                      <div ng-if="item.title != ''">[% item.title %]</div>
                      <div ng-if="item.title == ''">{t}Newsletter{/t}  -  [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]</div>
                      <div class="small-text">
                        <strong>{t}Created:{/t}</strong> [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %] <br>
                        <strong>{t}Updated:{/t}</strong> [% item.updated | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                      </div>
                      <div class="listing-inline-actions btn-group">
                        <a class="btn btn-white btn-small" ng-if="item.sent_items == 0 && item.send_items != -1" href="[% routing.generate('backend_newsletters_show_contents', { id: item.id }) %]" title="{t}Edit{/t}" uib-tooltip="{t}Edit{/t}" tooltip-placement="top" >
                          <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-white btn-small" href="[% routing.generate('backend_newsletters_preview', { id: item.id }) %]" title="{t}Preview{/t}" uib-tooltip="{t}Preview{/t}" tooltip-placement="top">
                          <i class="fa fa-eye text-primary"></i>
                        </a>
                        <button class="btn btn-white btn-small" ng-if="item.sent_items == 0 && item.send_items != -1" class="link link-danger" ng-click="delete(item.id)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
                          <i class="fa fa-trash-o text-danger"></i>
                        </button>
                      </div>
                    </td>
                    <td class="hidden-xs text-center">
                      <div>
                        <i class="fa fa-check text-success" ng-show="item.sent_items != 0 && item.sent_items != -1"></i>
                        <i class="fa fa-cogs text-info" ng-show="item.sent_items == -1"></i>
                        <i class="fa fa-inbox" ng-show="item.sent_items == 0"></i>
                        <i class="fa fa-clock text-info" ng-show="item.sent_items == 0"></i>
                      </div>
                      [% item.sent_items != 0 && item.sent_items != -1? (item.sent | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' ) : ''  %]
                      <span ng-show="item.sent_items == 0">{t}Not sent{/t}</span>
                      <div ng-show="item.sent_items != 0 && item.sent_items != -1">{t 1="[% item.sent_items %]"}%1 sent items{/t}</div>
                      <div ng-show="item.sent_items == -1">{t}Sending{/t}</div>
                    </td>
                    <td class="right"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </uib-tab>
        {is_module_activated name="es.openhost.module.newsletter_scheduling"}
          <uib-tab heading="{t}Schedules{/t}" ng-click="selectType(1)">
            <div class="listing-no-contents ng-cloak" ng-hide="!flags.http.loading">
              <div class="text-center p-b-15 p-t-15" ng-show="selectedType == 1">
                <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
                <h3 class="spinner-text">{t}Loading{/t}...</h3>
              </div>
            </div>
            <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && items.length == 0 && selectedType == 1">
              <div class="text-center p-b-15 p-t-15">
                <i class="fa fa-4x fa-warning text-warning"></i>
                <h3>{t}Unable to find any item that matches your search.{/t}</h3>
                <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
              </div>
            </div>
            <table class="table table-hover ng-cloak no-margin" ng-if="!flags.http.loading && items.length > 0">
              <thead>
                <tr>
                  <th>{t}Title{/t}</th>
                  <th class="hidden-xs">{t}Schedule{/t}</th>
                  <th class="text-right">{t}Enabled{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="item in items">
                  <td>
                    <div ng-if="item.title != ''">[% item.title %]</div>
                    <div ng-if="item.title == ''">{t}Newsletter{/t}  -  [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]</div>
                    <div class="small-text">
                      <strong>{t}Created:{/t}</strong> [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %] <br>
                    </div>
                    <div class="listing-inline-actions btn-group">
                      <a class="btn btn-white btn-small" href="[% routing.generate('backend_newsletter_template_show', { id: item.id }) %]" title="{t}Edit{/t}" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
                        <i class="fa fa-pencil"></i>
                      </a>
                      <button class="btn btn-white btn-small" ng-if="item.sent_items < 1" class="link link-danger" ng-click="delete(item.id)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
                        <i class="fa fa-trash-o text-danger"></i>
                      </button>
                    </div>
                  </td>
                  <td class="hidden-xs">
                    <span class="days">
                      Days:
                      <span ng-show="item.schedule.days.length > 0" class="badge badge-default m-r-10" ng-repeat="day in item.schedule.days | orderBy:days">[% data.extra.days[day - 1] %]</span>
                      <span ng-show="item.schedule.days.length <= 0" class="badge badge-default">{t}Not set{/t}</span>
                    </span>
                    <br>
                    <span class="hours">
                      Hours:
                      <span ng-show="item.schedule.hours.length > 0" class="badge badge-default m-r-10" ng-repeat="hour in item.schedule.hours">[% hour %]</span>
                      <span ng-show="item.schedule.hours.length <= 0" class="badge badge-danger">{t}Not set{/t}</span>
                    </span>
                  </td>
                  <td class="text-right">
                    <button class="btn btn-white" ng-click="patch(item, 'status', item.status != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.statusLoading, 'fa-check text-success' : !item.statusLoading && item.status == 1, 'fa-times text-error': !item.statusLoading && item.status == 0 }"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </uib-tab>
        </uib-tabset>
        {/is_module_activated}
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
</div>
{/block}
