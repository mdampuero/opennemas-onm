{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_trash}" method="post" id="trashform"  ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('content', { in_litter: 1, title_like: '', content_type_name: -1 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">


<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Trash{/t}
                    </h4>
                </li>
            </ul>
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
                {acl isAllowed="TRASH_ADMIN"}
                <li class="quicklinks">
                    <a href="#" ng-click="open('modal-batch-restore', 'backend_ws_contents_batch_restore_from_trash')">
                        <i class="fa fa-check fa-lg"></i>
                        {t}Restore{/t}
                    </a>
                </li>
                <li class="quicklinks">
                    <a href="#" ng-click="open('modal-batch-remove-permanently', 'backend_ws_contents_batch_remove_permanently')">
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
                    <input class="no-boarder" type="text" name="title" ng-model="shvs.search.title_like" placeholder="{t}Filter by name{/t}" />
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <select id="content_type_name" ng-model="shvs.search.content_type_name" data-label="{t}Content Type{/t}">
                        <option value="-1">{t}-- All --{/t}</option>
                        {is_module_activated name="ARTICLE_MANAGER"}
                        {acl isAllowed="ARTICLE_TRASH"}
                        <option value="article">{t}Articles{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="OPINION_MANAGER"}
                        {acl isAllowed="OPINION_TRASH"}
                            <option value="opinion">{t}Opinions{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="OPINION_MANAGER"}
                        {acl isAllowed="LETTER_TRASH"}
                            <option value="letter">{t}Letters{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="ADS_MANAGER"}
                        {acl isAllowed="ADVERTISEMENT_TRASH"}
                            <option value="advertisement">{t}Advertisements{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="KIOSKO_MANAGER"}
                        {acl isAllowed="KIOSKO_TRASH"}
                            <option value="kiosko">{t}Covers{/t}</option>
                        {/acl}
                        {/is_module_activated}

                        {is_module_activated name="ALBUM_MANAGER"}
                        {acl isAllowed="ALBUM_TRASH"}
                            <option value="album">{t}Albums{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="IMAGE_MANAGER"}
                        {acl isAllowed="PHOTO_TRASH"}
                            <option value="photo">{t}Images{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="VIDEO_MANAGER"}
                        {acl isAllowed="VIDEO_TRASH"}
                            <option value="video">{t}Videos{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="FILE_MANAGER"}
                        {acl isAllowed="FILE_DELETE"}
                            <option value="attachment">{t}Files{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="POLL_MANAGER"}
                        {acl isAllowed="POLL_DELETE"}
                            <option value="poll">{t}Polls{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="SPECIAL_MANAGER"}
                        {acl isAllowed="SPECIAL_DELETE"}
                            <option value="special">{t}Specials{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="STATIC_PAGES_MANAGER"}
                        {acl isAllowed="STATIC_PAGE_DELETE"}
                            <option value="static_page">{t}Static Pages{/t}</option>
                        {/acl}{/is_module_activated}

                        {is_module_activated name="WIDGET_MANAGER"}
                        {acl isAllowed="WIDGET_DELETE"}
                            <option value="widget">{t}Widgets{/t}</option>
                        {/acl}{/is_module_activated}
                    </select>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="content">
    {render_messages}

    <div class="grid simple">

        <div class="grid-body no-padding">
            <div ng-include="'trash_list'"></div>
        </div>

    </div>
        <script type="text/ng-template" id="trash_list">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
               <tr>
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th class="left">{t}Content type{/t}</th>
                    <th class='left'>{t}Title{/t}</th>
                    <th style="width:40px">{t}Section{/t}</th>
                    <th class="left" style="width:110px;">{t}Date{/t}</th>
                    <th class="nowrap center" style="width:40px;">{t}Actions{/t}</th>
               </tr>
            </thead>
            <tbody>
                <tr ng-if="shvs.contents.length == 0">
                    <td class="center"colspan=6>
                        {t}There is no elements in the trash{/t}
                    </td>
                </tr>
                <tr ng-if="shvs.contents.length >= 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                    <td>
                        <checkbox index="[% content.id %]">
                    </td>
                    <td>
                        <strong>[% content.content_type_l10n_name %]</strong>
                    </td>
                    <td>
                        [% content.title %]
                        <div class="listing-inline-actions">
                            <a class="link pointer" ng-click="open('modal-restore-from-trash', 'backend_ws_content_restore_from_trash', $index)" type="button" title="{t}Restore{/t}">
                                <i class="fa fa-retweet"></i>
                                {t}Restore{/t}
                            </a>

                            <button class="link link-danger" ng-click="open('modal-remove-permanently', 'backend_ws_content_remove_permanently', $index)" type="button" title="{t}Restore{/t}">
                                <i class="fa fa-trash-o"></i>
                                {t}Remove permanently{/t}
                            </button>
                        </div>
                    </td>
                    <td class="left">[% content.category_name %]</td>
                    <td class="center nowrap">
                        [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pull-left" ng-if="shvs.contents.length > 0">
                            {t}Showing{/t} [% (shvs.page - 1) * 10 %]-[% (shvs.page * 10) < shvs.total ? shvs.page * 10 : shvs.total %] {t}of{/t} [% shvs.total %]
                        </div>
                        <div class="pull-right" ng-if="shvs.contents.length > 0">
                            <pagination max-size="0" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                        </div>
                        <span ng-if="shvs.contents.length == 0">&nbsp;</span>
                    </td>
                </tr>
            </tfoot>
        </table>
        </script>

        <script type="text/ng-template" id="modal-restore-from-trash">
            {include file="common/modals/_modalRestoreFromTrash.tpl"}
        </script>

        <script type="text/ng-template" id="modal-remove-permanently">
            {include file="common/modals/_modalRemovePermanently.tpl"}
        </script>

        <script type="text/ng-template" id="modal-batch-restore">
            {include file="common/modals/_modalBatchDelete.tpl"}
        </script>

        <script type="text/ng-template" id="modal-batch-remove-permanently">
            {include file="common/modals/_modalBatchRemovePermanently.tpl"}
        </script>

    </div>
</form>
{/block}
