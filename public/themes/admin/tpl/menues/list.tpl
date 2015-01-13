{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { content_status: -1, renderlet: -1 }, 'name', 'asc', 'backend_ws_menus_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-list-alt fa-lg"></i>
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

    <div class="page-navbar selected-navbar" ng-class="{ 'collapsed': shvs.selected.length == 0 }">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                      <button class="btn btn-link" ng-click="shvs.selected = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
                        <i class="fa fa-check fa-lg"></i>
                      </button>
                    </li>
                     <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h4>
                            [% shvs.selected.length %] {t}items selected{/t}
                        </h4>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    {acl isAllowed="ADVERTISEMENT_DELETE"}
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
                    <div class="spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
                </div>
                <div class="table-wrapper">
                    <table class="table table-hover no-margin" ng-if="!loading">
                        <thead>
                            <tr>
                                <th class="pointer" style="width:15px;">
                                    <checkbox select-all="true"></checkbox>
                                </th>
                                <th class="pointer">{t}Name{/t}</th>
                                {if count($menu_positions) > 1}
                                <th class="pointer nowrap center" style="width:100px;">{t}Position assigned{/t}</th>
                                {/if}
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                                <td class="center">
                                    <checkbox index="[% content.id %]">
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
            <div class="grid-footer clearfix">
                <div class="pull-left pagination-info" ng-if="shvs.contents.length > 0">
                    {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                </div>
                <div class="pull-right" ng-if="shvs.contents.length > 0">
                    <pagination class="no-margin" max-size="0" direction-links="true"  items-oer-page="shvs.elements_per_page" on-select-page="selectPage(page, 'backend_ws_menus_list')" ng-model="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                </div>
                <span ng-if="shvs.contents.length == 0">&nbsp;</span>
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
