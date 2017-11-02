{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="NewsletterSubscriptorListCtrl" ng-init="criteria = { epp: 10, orderBy: { name: 'asc' }, page: 1 }; init(null, 'backend_ws_newsletter_subscriptors')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4 class="hidden-xs">
              <i class="fa fa-home fa-lg"></i>
              {t}Newsletters{/t}
            </h4>
            <h4 class="visible-xs">{t}Subscriptions{/t}</h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Subscriptions{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_newsletters}" title="{t}Go back to newsletter manager{/t}">
                <span class="fa fa-reply"></span>
                <span class="hidden-xs">{t}Newsletters{/t}</span>
              </a>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <a href="{url name=admin_newsletter_subscriptor_create}" class="btn btn-primary" accesskey="N">
                <span class="fa fa-plus"></span>
                {t}Create{/t}
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
              <i class="fa fa-arrow-left fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <h4>
              [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
            </h4>
          </li>
        </ul>
        <ul class="nav quick-section pull-right">
          <li class="quicklinks">
            <button class="btn btn-link batchSubscribeButton" ng-click="updateSelectedItems('backend_ws_newsletter_subscriptors_batch_activated', 'status', 2, 'loading')" uib-tooltip="{t}Activate{/t}" tooltip-placement="bottom">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link batchSubscribeButton" ng-click="updateSelectedItems('backend_ws_newsletter_subscriptors_batch_activated', 'status', 3, 'loading')" uib-tooltip="{t}Deactivate{/t}" tooltip-placement="bottom">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link batchSubscribeButton" ng-click="updateSelectedItems('backend_ws_newsletter_subscriptors_batch_subscribe', 'subscription', 1, 'loading_sub')" uib-tooltip="{t}Subscribe{/t}" tooltip-placement="bottom">
              <i class="fa fa-envelope fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_newsletter_subscriptors_batch_subscribe', 'subscription', 0, 'loading_sub')" uib-tooltip="{t}Unsubscribe{/t}" tooltip-placement="bottom">
              <i class="fa fa-envelope fa-lg"></i>
              <i class="fa fa-times fa-sub text-danger"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
              <span class="fa fa-trash-o fa-lg"></span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.name" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by name or email{/t}" type="search"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs ng-cloak" ng-init="activated = [ { name: '{t}All{/t}', value: null }, { name: '{t}Yes{/t}', value: 2 }, { name: '{t}No{/t}', value: 3 } ]">
            <ui-select name="filters[status]" theme="select2" ng-model="criteria.status">
              <ui-select-match>
                <strong>{t}Activated{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in activated | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks hidden-xs ng-cloak" ng-init="status = [ { name: '{t}All{/t}', value: null }, { name: '{t}Subscribed{/t}', value: 1 }, { name: '{t}No subscribed{/t}', value: 0 } ]">
            <ui-select name="filters[subscription]" theme="select2" ng-model="criteria.subscription">
              <ui-select-match>
                <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks hidden-sm hidden-xs ng-cloak">
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
        <ul class="nav quick-section pull-right ng-cloak" ng-if="contents.length > 0">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
        </ul>
      </div>
    </div>
  </div>

  <div class="content">


    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="table-wrapper ng-cloak">
          <table class="table table-hover no-margin" ng-if="!loading">
            <thead>
              <thead>
                <tr>
                  <th style="width:5px">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th>{t}Name{/t}</th>
                  <th>{t}Email{/t}</th>
                  <th class="left">{t}Status{/t}</th>
                  <th class="center" style="width:10px">{t}Activated{/t}</th>
                  <th class="center" style="width:10px">{t}Subscribed{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-if="contents.length == 0">
                  <td class="empty" colspan="10">{t}No available subscribers.{/t}</td>
                </tr>
                <tr ng-if="contents.length >= 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td class="left">
                    [% content.firstname %]&nbsp;[% content.lastname %] [% content.name %]
                    <span class="visible-xs">([% content.email %])</span>
                    <div class="listing-inline-actions">
                      <a class="link" href="[% edit(content.id, 'admin_newsletter_subscriptor_show') %]" title="{t}Edit user{/t}">
                        <i class="fa fa-pencil"></i> {t}Edit{/t}
                      </a>
                      <button type="button" class="link link-danger" title="{t}Delete user{/t}" ng-click="delete(content)">
                        <i class="icon-white fa fa-trash"></i> {t}Remove{/t}
                      </button>
                  </div>
                </td>
                <td class="left hidden-xs">
                  [% content.email %]
                </td>
                <td class="left">
                  <span ng-if="content.status == 0">{t}Mail sent.Waiting for user{/t}</span>
                  <span ng-if="content.status == 1">{t}Accepted by user{/t}</span>
                  <span ng-if="content.status == 2">{t}Accepted by administrator{/t}</span>
                  <span ng-if="content.status == 3">{t}Disabled by administrator{/t}</span>
                </td>
                <td class="center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_newsletter_subscriptor_toggle_activated', 'status', content.status != 1 ? 1 : 0, 'loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && (content.status == '1' || content.status == '2'), 'fa-times text-error': !content.loading && (content.status == '0' || content.status == '3') }"></i>
                  </button>
                </td>
                <td class="center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_newsletter_subscriptor_toggle_subscription', 'subscription', content.subscription != 1 ? 1 : 0, 'loading_sub')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading_sub, 'fa-check text-success' : !content.loading_sub && content.subscription == '1', 'fa-times text-error': !content.loading_sub && content.subscription == '0' }"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
        <div class="pull-right">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-delete">
    {include file="newsletter/subscriptions/modals/_modalDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="newsletter/subscriptions/modals/_modalBatchDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
</div>
{/block}
