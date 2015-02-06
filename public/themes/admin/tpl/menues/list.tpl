{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListController" ng-init="init(null, { content_status: -1, renderlet: -1 }, 'name', 'asc', 'backend_ws_menus_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-list-alt"></i>
                            {t}Menus{/t}
                        </h4>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            {acl isAllowed="MENU_CREATE"}
                            <a href="{url name=admin_menu_create}" class="btn btn-primary">
                                <i class="fa fa-plus"></i>
                                {t}Create menu{/t}
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
                      <button class="btn btn-link" ng-click="selected.contents = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
                        <i class="fa fa-check fa-lg"></i>
                      </button>
                    </li>
                     <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h4>
                            [% selected.contents.length %] {t}items selected{/t}
                        </h4>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    <li class="quicklinks">
                        <button class="btn btn-link" ng-click="deselectAll()" tooltip="{t}Clear selection{/t}" tooltip-placement="bottom" type="button">
                          {t}Deselect{/t}
                        </button>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
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
                                <th style="width:15px;">
                                  <div class="checkbox checkbox-default">
                                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                      <label for="select-all"></label>
                                  </div>
                                </th>
                                <th class="pointer">{t}Name{/t}</th>
                                {if count($menu_positions) > 1}
                                <th class="pointer nowrap center" style="width:100px;">{t}Position assigned{/t}</th>
                                {/if}
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                                <td class="center">
                                    <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
                                </td>
                                <td>
                                    [% content.name %]
                                    <div class="listing-inline-actions">
                                        <a class="link" href="[% edit(content.id, 'admin_menu_show') %]" title="{t}Edit{/t}">
                                            <i class="fa fa-pencil"></i>{t}Edit{/t}
                                        </a>
                                        <button class="link link-danger" ng-click="open('modal-delete', 'backend_ws_menu_delete', $index)" type="button">
                                            <i class="fa fa-trash-o"></i>{t}Delete{/t}
                                        </button>
                                    </div>
                                </td>
                                {if count($menu_positions) > 1}
                                <td class="left">
                                    <span ng-if="content.position">
                                        [% content.position %]
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
            <div class="grid-footer clearfix ng-cloak">
                <div class="pull-left pagination-info" ng-if="contents.length > 0">
                    {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
                </div>
                <div class="pull-right" ng-if="contents.length > 0">
                    <pagination class="no-margin" max-size="0" direction-links="true"  items-oer-page="pagination.epp" on-select-page="selectPage(page, 'backend_ws_menus_list')" ng-model="pagination.page" total-items="pagination.total" num-pages="pages"></pagination>
                </div>
                <span ng-if="contents.length == 0">&nbsp;</span>
            </div>
        </div>

    </div><!--fin wrapper-content-->
    <script type="text/ng-template" id="menus">


    </script>
    <script type="text/ng-template" id="modal-delete">
        {include file="common/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
</div>
{/block}
