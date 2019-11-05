{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="TagListCtrl" ng-init="init()">
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
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              {acl isAllowed="TAG_CREATE"}
                <li class="quicklinks">
                  <a class="btn btn-loading btn-success text-uppercase" href="[% routing.generate('backend_tag_create') %]">
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
              <input class="no-boarder" name="name" ng-model="criteria.name" placeholder="{t}Search{/t}" type="text">
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="config.locale.multilanguage">
              <ui-select name="language" theme="select2" ng-model="criteria.locale">
                <ui-select-match>
                  <strong>{t}Language{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="locale.id as locale in config.locale.available | filter: { name: $select.search }">
                  <div ng-bind-html="locale.name"></div>
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
                    <th class="text-center v-align-middle" width="50">
                      <div class="checkbox checkbox-default">
                        <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                        <label for="select-all"></label>
                      </div>
                    </th>
                    <th>{t}Name{/t}</th>
                    <th>{t}Slug{/t}</th>
                    <th ng-if="config.locale.multilanguage" width="100">{t}Locale{/t}</th>
                    <th class="text-center" width="100">{t}Contents{/t}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                    <td class="text-center v-align-middle">
                      <div class="checkbox check-default">
                        <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                        <label for="checkbox[%$index%]"></label>
                      </div>
                    </td>
                    <td>
                      [% item.name %]
                      <div class="listing-inline-actions">
                        <a class="btn btn-default btn-small" href="[% routing.generate('backend_tag_show', { id: getItemId(item) }) %]">
                          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                        </a>
                        <button class="btn btn-danger btn-small" ng-click="delete(item.id)" type="button">
                          <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                        </button>
                      </div>
                    </td>
                    <td>
                      [% item.slug %]
                    </td>
                    <td class="text-center">
                      <span class="badge badge-default" ng-class="{ 'badge-danger': !data.extra.stats[getItemId(item)] || data.extra.stats[getItemId(item)] == 0 }">
                        <strong>
                          [% data.extra.stats[getItemId(item)] ? data.extra.stats[getItemId(item)] : 0 %]
                        </strong>
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="common/extension/modal.delete.tpl"}
    </script>
  </div>
{/block}
