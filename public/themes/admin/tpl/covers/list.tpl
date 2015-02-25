{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_covers category=$category page=$page}" method="get" name="formulario" id="formulario"  ng-app="BackendApp" ng-controller="ContentListController" ng-init="init('kiosko', { content_status: -1, title_like: '', category_name: -1, in_litter: 0{if $category == 'widget'}, in_home: 1{/if} }, {if $category == 'widget'}'position'{else}'created'{/if}, {if $category == 'widget'}'asc'{else}'desc'{/if}, 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-newspaper-o"></i>
                            {t}Covers{/t}
                        </h4>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks dropdown hidden-xs">
                        <div data-toggle="dropdown">
                            {if $category == 'widget'}
                                {t}Widget Home{/t}
                            {else}
                                {t}Listing{/t}
                            {/if}
                            <span class="caret"></span>
                        </div>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{url name=admin_covers_widget}" {if $category=='widget'}class="active"{/if}>
                                    {t}Widget Home{/t}
                                </a>
                            </li>
                            <li>
                                <a href="{url name=admin_covers}" {if $category != 'widget'}class="active"{/if}>
                                    {t}Listing{/t}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            {acl isAllowed="KIOSKO_ADMIN"}
                                <li>
                                    <a class="btn btn-link" href="{url name=admin_covers_config}" title="{t}Config covers module{/t}">
                                        <span class="fa fa-cog fa-lg"></span>
                                    </a>
                                </li>
                            {/acl}
                        </li>
                        {acl isAllowed="KIOSKO_WIDGET"}
                        {if $category eq 'widget'}
                          <li class="quicklinks"><span class="h-seperate"></span></li>
                          <li>
                              <a class="btn btn-white" href="#" ng-click="savePositions('backend_ws_contents_save_positions')"  title="{t}Save positions{/t}">
                                  <span class="fa fa-save"></span> {t}Save positions{/t}
                              </a>
                          </li>
                        {/if}
                        {/acl}
                        <li class="quicklinks"><span class="h-seperate"></span></li>
                        {acl isAllowed="KIOSKO_CREATE"}
                        <li>
                            <a class="btn btn-primary" href="{url name=admin_cover_create}" title="{t}New cover{/t}">
                                {t}Create{/t}
                            </a>
                        </li>
                        {/acl}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                      <button class="btn btn-link" ng-click="selected.contents = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
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
                    {acl isAllowed="KIOSKO_AVAILABLE"}
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" id="batch-publish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Publish{/t}" tooltip-placement="bottom">
                            <i class="fa fa-check fa-lg"></i>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" id="batch-unpublish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
                            <i class="fa fa-times fa-lg"></i>
                        </a>
                    </li>
                    {/acl}
                    {if $category neq 'widget'}
                    {acl isAllowed="KIOSKO_HOME"}
                    <li class="quicklinks hidden-xs">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
                            <i class="fa fa-home fa-lg"></i>
                        </a>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
                            <i class="fa fa-home fa-lg"></i>
                            <i class="fa fa-times fa-sub text-danger"></i>
                        </a>
                    </li>
                    {/acl}
                    {/if}
                    {acl isAllowed="KIOSKO_DELETE"}
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" id="batch-delete" ng-click="sendToTrashSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                            <i class="fa fa-trash-o fa-lg"></i>
                        </a>
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
                        <input class="no-boarder" name="title" ng-model="criteria.title_like" placeholder="{t}Search by title{/t}" type="text"/>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <select class="select2" id="category" ng-model="criteria.category_name" data-label="{t}Category{/t}">
                          <option value="-1">{t}-- All --{/t}</option>
                          {section name=as loop=$allcategorys}
                            {assign var=ca value=$allcategorys[as]->pk_content_category}
                            <option value="{$allcategorys[as]->name}">
                                {$allcategorys[as]->title}
                                {if $allcategorys[as]->inmenu eq 0}
                                    <span class="inactive">{t}(inactive){/t}</span>
                                {/if}
                            </option>

                            {section name=su loop=$subcat[as]}
                              {assign var=subca value=$subcat[as][su]->pk_content_category}
                              {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                  {assign var=subca value=$subcat[as][su]->pk_content_category}
                                  <option value="{$subcat[as][su]->name}">
                                      &rarr;
                                      {$subcat[as][su]->title}
                                      {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                                          <span class="inactive">{t}(inactive){/t}</span>
                                      {/if}
                                  </option>
                              {/acl}
                            {/section}

                          {/section}
                        </select>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <select class="select2 input-medium" name="status" ng-model="criteria.elements_per_page" data-label="{t}View{/t}">
                            <option value="10a">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right simple-pagination ng-cloak">
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
                <input type="hidden" name="in_home" ng-model="criteria.in_home">
            </div>
        </div>
    </div>

    <div class="content">
        {render_messages}
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
                                <th class="center hidden-xs hidden-sm" style="width:10px"></th>
                                <th class="left">{t}Title{/t}</th>
                                {if $category=='widget' || $category == 'all'}
                                    <th class="center hidden-xs">{t}Section{/t}</th>
                                {/if}
                                <th class="center hidden-xs">{t}Date{/t}</th>
                                <th class="center hidden-xs">{t}Price{/t}</th>
                                <th class="center hidden-xs" style="width:10px">{t}Favorite{/t}</th>
                                <th class="center hidden-xs" style="width:10px">{t}Home{/t}</th>
                                <th class="center" style="width:10px">{t}Published{/t}</th>
                            </tr>
                        </thead>
                        <tbody {if $category == 'widget'}ui-sortable ng-model="contents"{/if}>
                            <tr ng-if="contents.length == 0">
                                <td class="empty" colspan="10">{t}No available covers.{/t}</td>
                            </tr>
                            <tr ng-if="contents.length > 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }" data-id="[% content.id %]">
                                <td class="checkbox-cell">
                                    <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
                                </td>
                                <td class="center hidden-xs hidden-sm">
                                    <img ng-src="{$KIOSKO_IMG_URL}[% content.path%][% content.thumb_url %]"
                                        title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" style="max-width:80px" class="thumbnail" />
                                </td>
                                <td class="left">
                                    <img ng-src="{$KIOSKO_IMG_URL}[% content.path%][% content.thumb_url %]"
                                        title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" style="max-width:80px" class="thumbnail visible-xs visible-sm" />

                                    <span tooltip="{t}Last editor{/t} [% shvs.extra.authors[content.fk_user_last_editor].name %]">[% content.title%]</span>

                                    <div class="listing-inline-actions">
                                        {acl isAllowed="VIDEO_UPDATE"}
                                        <a class="link" href="[% edit(content.id, 'admin_cover_show') %]">
                                            <i class="fa fa-pencil"></i> {t}Edit{/t}
                                        </a>
                                        {/acl}
                                        {acl isAllowed="VIDEO_DELETE"}
                                        <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                                            <i class="fa fa-trash-o"></i> {t}Remove{/t}
                                        </button>
                                        {/acl}
                                   </div
                                </td>
                                {if $category == 'widget' || $category == 'all'}
                                <td class="center hidden-xs">
                                    [% content.category_name%]
                                </td>
                                {/if}
                                <td class="center hidden-xs">
                                    [% content.date %]
                                </td>
                                <td class="center hidden-xs">
                                    [% content.price | number : 2 %] €
                                </td>
                                {acl isAllowed="KIOSKO_AVAILABLE"}
                                <td class="center hidden-xs">
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading && content.favorite == 1, 'fa-star-o': !content.favorite_loading && content.favorite != 1 }"></i>
                                        <i class="fa fa-times fa-sub text-danger" ng-if="!content.favorite_loading && content.favorite != 1"></i>
                                    </button>
                                </td>
                                {/acl}
                                {acl isAllowed="KIOSKO_HOME"}
                                <td class="right hidden-xs">
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-info': ! content.home_loading && content.in_home == 1, 'fa-home': ! content.home_loading &&content.in_home == 0 }"></i>
                                        <i class="fa fa-times fa-sub text-danger" ng-if="!content.home_loading && content.in_home != 1"></i>
                                    </button>
                                </td>
                                {/acl}
                                {acl isAllowed="KIOSKO_AVAILABLE"}
                                <td class="center">
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }"></i>
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
        <script type="text/ng-template" id="modal-delete">
            {include file="common/modals/_modalDelete.tpl"}
        </script>
        <script type="text/ng-template" id="modal-delete-selected">
            {include file="common/modals/_modalBatchDelete.tpl"}
        </script>
    </div>
</form>
{/block}
