{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-controller="CategoryListCtrl" ng-init="forcedLocale = '{$locale}'; init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-bookmark m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                {t}Categories{/t}
              </h4>
            </li>
            <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
              <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="CATEGORY_CREATE"}
              <li class="quicklinks">
                <a class="btn btn-loading btn-success text-uppercase" href="[% routing.generate('backend_category_create') %]">
                  <i class="fa fa-plus m-r-5"></i>
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
              <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button">
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
            {acl isAllowed="MASTER"}
              <li class="quicklinks" ng-if="selected.items.length < items.length && areSelectedNotEmpty()">
                <button class="btn btn-link" ng-click="moveSelected()" uib-tooltip="{t}Move contents{/t}" tooltip-placement="bottom">
                  <i class="fa fa-flip-horizontal fa-reply fa-lg"></i>
                </button>
              </li>
              <li class="quicklinks hidden-xs" ng-if="selected.items.length < items.length && areSelectedNotEmpty()">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="emptySelected()" uib-tooltip="{t}Delete contents{/t}" tooltip-placement="bottom">
                  <i class="fa fa-fire fa-lg"></i>
                </button>
              </li>
            {/acl}
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            {acl isAllowed="CATEGORY_AVAILABLE"}
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="patchSelected('inmenu', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-times fa-lg"></i>
                </button>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="patchSelected('inmenu', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-check fa-lg"></i>
                </button>
              </li>
            {/acl}
            {acl isAllowed="CATEGORY_DELETE"}
              <li class="quicklinks hidden-xs" ng-if="areSelectedEmpty()">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks" ng-if="areSelectedEmpty()">
                <button class="btn btn-link" ng-click="deleteSelected('api_v1_backend_categories_delete')" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
            <li class="quicklinks hidden-xs ng-cloak" ng-init="activated = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Enabled{/t}', value: 1}, { name: '{t}Disabled{/t}', value: 0 } ]">
              <ui-select name="activated" theme="select2" ng-model="criteria.inmenu">
                <ui-select-match>
                  <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in activated | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
          </ul>
          <ul class="nav quick-section pull-right ng-cloak" ng-if="items.length > 0">
            <li class="quicklinks hidden-xs">
              <onm-pagination ng-model="criteria.page" items-per-page="items.length" total-items="data.total"></onm-pagination>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && items.length == 0">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find any item that matches your search.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-if="!flags.http.loading && items.length > 0">
        <div class="grid-body no-padding">
          <div class="table-wrapper no-overflow">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="text-center v-align-middle" width="50">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th>{t}Name{/t}</th>
                  <th class="hidden-xs" width="200">{t}Slug{/t}</th>
                  <th class="hidden-sm hidden-xs text-center" width="80"><i class="fa fa-picture-o"></i></th>
                  <th class="hidden-sm hidden-xs text-center" width="80"><i class="fa fa-paint-brush"></i></th>
                  <th class="hidden-xs" width="100">{t}Contents{/t}</th>
                  <th class="hidden-xs text-center" width="50">{t}RSS{/t}</th>
                  <th class="text-center" width="50">{t}Enabled{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                  <td class="text-center v-align-middle">
                    <div class="checkbox check-default" ng-if="isSelectable(item)">
                      <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="getItemId(item)" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td class="v-align-middle">
                    <div class="[% 'm-l-' + 30 * levels[getItemId(item)] %]">
                      [% item.title %]
                      <div class="listing-inline-actions">
                        <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_category_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
                        <a class="btn btn-default btn-small" href="[% routing.generate('backend_category_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
                          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                        </a>
                        <div uib-tooltip="{t}Only empty categories can be deleted{/t}" tooltip-enable="data.extra.stats[getItemId(item)] > 0" tooltip-class="tooltip-danger">
                          <button class="btn btn-danger btn-small" ng-click="delete(getItemId(item))" ng-disabled="data.extra.stats[getItemId(item)] > 0" type="button">
                            <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                          </button>
                        </div>
                        {acl isAllowed="MASTER"}
                          <span>
                            <button class="btn btn-white btn-small dropdown-toggle" data-toggle="dropdown" ng-disabled="isEmpty(item)" type="button">
                              <i class="fa fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu no-padding">
                              <li>
                                <a href="#" ng-click="move(getItemId(item), item)">
                                  <i class="fa fa-flip-horizontal fa-reply"></i>
                                  {t}Move contents{/t}
                                </a>
                              </li>
                              <li>
                                <a href="#" ng-click="empty(getItemId(item))">
                                  <i class="fa fa-fire"></i>
                                  {t}Delete contents{/t}
                                </a>
                              </li>
                            </ul>
                          </span>
                        {/acl}
                      </div>
                    </div>
                  </td>
                  <td class="hidden-xs v-align-middle">
                    [% item.name %]
                  </td>
                  <td class="hidden-sm hidden-xs text-center v-align-middle">
                    <dynamic-image class="img-thumbnail" instance="{$app.instance->getMediaShortPath()}/" ng-model="item.logo_path" only-image="true"></dynamic-image>
                  </td>
                  <td class="hidden-sm hidden-xs text-center v-align-middle">
                    <span class="badge badge-white" ng-if="item.color" ng-style="{ 'background-color': item.color}">&nbsp;&nbsp;</span>
                  </td>
                  <td class="hidden-xs text-center v-align-middle">
                    <span class="badge badge-default" ng-class="{ 'badge-danger': !data.extra.stats[getItemId(item)] || data.extra.stats[getItemId(item)] == 0 }">
                      <strong>
                        [% data.extra.stats[getItemId(item)] ? data.extra.stats[getItemId(item)] : 0 %]
                      </strong>
                    </span>
                  </td>
                  <td class="hidden-xs text-center v-align-middle">
                    <button class="btn btn-white" ng-click="patchRss(item, item.params.inrss != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.inrssLoading, 'fa-feed text-success' : !item.inrssLoading && item.params.inrss == '1', 'fa-feed text-error': !item.inrssLoading && (!item.params || !item.params.inrss || item.params.inrss == '0') }"></i>
                    </button>
                  </td>
                  <td class="text-center v-align-middle">
                    <button class="btn btn-white" ng-click="patch(item, 'inmenu', item.inmenu != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.inmenuLoading, 'fa-check text-success' : !item.inmenuLoading && item.inmenu == '1', 'fa-times text-error': !item.inmenuLoading && item.inmenu == '0' }"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="grid-footer clearfix ng-cloak">
          <div class="pull-right">
            <onm-pagination ng-model="criteria.page" items-per-page="items.length" total-items="data.total"></onm-pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-empty">
    {include file="category/modal.empty.tpl"}
  </script>
  <script type="text/ng-template" id="modal-move">
    {include file="category/modal.move.tpl"}
  </script>
{/block}
