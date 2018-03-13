{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="SubscriberListCtrl" ng-init="list()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-user"></i>
                {t}Subscribers{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed=SUBSCRIBER_CREATE}
                <li class="quicklinks">
                  <a class="btn btn-success text-uppercase" href="{url name=backend_subscriber_show id=new}">
                    <i class="fa fa-plus"></i>
                    {t}Create{/t}
                  </a>
                </li>
              {/acl}
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
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
                [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
              </h4>
            </li>
          </ul>
          <ul class="nav quick-section pull-right">
            {acl isAllowed="SUBSCRIBER_AVAILABLE"}
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="patchSelected('activated', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-times fa-lg"></i>
                </button>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="patchSelected('activated', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-check fa-lg"></i>
                </button>
              </li>
            {/acl}
            {acl isAllowed="SUBSCRIBER_DELETE"}
              <li class="quicklinks hidden-xs">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                  <i class="fa fa-trash-o fa-lg"></i>
                </button>
              </li>
            {/acl}
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
              <input class="no-boarder" name="title" ng-model="criteria.name" placeholder="{t}Search by title{/t}" type="text"/>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
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
      <div class="listing-no-contents" ng-hide="!flags.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.loading && items.length == 0">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find any subscriber that matches your search.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-if="!flags.loading && items.length > 0">
        <div class="grid-body no-padding">
          <div class="table-wrapper">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="checkbox-cell" width="50">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th class="hidden-xs" width="50">{t}Avatar{/t}</th>
                  <th>{t}Name{/t}</th>
                  <th width="250">{t}Email{/t}</th>
                  <th width="240">{t}Subscriptions{/t}</th>
                  <th class="text-center" width="50">{t}Enabled{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-if="items.length > 0" ng-repeat="item in items" ng-class="{ row_selected: isSelected(items.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td class="text-center hidden-xs">
                    <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[item.avatar_img_id].path_img" transform="thumbnail,50,50" ng-if="item.avatar_img_id"></dynamic-image>
                    <gravatar class="gravatar" ng-model="item.email" size="40" ng-if="!item.avatar_img_id || item.avatar_img_id == 0"></gravatar>
                  </td>
                  <td class="left">
                    <span ng-if="item.name">[% item.name %]</span>
                    <i ng-if="!item.name">{t}Unknown{/t}</i>
                    <div class="listing-inline-actions">
                      <a class="link" href="[% routing.generate('backend_subscriber_show', { id: item.id }) %]" title="{t}Edit{/t}">
                        <i class="fa fa-pencil"></i> {t}Edit{/t}
                      </a>
                      <button class="link link-danger" ng-click="delete(item.id)" title="{t}Delete{/t}" type="button">
                        <i class="fa fa-trash-o"></i>
                        {t}Delete{/t}
                      </button>
                    </div>
                  </td>
                  <td>[% item.email %]</td>
                  <td>
                    <ul class="no-style">
                      <li ng-repeat="subscription in item.fk_user_group" ng-if="data.extra.subscriptions[subscription]">
                        <a class="badge text-uppercase m-b-5" ng-class="{ 'badge-danger': !data.extra.subscriptions[subscription].enabled, 'badge-success': data.extra.subscriptions[subscription].enabled }" href="[% routing.generate('backend_subscription_show', { id: subscription }) %]">
                          <strong>[% data.extra.subscriptions[subscription].name %]</strong>
                        </span>
                        </a>
                      </li>
                    </ul>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-white" ng-click="patch(item, 'activated', item.activated != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.activatedLoading, 'fa-check text-success' : !item.activatedLoading && item.activated == '1', 'fa-times text-error': !item.activatedLoading && item.activated == '0' }"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="grid-footer clearfix ng-cloak" ng-if="!flags.loading && items.length > 0">
          <div class="pull-right">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="subscriber/modal.tpl"}
    </script>
  </form>
{/block}
