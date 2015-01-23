{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('photo', { content_status: -1, title_like: '', category_name: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Images{/t}
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    {acl isAllowed="PHOTO_CREATE"}
                    <li class="quicklinks">
                        <a class="btn btn-primary" href="{url name=admin_image_new category=$category}">
                            <span class="fa fa-cloud-upload"></span> {t}Upload{/t}
                        </a>
                    </li>
                    {/acl}
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-navbar selected-navbar" class="hidden" ng-class="{ 'collapsed': shvs.selected.length == 0 }">
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
                {acl isAllowed="PHOTO_DELETE"}
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')">
                            <i class="fa fa-trash-o"></i>
                            {t}Delete{/t}
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
                    <input class="no-boarder" name="title" ng-model="shvs.search.title_like" placeholder="{t}Search by title{/t}" type="text"/>
                    <input type="hidden" name="in_home" ng-model="shvs.search.in_home">
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <span class="info">
                    {t}Results{/t}: [% shvs.total %]
                    </span>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks form-inline pagination-links">
                    <div class="btn-group">
                        <button class="btn btn-white" ng-click="pagination.page = pagination.page - 1" ng-disabled="pagination.page - 1 < 1" type="button">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-white" ng-click="pagination.page = pagination.page + 1" ng-disabled="pagination.page == pagination.pages" type="button">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="content">

    {render_messages}

    <div class="grid simple">
        <div class="grid-body no-padding">
            <div ng-include="'files'"></div>
        </div>
    </div>

        <script type="text/ng-template" id="files">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>

        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr>
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th style="width:80px"></th>
                    <th>{t}Information{/t}</th>
                    <th>{t}Created on{/t}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="shvs.contents.length == 0">
                    <td class="empty" colspan="10">
                        <p>
                            <img src="{$params.IMAGE_DIR}/search/search-images.png">
                        </p>
                        {t escape=off}No available images for this search{/t}
                    </td>
                </tr>
                <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents"  ng-class="{ row_selected: isSelected(content.id) }" data-id="[% content.id %]">
                    <td>
                        <checkbox index="[% content.id %]">
                    </td>
                    <td class="thumb">
                        <span ng-click="open('modal-image', null, $index)" class="thumbnail">
                            <span ng-if="content.type_img == 'swf'">
                                <object ng-data="'{$MEDIA_IMG_URL}[% content.path_file %][% content.name %]'" ng-param="{ 'vmode': 'opaque' }"  style="width:100px;height:80px"></object>
                                <img class="image-preview" style="width:16px;height:16px;border:none;"  src="{$params.IMAGE_DIR}flash.gif" />
                            </span>
                            <span ng-if="content.type_img !== 'swf'">
                                <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" path="[% content.path_file + '/' + content.name %]" width="80" transform="zoomcrop,80,80,center,center" class="image-preview"></dynamic-image>
                            </span>
                        </span>
                    </td>
                    <td>
                        <div class="description">
                            <span ng-if="content.description != ''">[% content.description %]</span>
                            <span ng-if="content.description == ''">{t}No available description{/t}</span>
                        </div>

                        <!-- <div class="tags">
                            <img src="{$params.IMAGE_DIR}tag_red.png" />
                            <span ng-if="content.metadata != ''">[% content.metadata %]</span>
                            <span ng-if="content.metadata == ''">{t}No tags{/t}</span>
                        </div>
                        <div class="author" ng-if="content.fk_author !== null">
                            <strong>{t}Author:{/t}</strong> [% shvs.extra.authors[content.fk_author].name %]
                        </div> -->

                        <div>
                            <div class="listing-inline-actions">
                                {acl isAllowed="PHOTO_UPDATE"}
                                <a class="link" href="[% edit(content.id, 'admin_photo_show') %]">
                                    <i class="fa fa-pencil"></i> {t}Edit{/t}
                                </a>
                                {/acl}
                                {acl isAllowed="PHOTO_DELETE"}
                                <button class="del link link-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                    <i class="fa fa-trash-o"></i> {t}Remove{/t}
                                </button>
                                {/acl}
                                <a href="{$MEDIA_IMG_URL}[% content.path_file %][% content.name %]" target="_blank">
                                    <span class="fa fa-download"></span> {t}Download{/t}
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="left nowrap">
                        [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pull-left" ng-if="shvs.contents.length > 0">
                            {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
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

        <script type="text/ng-template" id="modal-delete">
            {include file="common/modals/_modalDelete.tpl"}
        </script>

        <script type="text/ng-template" id="modal-delete-selected">
            {include file="common/modals/_modalBatchDelete.tpl"}
        </script>

        <script type="text/ng-template" id="modal-image">
            <div class="modal-header">
              <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
              <h3>{t}Image preview{/t}</h3>
            </div>
            <div class="modal-body">
                <div class="resource">
                    <span ng-if="contents[index].type_img == 'swf'">
                        <object ng-data="'{$MEDIA_IMG_URL}[% contents[index].path_file %][% contents[index].name %]'" ng-param="{ 'vmode': 'opaque' }"></object>
                    </span>
                    <span ng-if="contents[index].type_img !== 'swf'">
                        <img ng-src="{$MEDIA_IMG_URL}[% contents[index].path_file + contents[index].name %]"/>
                    </span>
                </div>

                <div class="details">
                    <h4 class="description">
                        <span ng-if="contents[index].description != ''">[% contents[index].description %]</span>
                        <span ng-if="contents[index].description == ''">{t}No available description{/t}</span>
                    </h4>
                    <div><strong>{t}Filename{/t}</strong> [% contents[index].title %]</div>
                    <div class="tags">
                        <img src="{$params.IMAGE_DIR}tag_red.png" />
                        <span ng-if="contents[index].metadata != ''">[% contents[index].metadata %]</span>
                        <span ng-if="contents[index].metadata == ''">{t}No tags{/t}</span>
                    </div>
                    <span class="author" ng-if="contents[index].author != ''">
                        <strong>{t}Author:{/t}</strong> {$photo->author_name|clearslash|default:""}
                    </span>
                    <div><strong>{t}Created on{/t}</strong> [% contents[index].created %]</div>

                    <div><strong>{t}Resolution:{/t}</strong> [% contents[index].width %] x [% contents[index].height %] (px)</div>
                    <div><strong>{t}Size:{/t}</strong> [% contents[index].size %] Kb</div>
                </div>
            </div>
        </script>
</div>
</form>
{/block}
