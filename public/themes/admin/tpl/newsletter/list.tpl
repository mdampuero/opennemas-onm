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
            <li>
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
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.title" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs ng-cloak">
            <ui-select name="view" theme="select2" ng-model="criteria.epp">
              <ui-select-match>
                <strong>{t}View{/t}:</strong> [% $select.selected %]
              </ui-select-match>
              <ui-select-choices repeat="item in views  | filter: $select.search">
                <div ng-bind-html="item | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
        </ul>
        <ul class="nav quick-section pull-right ng-cloak" ng-if="items.length > 0">
          <li class="quicklinks hidden-xs">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-body no-padding">
        <uib-tabset active="active">
          <uib-tab heading="{t}Sendings{/t}" ng-click="selectType(0)">

            <div class="listing-no-contents ng-cloak" ng-hide="!flags.http.loading">
              <div class="text-center p-b-15 p-t-15" ng-show="selectedType == 0">
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

            <div class="table-wrapper ng-cloak" ng-if="!flags.http.loading && items.length > 0">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th class="hidden-xs hidden-sm text-center">{t}Sent{/t}</th>
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
                      <div class="listing-inline-actions">
                        <a class="btn btn-default btn-small" href="[% routing.generate('backend_newsletters_show_contents', { id: item.id }) %]" title="{t}Edit{/t}" >
                          <i class="fa fa-pencil"></i> {t}Edit{/t}
                        </a>
                        <a class="btn btn-primary btn-small" href="[% routing.generate('backend_newsletters_preview', { id: item.id }) %]" title="{t}Preview{/t}">
                          <i class="fa fa-eye"></i>
                          {t}Preview{/t}
                        </a>
                        <button class="btn btn-danger btn-small" ng-if="item.sent_items < 1" class="link link-danger" ng-click="delete(item.id)" type="button">
                          <i class="fa fa-trash-o"></i>
                          {t}Delete{/t}
                        </button>
                      </div>
                    </td>
                    <td class="hidden-xs hidden-sm text-center">
                      <div>
                        <i class="fa fa-check text-success" ng-show="item.sent_items != 0"></i>
                        <i class="fa fa-inbox" ng-show="item.sent_items == 0"></i>
                        <i class="fa fa-clock text-info" ng-show="item.sent_items == 0"></i>
                      </div>
                      [% item.sent_items != 0 ? (item.sent | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}') : '{t}Not sent{/t}' %]
                      <div ng-show="item.sent_items != 0">{t 1="[% item.sent_items %]"}%1 sent items{/t}</div>
                    </td>
                    <td class="right">

                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </uib-tab>

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
                  <th class="hidden-xs hidden-sm">{t}Schedule{/t}</th>
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
                    <div class="listing-inline-actions">
                      <a class="btn btn-default btn-small" href="[% routing.generate('backend_newsletter_template_show', { id: item.id }) %]" title="{t}Edit{/t}" >
                        <i class="fa fa-pencil"></i> {t}Edit{/t}
                      </a>
                      <button class="btn btn-danger btn-small" ng-if="item.sent_items < 1" class="link link-danger" ng-click="delete(item.id)" type="button">
                        <i class="fa fa-trash-o"></i>
                        {t}Delete{/t}
                      </button>
                    </div>
                  </td>
                  <td class="hidden-xs hidden-sm">
                    [% item.schedule %]
                  </td>
                </tr>
              </tbody>
            </table>
          </uib-tab>
        </uib-tabset>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="!flags.http.loading && items.length > 0">
        <div class="pull-right">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-delete">
    {include file="base/modal/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-confirm">
    {include file="user/modal.confirm.tpl"}
  </script>
</div>
{/block}
