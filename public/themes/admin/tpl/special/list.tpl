{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
{/block}

{block name="content"}
<form action="{url name=admin_specials}" method="get" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Specials{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if $category == 'widget'}{t}WIDGET HOME{/t}{elseif $category == 'all'}{t}All categories{/t}{else}{$datos_cat[0]->title}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        <h4>{t}Special elements{/t}</h4>
                        <a href="{url name=admin_specials_widget}" {if $category=='widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
                        {include file="common/drop_down_categories.tpl" home={url name=admin_specials l=1}}
                    </div>
                </div>
            </div>
            <ul class="old-button">
                {acl isAllowed="SPECIAL_DELETE"}
                <li>
                    <button class="batch-delete" data-controls-modal="modal-special-batchDelete" title="{t}Delete{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </button>
                </li>
                {/acl}
                {acl isAllowed="SPECIAL_AVAILABLE"}
                <li>
                    <button id="batch-unpublish" type="submit" name="status" value="0">
                       <img src="{$params.IMAGE_DIR}publish_no.gif" alt="{t}Unpublish{/t}" /><br />{t}Unpublish{/t}
                    </button>
                </li>
                <li>
                    <button id="batch-publish" type="submit"  name="status" value="1">
                       <img src="{$params.IMAGE_DIR}publish.gif" alt="{t}Publish{/t}" /><br />{t}Publish{/t}
                    </button>
                </li>
                {/acl}
                {acl isAllowed="SPECIAL_WIDGET"}
                     {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" class="admin_add" onClick="javascript:saveSortPositions('{url name=admin_special_widget_save_positions category=$category page=$page}');" title="Guardar Positions" alt="Guardar Posiciones">
                                <img border="0" src="{$params.IMAGE_DIR}save.png" alt="Guardar Posiciones"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
                {acl isAllowed="SPECIAL_SETTINGS"}
                    <li>
                        <a href="{url name=admin_specials_config}" class="admin_add" title="{t}Config special module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Settings{/t}
                        </a>
                    </li>
                {/acl}
                {acl isAllowed="SPECIAL_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_special_create}">
                        <img src="{$params.IMAGE_DIR}special.png" alt="Nuevo Special"><br />{t}New special{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}
        <div id="warnings-validation"></div>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>

                    <th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th class="title">{t}Title{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    {if $category=='widget' || $category=='all'}<th style="width:65px;" class="center">{t}Section{/t}</th>{/if}
                    <th class="center" style="width:100px;">Created</th>
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    {acl isAllowed="SPECIAL_FAVORITE"}
                        {if $category!='widget'} <th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}
                    {/acl}
                    {acl isAllowed="SPECIAL_HOME"}
                        <th class="center" style="width:35px;">{t}Home{/t}</th>
                    {/acl}
                    <th class="right" style="width:110px;">{t}Actions{/t}</th>
                </tr>
            </thead>
             <tbody class="sortable">
            {foreach from=$specials item=special}
            <tr data-id="{$special->pk_special}">
                <td class="center">
                    <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$special->id}"  style="cursor:pointer;" >
                </td>
                <td>
                    <a href="{url name=admin_special_show id=$special->pk_special}" title="{$special->title|clearslash}">
                        {$special->title|clearslash}
                    </a>
                </td>
                 <td class="center">
                    {$special->views}
                </td>
                {if $category=='widget' || $category=='all'}
                    <td class="center">
                         {$special->category_title}
                    </td>
                {/if}
                <td class="center">
                         {$special->created}
                </td>
                <td class="center">
                {acl isAllowed="SPECIAL_AVAILABLE"}
                    {if $special->available == 1}
                    <a href="{url name=admin_special_toggleavailable id=$special->pk_special status=0 category=$category page=$page}" title="{t}Published{/t}">
                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" />
                    </a>
                    {else}
                    <a href="{url name=admin_special_toggleavailable id=$special->pk_special status=1 category=$category page=$page}" title="{t}Pending{/t}">
                        <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Pending{/t}"/>
                    </a>
                    {/if}
                {/acl}
                </td>
                {if $category!='widget'}
                    {acl isAllowed="SPECIAL_FAVORITE"}
                        <td class="center">
                            {if $special->favorite == 1}
                               <a href="{url name=admin_special_togglefavorite id=$special->pk_special status=0 category=$category page=$page}" class="favourite_on" title="{t}Take out from frontpage{/t}"></a>
                            {else}
                                <a href="{url name=admin_special_togglefavorite id=$special->pk_special status=1 category=$category page=$page}" class="favourite_off" title="{t}Put in frontpage{/t}"></a>
                            {/if}
                        </td>
                    {/acl}
                {/if}
                {acl isAllowed="SPECIAL_HOME"}
                    <td class="center">
                        {if $special->in_home == 1}
                           <a href="{url name=admin_special_toggleinhome id=$special->pk_special status=0 category=$category page=$page}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="{url name=admin_special_toggleinhome id=$special->pk_special status=1 category=$category page=$page}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    </td>
                {/acl}
                <td class="right">
                    <div class="btn-group">
                       {acl isAllowed="SPECIAL_UPDATE"}
                        <a class="btn"
                            href="{url name=admin_special_show id=$special->id}"
                            title="{t}Edit{/t}" >
                           <i class="icon-pencil"></i>{t}Edit{/t}
                        </a>
                       {/acl}

                       {acl isAllowed="SPECIAL_DELETE"}
                        <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                            data-url="{url name=admin_special_delete id=$special->id}"
                            data-title="{$special->title|capitalize}"
                            href="{url name=admin_special_delete id=$special->id}"
                            title="{t}Delete{/t}">
                           <i class="icon-trash icon-white"></i>
                        </a>
                       {/acl}
                    </ul>
                </td>

            </tr>
            {foreachelse}
            <tr>
                <td class="empty" colspan=9>{t}There is no specials yet{/t}</td>
            </tr>
            {/foreach}
            </tbody>
            <tfoot>
              <tr>
                  <td colspan="9" >
                    <div class="pagination">
                        {$pagination->links|default:""}
                    </div>
                  </td>
              </tr>
            </tfoot>
        </table>

        <input type="hidden" name="page" id="page" value="{$page|default:1}" />
        <input type="hidden" name="category" id="category" value="{$category}" />
    </div>
</form>
    <script>
    // <![CDATA[
        jQuery('#batch-publish, #batch-unpublish').on('click', function(){
            jQuery('#formulario').attr('action', "{url name=admin_specials_batchpublish category=$category page=$page}");
        });

        {if $category eq 'widget'}
            jQuery(document).ready(function() {
                makeSortable();
            });
        {/if}
    // ]]>
    </script>

    {include file="special/modals/_modalDelete.tpl"}
    {include file="special/modals/_modalBatchDelete.tpl"}
    {include file="special/modals/_modalAccept.tpl"}
{/block}
