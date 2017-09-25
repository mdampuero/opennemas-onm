{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init(null, { content_status: -1, renderlet: -1 }, 'name', 'asc', 'backend_ws_menus_list', '{{$smarty.const.CURRENT_LANGUAGE}}'); menu_positions = {json_encode($menu_positions)|clear_json}; languageData = {json_encode($language_data)|clear_json}">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-list-alt page-navbar-icon"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/221738-opennemas-c%C3%B3mo-cambiar-el-men%C3%BA-de-mi-peri%C3%B3dico" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
              {t}Menus{/t}
            </h4>
          </li>
          <li class="quicklinks visible-xs">
            <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/221738-opennemas-c%C3%B3mo-cambiar-el-men%C3%BA-de-mi-peri%C3%B3dico" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
              <i class="fa fa-question fa-lg"></i>
            </a>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              {acl isAllowed="MENU_CREATE"}
              <a href="{url name=admin_menu_create}" class="btn btn-primary" id="create-button">
                <i class="fa fa-plus"></i>
                {t}Create{/t}
              </a>
              {/acl}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
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
          {acl isAllowed="ADVERTISEMENT_DELETE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="removeSelectedMenus()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
            <input class="no-boarder" name="name" ng-model="criteria.name_like" placeholder="{t}Search by name{/t}" type="text"/>
          </li>
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs ng-cloak">
            <ui-select name="view" theme="select2" ng-model="pagination.epp">
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
          <li class="quicklinks hidden-xs">
            <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
          </li>
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
        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
            <h4>{t}Unable to find any menu that matches your search.{/t}</h4>
            <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
          </div>
        </div>
        <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th class="pointer">{t}Name{/t}</th>
                {if count($menu_positions) > 1}
                <th class="pointer nowrap text-center" width="100">{t}Position assigned{/t}</th>
                {/if}
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td>
                  [% content.name %]
                  <div class="listing-inline-actions">
                    {is_module_activated name="es.openhost.module.multilanguage"}
                      <translator ng-model="lang" link="[% edit(content.id, 'admin_menu_show') %]" translator-options="languageData" />
                    {/is_module_activated}
                    {is_module_activated name="es.openhost.module.multilanguage" deactivated=1}
                    <a class="link" href="[% edit(content.id, 'admin_menu_show') %]" title="{t}Edit{/t}">
                      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                    </a>
                    {/is_module_activated}
                    <button class="link link-danger" ng-click="removeMenu(content)" type="button">
                      <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                    </button>
                  </div>
                </td>
                {if count($menu_positions) > 1}
                <td class="text-center">
                  <span ng-if="content.position">
                    [% menu_positions[content.position] %]
                  </span>
                  <span ng-if="!content.position">
                    {t}Unasigned{/t}
                  </span>
                </td>
                {/if}
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
        <div class="pull-right">
          <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-remove-permanently">
    {include file="common/modals/_modalDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-batch-remove-permanently">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
</div>
{/block}
