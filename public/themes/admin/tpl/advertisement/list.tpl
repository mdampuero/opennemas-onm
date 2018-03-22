{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('advertisement', 'backend_ws_contents_list');
categories = {json_encode($categories)|clear_json};
advertisement_positions = {json_encode($advertisement_positions)|clear_json}; type = {json_encode($types)|clear_json};
status = [ { name: '{t}All{/t}', value: null }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 } ];">
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
            <input class="no-boarder" name="title" ng-model="criteria.title" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs ng-cloak">
            <ui-select name="author" theme="select2" ng-model="criteria.fk_content_categories">
              <ui-select-match>
                <strong>{t}Category{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices group-by="'group'" repeat="item.value as item in categories | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="hidden-xs ng-cloak">
            <ui-select name="position" theme="select2" ng-model="criteria.position">
              <ui-select-match>
                <strong>{t}Position{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in advertisement_positions | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="hidden-xs hidden-sm ng-cloak">
            <ui-select name="type" theme="select2" ng-model="criteria.with_script">
              <ui-select-match>
                <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in type | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks ng-cloak">
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
          <li class="quicklinks">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
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
        <div class="table-wrapper ng-cloak ads-listing" ng-if="!loading && contents.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th class="">{t}Title{/t}</th>
                <th class="hidden-xs hidden-sm " style="width: 33%;">{t}Position{/t}</th>
                <th class="hidden-xs text-center"><i class="fa fa-mouse-pointer"></i></th>
                <th class="hidden-xs hidden-sm text-center">{t}Type{/t}</th>
                {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                <th class="text-center">{t}Published{/t}</th>
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
                <td>
                  <span class="small-text visible-xs-inline-block visible-sm-inline-block">
                    <i class="fa fa-file-picture-o fa-lg m-r-5 text-success" ng-if="content.with_script == 0 && content.is_flash != 1" title="{t}Media element (jpg, png, gif){/t}"></i>
                    <i class="fa fa-file-video-o fa-lg m-r-5 text-danger" ng-if="content.with_script == 0 && content.is_flash == 1" title="{t}Media flash element (swf){/t}"></i>
                    <i class="fa fa-file-code-o fa-lg m-r-5 text-info" ng-if="content.with_script == 1" title="Javascript"></i>
                    <i class="fa fa-gg fa-lg m-r-5 text-info" ng-if="content.with_script == 2" title="OpenX"></i>
                    <i class="fa fa-google fa-lg m-r-5 text-danger" ng-if="content.with_script == 3" title="Google DFP"></i>
                  </span>
                  [% content.title %]
                  <div class="small-text">
                    <span ng-if="content.starttime && content.starttime != '0000-00-00 00:00:00'">
                      <strong>{t}Available from{/t} </strong>
                      [% content.starttime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </span>
                    <span ng-if="content.endtime && content.endtime != '0000-00-00 00:00:00'">
                      <strong>{t}to{/t} </strong> [% content.endtime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </span>
                  </div>
                  <div class="small-text">
                    <span class="hidden-lg">
                      <span ng-show="content.positions.length > 1" tooltip-class="text-left" {* uib-tooltip-template="'ad_position_template'" *} tooltip-placement="bottom-left">{t 1="[% content.positions.length %]"}%1 positions{/t},</span>
                      <span ng-show="content.positions.length == 1"><span ng-repeat="value in content.positions | limitTo:1">[% map[value].name %]</span>,</span>
                      <span ng-show="content.positions.length == 0">{t}No positions assigned{/t},</span>
                      <span ng-show="content.num_clic_count == 0">{t}No clicks{/t}</span>
                      <span ng-show="content.num_clic_count > 0">{t 1="[% content.num_clic_count %]"}%1 clicks{/t}</span>
                    </span>
                  </div>
                  <div class="listing-inline-actions" >
                    {acl isAllowed="ADVERTISEMENT_UPDATE"}
                    <a class="btn btn-default btn-small" href="[% edit(content.id, 'admin_advertisement_show') %]" title="{t}Edit{/t}">
                      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                    </a>
                    {/acl}
                    {acl isAllowed="ADVERTISEMENT_DELETE"}
                    <button class="btn btn-danger btn-small" ng-click="sendToTrash(content)" type="button">
                      <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                    </button>
                    {/acl}
                  </div>
                </td>
                <td class="hidden-xs hidden-sm small-text">
                  <span ng-repeat="value in content.positions | limitTo:3" class="ad-position">[% map[value].name %]</span>
                  <span ng-show="content.positions.length > 2" {* uib-tooltip-template="'ad_position_template'" tooltip-placement="bottom" *}>{t 1="[% content.positions.length - 3 %]"}And %1 more…{/t}</span>
                </td>
                <td class="hidden-xs text-center">
                  [% content.num_clic_count %]
                </td>
                <td class="hidden-xs hidden-sm text-center">
                  <i class="fa fa-file-picture-o fa-lg m-r-5 text-success" ng-if="content.with_script == 0 && content.is_flash != 1" title="{t}Media element (jpg, png, gif){/t}"></i>
                  <i class="fa fa-file-video-o fa-lg m-r-5 text-danger" ng-if="content.with_script == 0 && content.is_flash == 1" title="{t}Media flash element (swf){/t}"></i>
                  <i class="fa fa-file-code-o fa-lg m-r-5 text-info" ng-if="content.with_script == 1" title="Javascript"></i>
                  <i class="fa fa-gg fa-lg m-r-5 text-info" ng-if="content.with_script == 2" title="OpenX"></i>
                  <i class="fa fa-google fa-lg m-r-5 text-danger" ng-if="content.with_script == 3" title="Google DFP"></i>
                </td>
                {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                <td class="text-center">
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
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
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
  <script type="text/ng-template" id="ad_position_template">
    <div ng-repeat="position in content.positions">[% map[position].name %]</div>
  </script>
</div>
{/block}
