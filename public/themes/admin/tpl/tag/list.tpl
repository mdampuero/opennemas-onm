{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="TagListCtrl" ng-init="init({json_encode($locale)|clear_json})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-tags"></i>
                {t}Tags{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="TAG_CREATE"}
                <li>
                  <a class="btn btn-primary text-uppercase" ng-click="createTag()">
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
            {acl isAllowed="TAG_DELETE"}
              <li class="quicklinks hidden-xs">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" href="#" ng-click="deleteSelected('backend_ws_tag_delete')">
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
              <input class="no-boarder" name="name" ng-model="criteria.name" placeholder="Search by name" type="text">
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="data.extra.locales.length > 1">
              <ui-select name="language" theme="select2" ng-model="criteria.language_id">
                <ui-select-match>
                  <strong>{t}Language{/t}:</strong> [% $select.selected.value %]
                </ui-select-match>
                <ui-select-choices repeat="item.key as item in data.extra.locales | filter: { value: $select.search }">
                  <div ng-bind-html="item.value"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="criteria.epp">
                <ui-select-match>
                  <strong>{t}View{/t}:</strong> [% $select.selected %]
                </ui-select-match>
                <ui-select-choices repeat="item in views | filter: $select.search">
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
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && items.length == 0 && !editedTag">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find any item that matches your search.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-if="(!flags.http.loading && items.length > 0) || editedTag">
        <div class="grid-body no-padding">
          <div class="table-wrapper">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="checkbox-cell" width="10">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th>{t}Name{/t}</th>
                  <th class="text-center" width="10">{t}Nº contents{/t}</th>
                  <th class="text-center" width="10"></th>
                </tr>
              </thead>
              <tbody>
                <tr ng-hide="!editedTag || editedTag.id" class="bg-warning">
                  <td></td>
                  <td class="editing">
                    <input class="form-control" id="editedTag.name" placeholder="new tag"  name="editedTag.name" type="text" ng-model="editedTag.name" ng-keyup="validateTag()"/>
                    <i class="fa fa-exclamation-circle m-r-5 text-warning" uib-tooltip="{t}The tag exist or is invalid{/t}" tooltip-placement="right" ng-hide="enableUpdate"></i>
                    <select name="editedTag.language_id" ng-model="editedTag.language_id" ng-options="locale.key as locale.value for locale in data.extra.locales" ng-if="data.extra.locales.length > 1">
                    </select>
                  </td>
                  <td></td>
                  <td>
                    <button class="btn btn-primary" ng-disabled="!enableUpdate" ng-click="save()" ng-class="{ 'fa-circle-o-notch': flags.http.saving }" type="button">
                      {t}Ok{/t}
                    </button>
                    <button class="btn btn-secundary" ng-click="editTag()" type="button">
                      {t}Cancel{/t}
                    </button>
                  </td>
                </tr>
                <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td class="editable" ng-click="editTag(item)" ng-if="item.id !== editedTag.id">
                    [% item.name %]
                    <a class="link" ng-click="editTag(item)">
                      <i class="fa fa-pencil m-r-5"></i>
                    </a>
                  </td>
                  <td class="editing" ng-if="item.id === editedTag.id">
                    <input class="form-control" id="editedTag.name" name="editedTag.name" type="text" ng-model="editedTag.name" ng-keyup="validateTag()"/>
                    <i class="fa fa-exclamation-circle m-r-5 text-warning" uib-tooltip="{t}The tag exist or is invalid{/t}" tooltip-placement="right" ng-hide="enableUpdate"></i>
                    <button class="btn btn-primary" ng-disabled="!enableUpdate" ng-click="save()" ng-class="{ 'fa-circle-o-notch': flags.http.saving }" type="button">
                      {t}Ok{/t}
                    </button>
                    <button class="btn btn-secundary" ng-click="editTag()" type="button">
                      {t}Cancel{/t}
                    </button>
                  </td>
                  <td class="text-center">
                    [% data.extra.numberOfContents[item.id] %]
                  </td>
                  <td class="text-right">
                    <button class="btn btn-danger btn-small" ng-click="delete(item.id)" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="grid-footer clearfix ng-cloak" ng-if="items.length !== 0">
          <div class="pull-right">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="base/modal/modal.delete.tpl"}
    </script>
  </div>
{/block}
