{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('advertisement',{ fk_content_categories: 0, type_advertisement: -1, content_status: -1, with_script: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar" ng-controller="AdBlockCtrl">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-bullhorn"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/818598-opennemas-como-crear-y-gestionar-publicidades" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
              <span class="hidden-xs">{t}Advertisements{/t}</span>
              <span class="visible-xs-inline">{t}Ads{/t}</span>
            </h4>
          </li>
        </ul>
      </div>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          {acl isAllowed="ADVERTISEMENT_SETTINGS"}
          <li class="quicklinks">
            <a class="btn btn-link" href="{url name=admin_ads_config}">
              <i class="fa fa-cog fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          {/acl}
          <li class="quicklinks">
            <a href="{url name=admin_ad_create}" class="btn btn-primary" id="create-button">
              <i class="fa fa-plus"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
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
          {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
          <li class="quicklinks">
            <button class="btn btn-link"  ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          {/acl}
          {acl isAllowed="ADVERTISEMENT_DELETE"}
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="sendToTrashSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
            <input class="no-boarder" name="title" ng-model="criteria.title_like" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|clear_json}">
            <ui-select name="author" theme="select2" ng-model="criteria.fk_content_categories">
              <ui-select-match>
                <strong>{t}Category{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices group-by="'group'" repeat="item.value as item in categories | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="hidden-xs ng-cloak" ng-init="typeAdvertisement = {json_encode($typeAdvertisement)|clear_json}">
            <ui-select name="type_advertisement" theme="select2" ng-model="criteria.type_advertisement">
              <ui-select-match>
                <strong>{t}Position{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in typeAdvertisement | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="hidden-xs hidden-sm ng-cloak" ng-init="type = {json_encode($types)|clear_json}">
            <ui-select name="type" theme="select2" ng-model="criteria.with_script">
              <ui-select-match>
                <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in type | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks ng-cloak" ng-init="status = [ { name: '{t}All{/t}', value: -1 }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 } ]">
            <ui-select name="status" theme="select2" ng-model="criteria.content_status">
              <ui-select-match>
                <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
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
          <li class="quicklinks">
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
            <h4>{t}Unable to find any advertisement that matches your search.{/t}</h4>
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
                <th>{t}Title{/t}</th>
                <th class="hidden-xs hidden-sm" style="width:250px">{t}Type{/t}</th>
                <th class="hidden-xs text-center" width="100">{t}Permanence{/t}</th>
                <th class="hidden-xs text-center" width="100"><i class="fa fa-mouse-pointer"></i></th>
                {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                <th class="text-center" width="100">{t}Published{/t}</th>
                {/acl}
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
                <td style="">
                  <span class="visible-xs-inline-block visible-sm-inline-block">
                    <i class="fa fa-file-picture-o fa-lg m-r-5 text-success" ng-if="content.with_script == 0 && content.is_flash != 1" title="{t}Media element (jpg, png, gif){/t}"></i>
                    <i class="fa fa-file-video-o fa-lg m-r-5 text-danger" ng-if="content.with_script == 0 && content.is_flash == 1" title="{t}Media flash element (swf){/t}"></i>
                    <i class="fa fa-file-code-o fa-lg m-r-5 text-info" ng-if="content.with_script == 1" title="Javascript"></i>
                    <i class="fa fa-gg fa-lg m-r-5 text-info" ng-if="content.with_script == 2" title="OpenX"></i>
                    <i class="fa fa-google fa-lg m-r-5 text-danger" ng-if="content.with_script == 3" title="Google DFP"></i>
                    [% map[content.type_advertisement].name %]
                  </span>
                  [% content.title %]
                  <div class="listing-inline-actions">
                    {acl isAllowed="ADVERTISEMENT_UPDATE"}
                    <a class="link" href="[% edit(content.id, 'admin_advertisement_show') %]" title="{t}Edit{/t}">
                      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                    </a>
                    {/acl}
                    {acl isAllowed="ADVERTISEMENT_DELETE"}
                    <button class="link link-danger" ng-click="sendToTrash(content)" type="button">
                      <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                    </button>
                    {/acl}
                  </div>
                </td>
                <td class="hidden-xs hidden-sm">
                  <i class="fa fa-file-picture-o fa-lg m-r-5 text-success" ng-if="content.with_script == 0 && content.is_flash != 1" title="{t}Media element (jpg, png, gif){/t}"></i>
                  <i class="fa fa-file-video-o fa-lg m-r-5 text-danger" ng-if="content.with_script == 0 && content.is_flash == 1" title="{t}Media flash element (swf){/t}"></i>
                  <i class="fa fa-file-code-o fa-lg m-r-5 text-info" ng-if="content.with_script == 1" title="Javascript"></i>
                  <i class="fa fa-gg fa-lg m-r-5 text-info" ng-if="content.with_script == 2" title="OpenX"></i>
                  <i class="fa fa-google fa-lg m-r-5 text-danger" ng-if="content.with_script == 3" title="Google DFP"></i>
                  [% map[content.type_advertisement].name %]
                </td>
                <td class="hidden-xs text-center">
                  <span ng-if="content.type_medida == 'NULL'">{t}Not defined{/t}</span>
                  <span ng-if="content.type_medida == 'CLICK'">{t}Clicks:{/t} [% content.num_clic %]</span>
                  <span ng-if="content.type_medida == 'VIEW'">{t}Viewed:{/t} [% content.num_view %]</span>
                  <span ng-if="content.type_medida == 'DATE'">{t}Date:{/t} [% content.startime %]-[% content.endtime %]</span>
                </td>
                <td class="hidden-xs text-center">
                  [% content.num_clic_count %]
                </td>
                {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                <td class="text-centerk">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.content_status == '1', 'fa-times text-error': !content.loading && content.content_status == '0' }"></i>
                  </button>
                </td>
                {/acl}
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
  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/_modalDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-adblock">
    {include file="base/modals/modalAdblock.tpl"}
  </script>
</div>
{/block}
