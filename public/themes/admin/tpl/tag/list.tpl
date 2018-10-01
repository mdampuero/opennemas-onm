{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="TagListCtrl" ng-init="init('{$locale}')">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-tags m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                {t}Tags{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=backend_tags_config}" title="{t}Config tag module{/t}">
                  <span class="fa fa-cog fa-lg"></span>
                </a>
              </li>
              <li class="quicklinks"><span class="h-seperate"></span></li>
              {acl isAllowed="TAG_CREATE"}
                <li>
                  <button class="btn btn-primary text-uppercase" ng-click="createTag()" ng-disabled="editedTag && !editedTag.id" type="button">
                    <i class="fa fa-plus"></i>
                    {t}Create{/t}
                  </button>
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
            <li class="quicklinks hidden-xs ng-cloak" ng-if="data.extra.locales.length > 2">
              <ui-select name="language" theme="select2" ng-model="criteria.language_id">
                <ui-select-match>
                  <strong>{t}Language{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="locale.id as locale in data.extra.locales | filter: { name: $select.search }">
                  <div ng-bind-html="locale.name"></div>
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
      <div class="listing-no-contents ng-cloak" ng-show="!flags.http.loading && items.length == 0 && !editedTag">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find any item that matches your search.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-show="(!flags.http.loading && items.length > 0) || editedTag">
        <div class="grid-body no-padding">
          <div class="table-wrapper">
            <form name="form">
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
                  <tr ng-if="editedTag && !editedTag.id" class="bg-warning">
                    <td></td>
                    <td class="editing">
                      <div class="row">
                        <div class="col-lg-4 col-sm-6 form-group no-margin">
                          <label class="form-label" for="name">
                            {t}Tag{/t}
                          </label>
                          <div class="controls input-with-icon right">
                            <input class="form-control" id="name" name="name" ng-change="isValid()" ng-model="editedTag.name" placeholder="{t}Tag{/t}" required type="text">
                            <span class="icon right ng-cloak">
                              <span class="fa fa-circle-o-notch fa-spin" ng-if="flags.http.validating"></span>
                              <span class="fa fa-check text-success" ng-if="!flags.http.validating && form.name.$dirty && form.name.$valid"></span>
                              <span class="fa fa-info-circle text-info" ng-if="!flags.http.validating && !form.name.$dirty && form.name.$invalid" uib-tooltip-html="'<ul><li>{t}Tags should start by letter or number{/t}</li><li>{t}The maximum length for a tag is 60 chars (recommended: 30 chars or less){/t}</li></ul>'"></span>
                              <span class="fa fa-times text-error text-left" ng-if="!flags.http.validating && form.name.$dirty && form.name.$invalid" uib-tooltip-html="error"></span>
                            </span>
                          </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 form-group no-margin" ng-if="data.extra.locales.length > 2">
                          <label class="form-label" for="locale">
                            {t}Language{/t}
                          </label>
                          <div class="controls">
                            <select class="form-control" name="locale" ng-model="editedTag.language_id">
                              <option value="[% locale.id %]" ng-repeat="locale in data.extra.locales">[% locale.name %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="form-group no-margin p-t-5">
                        <button class="btn btn-default btn-small" ng-click="editTag()" type="button">
                          <i class="fa fa-times m-r-5"></i>
                          {t}Cancel{/t}
                        </button>
                        <button class="btn btn-small btn-loading btn-success" ng-disabled="form.$invalid" ng-click="save()"  type="button">
                          <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                          {t}Save{/t}
                        </button>
                      </div>
                    </td>
                    <td></td>
                    <td></td>
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
                      <i class="fa fa-pencil m-l-5"></i>
                    </td>
                    <td class="editing" ng-if="item.id === editedTag.id">
                      <div class="row">
                        <div class="col-lg-4 col-sm-6 form-group no-margin">
                          <label class="form-label" for="name">
                            {t}Tag{/t}
                          </label>
                          <div class="controls input-with-icon right">
                            <input class="form-control" id="name" name="name" ng-change="isValid()" ng-model="editedTag.name" placeholder="{t}Tag{/t}" required type="text">
                            <span class="icon right ng-cloak">
                              <span class="fa fa-circle-o-notch fa-spin" ng-if="flags.http.validating"></span>
                              <span class="fa fa-check text-success" ng-if="!flags.http.validating && form.name.$dirty && form.name.$valid"></span>
                              <span class="fa fa-info-circle text-info" ng-if="!flags.http.validating && !form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                              <span class="fa fa-times text-error" ng-if="!flags.http.validating && form.name.$dirty && form.name.$invalid" uib-tooltip="{t}The tag already exist or is invalid{/t}"></span>
                            </span>
                          </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 form-group no-margin" ng-if="data.extra.locales.length > 2">
                          <label class="form-label" for="locale">
                            {t}Language{/t}
                          </label>
                          <div class="controls">
                            <select class="form-control" name="locale" ng-model="editedTag.language_id">
                              <option value="[% locale.id %]" ng-repeat="locale in data.extra.locales">[% locale.name %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="form-group no-margin p-t-5">
                        <button class="btn btn-default btn-small" ng-click="editTag()" type="button">
                          <i class="fa fa-times m-r-5"></i>
                          {t}Cancel{/t}
                        </button>
                        <button class="btn btn-small btn-success btn-loading" ng-disabled="form.$invalid" ng-click="save()"  type="button">
                          <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                          {t}Save{/t}
                        </button>
                      </div>
                    </td>
                    <td class="text-center">
                      [% data.extra.stats[item.id] ? data.extra.stats[item.id] : 0 %]
                    </td>
                    <td class="text-right">
                      <button class="btn btn-danger btn-small" ng-click="delete(item.id)" type="button">
                      <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </form>
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
