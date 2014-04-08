{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('photo', { content_status: -1, title_like: '', category_name: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Images{/t}</h2>
            </div>
            <ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="PHOTO_DELETE"}
                            <li class="divider"></li>
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="shvs.selected.length > 0"></li>
                {acl isAllowed="PHOTO_CREATE"}
                <li>
                    <a class="admin_add" href="{url name=admin_image_new category=$category}">
                        <img src="{$params.IMAGE_DIR}upload.png" alt="{t}Upload{/t}"><br />{t}Upload{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            <div class="pull-left">
                <div class="form-inline">
                    <strong>{t}FILTER:{/t}</strong>
                    &nbsp;&nbsp;
                    <input type="text" autofocus placeholder="{t}Search by title{/t}" name="title" ng-model="shvs.search.title_like"/>
                    &nbsp;&nbsp;
                    <input type="hidden" name="in_home" ng-model="shvs.search.in_home">
                </div>
            </div>
        </div>
        <div ng-include="'files'"></div>

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
                    <th class="center" style="width:10px;"></th>
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
                        <span ng-click="open('modal-image', null, $index)">
                            <span ng-if="content.type_img == 'swf'">
                                <object>
                                    <param name="wmode" value="window" value="{$MEDIA_IMG_URL}[% content.path_file%][% content.name %]" />
                                    <embed wmode="window" src="[% content.path_file%][% content.name %]"  width="140" height="80" ></embed>
                                </object>
                                <img class="image-preview" style="width:16px;height:16px;border:none;"  src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif" />
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

                        <div class="tags">
                            <img src="{$params.IMAGE_DIR}tag_red.png" />
                            <span ng-if="content.metadata != ''">[% content.metadata %]</span>
                            <span ng-if="content.metadata == ''">{t}No tags{/t}</span>
                        </div>

                        <div class="author" ng-if="content.fk_author !== null">
                            <strong>{t}Author:{/t}</strong> [% shvs.extra.authors[content.fk_author].name %]
                        </div>

                        <div>
                            <span class="url">
                                <a href="{$MEDIA_IMG_URL}[% content.path_file %]/[% content.name %]" target="_blank">
                                    {t}[Link]{/t}
                                </a>
                            </span>
                        </div>
                    </td>
                    <td class="left nowrap">
                        [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            {acl isAllowed="PHOTO_UPDATE"}
                            <a class="btn" href="[% edit(content.id, 'admin_photo_show') %]">
                                <i class="icon-pencil"></i>
                            </a>
                            {/acl}
                            {acl isAllowed="PHOTO_DELETE"}
                            <button class="del btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                <i class="icon-trash icon-white"></i>
                            </button>
                            {/acl}
                        </div>
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
                    <img ng-src="{$MEDIA_IMG_URL}/[% contents[index].path_file + '/' + contents[index].name %]"/>
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
