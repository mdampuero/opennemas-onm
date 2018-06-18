{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="AuthorListCtrl" ng-init="init();backup.master = {if $app.security->hasPermission('MASTER')}true{else}false{/if};backup.id = {$app.user->id}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-edit m-r-10"></i>
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/566184-opennemas-gesti%C3%B3n-de-autores" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                {t}Authors{/t}
              </h4>
            </li>
            <li class="quicklinks visible-xs">
              <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/566184-opennemas-gesti%C3%B3n-de-autores" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question fa-lg"></i>
              </a>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-success text-uppercase" href="[% routing.generate('backend_author_create') %]">
                  <i class="fa fa-plus"></i>
                  {t}Create{/t}
                </a>
              </li>
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
            {acl isAllowed="AUTHOR_DELETE"}
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deleteSelected(item.id)" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
            <li class="quicklinks">
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
          <div class="table-wrapper">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="checkbox-cell">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th class="hidden-xs text-center" width="80"><i class="fa fa-picture-o"></i></th>
                  <th width="400">{t}Name{/t}</th>
                  <th class="hidden-xs">{t}Email{/t}</th>
                  <th class="text-center" width="100">{t}Blog{/t}</th>
                  <th class="hidden-sm hidden-xs" width="400">{t}Biography{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default" ng-if="isSelectable(item)">
                      <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td class="text-center hidden-xs">
                    <dynamic-image class="img-thumbnail img-thumbnail-circle" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[item.avatar_img_id].path_img" transform="thumbnail,50,50" ng-if="item.avatar_img_id && item.avatar_img_id != 0"></dynamic-image>
                    <gravatar class="gravatar img-thumbnail img-thumbnail-circle" ng-model="item.email" size="60" ng-if="!item.avatar_img_id || item.avatar_img_id == 0"></gravatar>
                  </td>
                  <td>
                    <strong class="hidden-xs" ng-if="item.name">
                      [% item.name %]
                    </strong>
                    <span class="visible-xs" ng-if="item.name">
                      <strong>{t}Name{/t}:</strong>
                      [% item.name%]
                    </span>
                    <span class="visible-xs">
                      <strong>{t}Email{/t}:</strong>
                      [% item.email%]
                    </span>
                    <div class="listing-inline-actions">
                      {acl isAllowed="AUTHOR_UPDATE"}
                      <a class="btn btn-default btn-small" href="[% routing.generate('backend_author_show', { id:  item.id }) %]" title="{t}Edit{/t}">
                        <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                      </a>
                      {/acl}
                      {acl isAllowed="AUTHOR_DELETE"}
                      <button class="btn btn-danger btn-small" ng-click="delete(item.id)" ng-if="backup.master || item.id != backup.id" type="button">
                        <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                      </button>
                      {/acl}
                    </div>
                  </td>
                  <td class="hidden-xs">
                    [% item.email%]
                  </td>
                  <td class="text-center">
                    <i class="fa" ng-class="{ 'fa-check text-success': item.is_blog == 1, 'fa-times text-danger': !item.is_blog || item.is_blog == 0 }"></i>
                  </td>
                  <td class="hidden-sm hidden-xs">
                    <span ng-if="item.is_blog == 1">
                      <strong>Blog:</strong>:
                    </span>
                    <span class="item-biography" ng-if="item.bio" title="[% item.bio %]">[% item.bio %]</span>
                    <span class="item-biography" ng-if="!item.bio" title="[% item.bio %]"><i>{t}No biography set{/t}</i></span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="grid-footer clearfix ng-cloak">
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
