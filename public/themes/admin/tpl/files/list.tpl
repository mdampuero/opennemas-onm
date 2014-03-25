{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="services.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="content-modal.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="content.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="fos-js-routing.js" language="javascript" bundle="backend" basepath="js/services"}
{/block}

{block name="footer-js" append}
    <script>
    var file_manager_urls = {
        batchDelete: '{url name=admin_files_batchdelete category=$category page=$page}',
        savePositions: '{url name=admin_files_save_positions category=$category page=$page}'
    }

    jQuery('#save-positions').on('click', function(e, ui){
        e.preventDefault();

        var items_id = [];
        jQuery( "tbody.sortable tr" ).each(function(){
            items_id.push(jQuery(this).data("id"))
        });

        jQuery.ajax(file_manager_urls.savePositions, {
           type: "POST",
           data: { positions : items_id }
        }).done(function( msg ){

               jQuery('#warnings-validation').html("<div class=\"success\">"+msg+"</div>")
                                             .effect("highlight", { }, 3000);

       });
        return false;
    });

    </script>
{/block}

{block name="content"}
<form action="{url name=admin_files}" method="GET" name="formulario" id="formulario"  ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('attachment', { available: -1, category_name: -1, title_like: '', in_home: {if $category == 'widget'}1{else}-1{/if} }, 'title', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Files{/t} ::</h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if $category == 'widget'}{t}Widget Home{/t}{else}{t}Listing{/t}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        <h4>{t}Special elements{/t}</h4>
                        <a href="{url name=admin_files_widget}" {if $category=='widget'}class="active"{/if}>{t}Widget Home{/t}</a>
                        <a href="{url name=admin_files}" {if $category !=='widget'}class="active"{/if}>{t}Listing{/t}</a>
                    </div>
                </div>
            </div>
            {if $category != ''}
            <ul class="old-button">

                <li>
                <a href="{url name=admin_files_create category=$category page=$page}" title="{t}Upload file{/t}">
                        <img src="{$params.IMAGE_DIR}upload.png" border="0" /><br />
                        {t}Upload file{/t}
                    </a>
                </li>
                <li ng-if="selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="FILES_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="batchToggleAvailable(1, 'backend_ws_contents_batch_toggle_available')">
                                <i class="icon-eye-open"></i>
                                {t}Publish{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="batchToggleAvailable(0, 'backend_ws_contents_batch_toggle_available')">
                                <i class="icon-eye-close"></i>
                                {t}Unpublish{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="FILES_DELETE"}
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

                {acl isAllowed="VIDEO_WIDGET"}
                    {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" id="save-widget-positions" title="{t}Save positions{/t}">
                                <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save positions{/t}"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_files_statistics}">
                        <img src="{$params.IMAGE_DIR}statistics.png" alt="Statistics"><br>Statistics
                    </a>
                </li>
            </ul>
            {/if}
        </div>
    </div>
    <div class="wrapper-content">
        <ul class="pills">
            <li>
                <a href="{url name=admin_files_widget}" {if $category eq 'widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
            </li>
            <li>
                <a href="{url name=admin_files}" {if $category eq 'all'}class="active"{/if}>{t}All categories{/t}</a>
            </li>
        </ul>

        {render_messages}

        <div class="table-info clearfix">
            {acl hasCategoryAccess=$category}<div class="pull-left"><strong>{t}[% total %] polls{/t}</strong></div>{/acl}
            <div class="pull-right">
                <div class="form-inline">
                    <input type="text" placeholder="{t}Search by title{/t}" name="title" ng-model="filters.search.title_like"/>
                    <label for="category">{t}Category:{/t}</label>
                    <select class="input-medium select2" id="category" ng-model="filters.search.category_name">
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
                    {t}Status:{/t}
                    <select class="select2 input-medium" name="status" ng-model="filters.search.available">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>

                    <input type="hidden" name="in_home" ng-model="filters.search.in_home">
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
               <thead ng-if="contents.length > 0">
                   <tr>
                        <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                        <th style="width:20px">{t}Path{/t}</th>
                        <th>{t}Title{/t}</th>
                        <th class="left">{t}Category{/t}</th>
                        {if $category!='widget'} <th class="center" style="width:20px;">{t}Favorite{/t}</th>{/if}
                        <th class="center" style="width:20px;">{t}Home{/t}</th>
                        <th class="center" style="width:20px">{t}Published{/t}</th>
                        <th style="width:10px" class="center">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody class="sortable">
                    <tr ng-if="contents.length == 0">
                        <td class="empty" colspan="10">{t}No available files.{/t}</td>
                    </tr>

                    <tr ng-if="contents.length >= 0" ng-repeat="content in contents" data-id="[% content.id %]">
                        <td>
                            <input type="checkbox" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)">
                        </td>
                        <td>
                            <a href="{$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}[% content.path %]" target="_blank">
                                {t}[Link]{/t}
                            </a>
                        </td>
                        <td>
                            [% content.title %]
                        </td>
                        <td class="left">
                            [% content.category_name %]
                        </td>
                        {acl isAllowed="FILE_AVAILABLE"}
                        <td class="center">
                            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.available == 1, unpublished: content.available == 0 }" ng-click="toggleAvailable(content.id, $index, 'backend_ws_content_toggle_available')" type="button"></button>
                        </td>
                        {/acl}
                        {if $category != 'widget'}
                        {acl isAllowed="FILE_AVAILABLE"}
                        <td class="center">
                            <button class="btn-link" ng-class="{ loading: content.favorite_loading == 1, 'favorite': content.favorite == 1, 'no-favorite': content.favorite != 1 }" ng-click="toggleFavorite(content.id, $index, 'backend_ws_content_toggle_favorite')" type="button"></button>
                        </td>
                        {/acl}
                        {/if}
                        {acl isAllowed="FILE_AVAILABLE"}
                        <td class="center">
                            <button class="btn-link" ng-class="{ 'loading': content.home_loading == 1, 'go-home': content.in_home == 1, 'no-home': content.in_home == 0 }" ng-click="toggleInHome(content.id, $index, 'backend_ws_content_toggle_in_home')" type="button"></button>
                        </td>
                        {/acl}
                        <td class="right">
                            <div class="btn-group">
                                {acl isAllowed="FILE_UPDATE"}
                                <button class="btn" ng-click="edit(content.id, 'admin_poll_show')" type="button">
                                    <i class="icon-pencil"></i>
                                </button>
                                {/acl}
                                {acl isAllowed="FILE_DELETE"}
                                <button class="del btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                    <i class="icon-trash icon-white"></i>
                                </button>
                                {/acl}
                           </ul>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" class="center">
                            <div class="pagination">
                                {$pagination->links}
                            </div>
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
    </div>
</form>
 <script>
    // <![CDATA[
        {if $category eq 'widget'}
            jQuery(document).ready(function() {
                makeSortable();
            });
        {/if}
    // ]]>

</script>

{/block}
