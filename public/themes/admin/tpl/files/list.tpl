{extends file="base/admin.tpl"}

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
<form action="{url name=admin_files}" method="GET" name="formulario" id="formulario" {$formAttrs|default:""} >
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Files{/t}</h2></div>
            {if $category != ''}
            <ul class="old-button">
                <li>
                <a href="{url name=admin_files_create category=$category page=$page}" title="{t}Upload file{/t}">
                        <img src="{$params.IMAGE_DIR}upload.png" border="0" /><br />
                        {t}Upload file{/t}
                    </a>
                </li>
                {acl isAllowed="FILES_DELETE"}
                <li>
                    <a class="delChecked" data-controls-modal="modal-file-batchDelete" href="#" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="FILES_AVAILABLE"}
                <li>
                    <button id="batch-unpublish" type="submit" name="status" value="0">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
               </li>
               <li>
                   <button id="batch-publish" type="submit" name="status" value="1">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
               </li>
                {/acl}
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
            <div>
                <div class="right form-inline">
                    <label>{t}Status:{/t}
                    <select name="listing-status" class="form-filters" {if $category == 'widget'} disabled="disabled"{/if}>
                        <option value="" {if  !isset($listingStatus)}selected{/if}> {t}All{/t} </option>
                        <option value="0" {if $listingStatus eq '0'}selected{/if}> {t}Unpublished{/t} </option>
                        <option value="1" {if $listingStatus eq '1'}selected{/if}> {t}Published{/t} </option>
                    </select>
                    </label>

                    <div class="input-append">
                        <label for="category">
                            {t}Category:{/t}
                            <select name="category" class="form-filters">
                                <option value="all" {if $category eq '0'}selected{/if}> {t}All{/t} </option>
                                {section name=as loop=$allcategorys}
                                     <option value="{$allcategorys[as]->pk_content_category}" {if isset($category) && ($category eq $allcategorys[as]->pk_content_category)}selected{/if}>{$allcategorys[as]->title}</option>
                                     {section name=su loop=$subcat[as]}
                                            {if $subcat[as][su]->internal_category eq 1}
                                                <option value="{$subcat[as][su]->pk_content_category}"
                                                {if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                            {/if}
                                        {/section}
                                {/section}
                            </select>
                        </label>
                        <button type="submit" id="search" class="btn"><i class="icon-search"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
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
                {section name=c loop=$attaches}
                 <tr data-id="{$attaches[c]->id}">
                    <td class="center">
                        <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$attaches[c]->id}"  style="cursor:pointer;" >
                    </td>
                    <td>
                        <a href="{$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}{$attaches[c]->path}" target="_blank">
                            {t}[Link]{/t}
                        </a>
                    </td>
                    <td>
                        {$attaches[c]->title|clearslash}
                    </td>
                    <td class="left">
                        {$attaches[c]->category_name|clearslash}
                    </td>
                    {if $category != 'widget'}
                    <td class="center">
                    {acl isAllowed="FILE_AVAILABLE"}
                        {if $attaches[c]->favorite == 1}
                           <a href="{url name=admin_files_toggle_favorite id=$attaches[c]->id status=0 category=$category page=$page}" class="favourite_on" title="{t}Take out from frontpage{/t}"></a>
                        {else}
                            <a href="{url name=admin_files_toggle_favorite id=$attaches[c]->id status=1 category=$category page=$page}" class="favourite_off" title="{t}Put in frontpage{/t}"></a>
                        {/if}
                    {/acl}
                    </td>
                    {/if}
                    <td class="center">
                    {acl isAllowed="FILE_AVAILABLE"}
                        {if $attaches[c]->in_home == 1}
                            <a href="{url name=admin_files_toggle_in_home id=$attaches[c]->id status=0 category=$category page=$page}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="{url name=admin_files_toggle_in_home id=$attaches[c]->id status=1 category=$category page=$page}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                    </td>
                    <td class="center">
                    {acl isAllowed="FILE_AVAILABLE"}
                        {if $attaches[c]->available == 1}
                            <a href="{url name=admin_files_toggle_available id=$attaches[c]->id status=0 category=$category page=$page}" title="Publicado">
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                            </a>
                        {else}
                            <a href="{url name=admin_files_toggle_available id=$attaches[c]->id status=1 category=$category page=$page}" title="Pendiente">
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                            </a>
                        {/if}
                    {/acl}
                    </td>
                    <td class="rigth nowrap">
                        <div class="btn-group">
                            {acl isAllowed="FILE_UPDATE"}
                                <a class="btn"  href="{url name=admin_file_show id=$attaches[c]->id}" title="{t}Edit file{/t}">
                                    <i class="icon-pencil"></i> Edit
                                </a>
                            {/acl}
                            {acl isAllowed="FILE_DELETE"}
                                <a class="btn btn-danger del"
                                   data-url="{url name=admin_files_delete id=$attaches[c]->id}"
                                   data-title="{$attaches[c]->title|capitalize}"  href="{url name=admin_files_delete id=$attaches[c]->id}" >
                                    <i class="icon-trash icon-white"></i>
                                </a>
                            {/acl}
                        </div>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td class="empty" colspan="8">
                        {t}There is not files available here.{/t}
                    </td>
                </tr>
                {/section}
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
    </div>
</form>
 <script>
    // <![CDATA[
        jQuery('#batch-publish').on('click', function(){
            jQuery('#formulario').attr('action', '{url name=admin_files_batchpublish}');
        });
        jQuery('#batch-unpublish').on('click', function(){
            jQuery('#formulario').attr('action', '{url name=admin_files_batchpublish}');
        });

        {if $category eq 'widget'}
            jQuery(document).ready(function() {
                makeSortable();
            });
        {/if}
    // ]]>

</script>

{include file="files/modals/_modalDelete.tpl"}
{include file="files/modals/_modalBatchDelete.tpl"}
{include file="files/modals/_modalAccept.tpl"}
{/block}
