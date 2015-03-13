{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('advertisement',{ fk_content_categories: 0, type_advertisement: -1, content_status: -1, with_script: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-bullhorn"></i>
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
              <a href="{url name=admin_ad_create category=$category page=$page filter=$filter}" class="btn btn-primary">
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
              <button class="btn btn-link" ng-click="deselectAll()" tooltip="Clear selection" tooltip-placement="right"type="button">
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
                <button class="btn btn-link"  ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-times fa-lg"></i>
                </button>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-check fa-lg"></i>
                </button>
              </li>
            {/acl}
            {acl isAllowed="ADVERTISEMENT_DELETE"}
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="sendToTrashSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
            <li class="m-r-10 input-prepend inside search-form no-boarder">
              <select class="select2" id="category" ng-model="criteria.fk_content_categories" data-label="{t}Category{/t}">
                <option value="-1">{t}-- All --{/t}</option>
                <optgroup label="{t}Special elements{/t}">
                  <option value="0">{t}HOMEPAGE{/t}</option>
                  <option value="4">{t}OPINION{/t}</option>
                  <option value="3">{t}ALBUM{/t}</option>
                  <option value="6">{t}VIDEO{/t}</option>
                </optgroup>
                <optgroup label="Categories">
                  {section name=as loop=$allcategorys}
                    {assign var=ca value=$allcategorys[as]->pk_content_category}
                    <option value="{$allcategorys[as]->pk_content_category}">
                      {$allcategorys[as]->title}
                      {if $allcategorys[as]->inmenu eq 0}
                        <span class="inactive">{t}(inactive){/t}</span>
                      {/if}
                    </option>
                    {section name=su loop=$subcat[as]}
                      {assign var=subca value=$subcat[as][su]->pk_content_category}
                      {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                        {assign var=subca value=$subcat[as][su]->pk_content_category}
                        <option value="{$subcat[as][su]->pk_content_category}">
                          &rarr;
                          {$subcat[as][su]->title}
                          {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                          <span class="inactive">{t}(inactive){/t}</span>
                          {/if}
                        </option>
                      {/acl}
                    {/section}
                  {/section}
                </optgroup>
              </select>
            </li>
            <li class="hidden-xs">
              <select class="select2" name="filter[type_advertisement]" ng-model="criteria.type_advertisement" data-label="{t}Banner type{/t}">
                {html_options options=$filter_options.type_advertisement selected=$filterType}
              </select>
            </li>
            <li class="hidden-xs">
              <select class="input-medium select2" ng-model="criteria.content_status" data-label="{t}Status{/t}">
                {html_options options=$filter_options.content_status selected=$filterAvailable}
              </select>
            </li>
            <li class="hidden-xs hidden-sm">
              <select class="input-medium select2" ng-model="criteria.with_script" data-label="{t}Type{/t}">
                {html_options options=$filter_options.type}
              </select>
            </li>
            <li class="quicklinks hidden-xs hidden-sm">
              <select class="xmedium" ng-model="pagination.epp">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="500">500</option>
              </select>
            </li>
          </ul>
          <ul class="nav quick-section pull-right simple-pagination ng-cloak" ng-if="contents.length > 0">
            <li class="quicklinks hidden-xs">
              <span class="info">
                [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
              </span>
            </li>
            <li class="quicklinks form-inline pagination-links">
              <div class="btn-group">
                <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                  <i class="fa fa-chevron-left"></i>
                </button>
                <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                  <i class="fa fa-chevron-right"></i>
                </button>
              </div>
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
          <div class="table-wrapper ng-cloak">
            <table class="table table-hover no-margin" ng-if="!loading">
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
                  <th class="center hidden-xs" style="width:30px">{t}Permanence{/t}</th>
                  <th class="center hidden-xs" style="width:40px"><img src="{$params.IMAGE_DIR}clicked.png" alt="{t}Clicks{/t}" title="{t}Clicks{/t}"></th>
                  {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                    <th class="center" style="width:40px;">{t}Available{/t}</th>
                  {/acl}
                </tr>
              </thead>
              <tbody>
                <tr ng-if="contents.length == 0">
                  <td class="empty" colspan="10">
                    {t}There is no advertisement stored in this section{/t}
                  </td>
                </tr>
                <tr ng-if="contents.length > 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td style="">
                    <span class="visible-xs visible-sm">
                      <img ng-if="content.with_script == 1" src="{$params.IMAGE_DIR}iconos/script_code_red.png" alt="Javascript" title="Javascript"/>
                      <img ng-if="content.with_script != 1 && content.is_flash == 1" src="{$params.IMAGE_DIR}flash.gif" alt="{t}Media flash{/t}" title="{t}Media flash element (swf){/t}" style="width: 16px; height: 16px;"/>
                      <img ng-if="content.with_script != 1 && content.is_flash != 1" src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}" title="{t}Media element (jpg, png, gif){/t}" />
                      [% map[content.type_advertisement].name %]
                    </span>
                    [% content.title %]
                    <div class="listing-inline-actions">
                      {acl isAllowed="ADVERTISEMENT_UPDATE"}
                        <a class="link" href="[% edit(content.id, 'admin_advertisement_show') %]" title="{t}Edit{/t}">
                          <i class="fa fa-pencil"></i>{t}Edit{/t}
                        </a>
                      {/acl}
                      {acl isAllowed="ADVERTISEMENT_DELETE"}
                        <button class="link link-danger" ng-click="sendToTrash(content)" type="button">
                          <i class="fa fa-trash-o"></i>{t}Delete{/t}
                        </button>
                      {/acl}
                    </div>
                  </td>
                  <td class="hidden-xs hidden-sm">
                    <img ng-if="content.with_script == 1" src="{$params.IMAGE_DIR}iconos/script_code_red.png" alt="Javascript" title="Javascript"/>
                    <img ng-if="content.with_script != 1 && content.is_flash == 1" src="{$params.IMAGE_DIR}flash.gif" alt="{t}Media flash{/t}" title="{t}Media flash element (swf){/t}" style="width: 16px; height: 16px;"/>
                    <img ng-if="content.with_script != 1 && content.is_flash != 1" src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}" title="{t}Media element (jpg, png, gif){/t}" />
                    [% map[content.type_advertisement].name %]
                  </td>
                  <td class="center hidden-xs">
                    <span ng-if="content.type_medida == 'NULL'">{t}Not defined{/t}</span>
                    <span ng-if="content.type_medida == 'CLICK'">{t}Clicks:{/t} [% content.num_clic %]</span>
                    <span ng-if="content.type_medida == 'VIEW'">{t}Viewed:{/t} [% content.num_view %]</span>
                    <span ng-if="content.type_medida == 'DATE'">{t}Date:{/t} [% content.startime %]-[% content.endtime %]</span>
                  </td>
                  <td class="center hidden-xs">
                    [% content.num_clic_count %]
                  </td>
                  {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                    <td class="right" style="width:40px;">
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
        <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
          <div class="pagination-info pull-left" ng-if="contents.length > 0">
            {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
          </div>
          <div class="pull-right pagination-wrapper" ng-if="contents.length > 0">
            <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
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
  </div>
{/block}
