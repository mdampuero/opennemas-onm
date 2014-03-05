{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
    <script>
    var cover_manager_urls = {
        batchDelete: '{url name=admin_covers_batchdelete category=$category page=$page}',
        saveWidgetPositions: '{url name=admin_covers_savepositions category=$category page=$page}'
    }
    </script>
{/block}

{block name="content"}
<form action="{url name=admin_covers category=$category page=$page}" method="get" name="formulario" id="formulario">

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}ePapers{/t} :: </h2>
            <div class="section-picker">
                <div class="title-picker btn"><span class="text">{if $category == 'widget'}{t}WIDGET HOME{/t}{elseif $category == 'all'}{t}All categories{/t}{else}{$datos_cat[0]->title}{/if}</span> <span class="caret"></span></div>
                <div class="options">
                    <h4>{t}Special elements{/t}</h4>
                    <a href="{url name=admin_covers_widget}" {if $category=='widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
                    {include file="common/drop_down_categories.tpl" home={url name=admin_covers l=1}}
                </div>
            </div>
        </div>
        <ul class="old-button">
            {acl isAllowed="KIOSKO_DELETE"}
            <li>
                <a class="batch-delete-button" data-controls-modal="modal-kiosko-batchDelete" href="#" title="{t}Delete{/t}">
                    <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="KIOSKO_AVAILABLE"}
            <li>
                <button name="status" value="0" id="batch-publish" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                </button>
            </li>
            <li>
                <button name="status" value="1" id="batch-unpublish" type="submit">
                    <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                </button>
            </li>
            {/acl}
            {acl isAllowed="KIOSKO_CREATE"}
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_cover_create}" title="{t}New cover{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}article_add.png" title="Nueva" alt="Nueva"><br />{t}New ePaper{/t}
                </a>
            </li>
            {/acl}
            {if $category eq 'widget'}
            {acl isAllowed="KIOSKO_HOME"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:saveSortPositions('{url name=admin_covers_savepositions}');" title="Guardar Positions" alt="Guardar Posiciones">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar Cambios" alt="Guardar Posiciones"><br />{t}Save positions{/t}
                    </a>
                </li>
            {/acl}
            {/if}
            {acl isAllowed="KIOSKO_ADMIN"}
            <li class="separator"></li>
                <li>
                    <a href="{url name=admin_covers_config}" title="{t}Config covers module{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Settings{/t}
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

        {if count($covers) > 0}
        <thead>
            <tr>
                <th><input type="checkbox" class="toggleallcheckbox"></th>
                <th class="center">{t}Thumbnail{/t}</th>
                <th class="center">{t}Title{/t}</th>
                {if $category=='widget' || $category == 'all'}
                    <th class="center">{t}Section{/t}</th>
                {/if}
                <th class="center">{t}Date{/t}</th>
                <th class="center">{t}Price{/t}</th>
                <th class="center">{t}Last editor{/t}</th>
                <th class="center">{t}Published{/t}</th>
                <th class="center">{t}Favorite{/t}</th>
                <th class="center">{t}Home{/t}</th>
                <th class="center">{t}Actions{/t}</th>
            </tr>
        </thead>
        {else}
        <thead>
            <tr>
                <th colspan="11">
                    &nbsp;
                </th>

            </tr>
        </thead>
        {/if}
        <tbody class="sortable">
        {foreach from=$covers item=cover}
        <tr data-id="{$cover->pk_kiosko}">
            <td class="center">
                <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$cover->id}"  style="cursor:pointer;" >
            </td>
            <td class="center">
                <img src="{$KIOSKO_IMG_URL}{$cover->path}{$cover->name|regex_replace:"/.pdf$/":".jpg"}"
                    title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" style="height:180px"/>
            </td>
            <td class="center">
                {$cover->title|clearslash}
            </td>
            {if $category == 'widget' || $category == 'all'}
            <td class="center">
                 {$cover->category_title}
            </td>
            {/if}
            <td class="center">
                {$cover->date}
            </td>
            <td class="center">
                {$cover->price|number_format:2:".":","|default:"0"}€
            </td>
            <td  class="center">
                {$cover->editor}
            </td>
             <td class="center">
                {acl isAllowed="KIOSKO_AVAILABLE"}
                    {if $cover->available == 1}
                        <a href="{url name=admin_cover_toggleavailable id=$cover->id status=0 page=$page category=$category}" title="Publicado">
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                        </a>
                    {else}
                        <a href="{url name=admin_cover_toggleavailable id=$cover->id status=1 page=$page category=$category}" title="Pendiente">
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                        </a>
                    {/if}
                {/acl}
            </td>
            <td class="center">
            {acl isAllowed="KIOSKO_AVAILABLE"}
                {if $cover->favorite == 1}
                    <a href="{url name=admin_cover_togglefavorite id=$cover->id status=0 page=$page category=$category}" class="favourite_on" title="Quitar de favorito"></a>
                {else}
                    <a href="{url name=admin_cover_togglefavorite id=$cover->id status=1 page=$page category=$category}" class="favourite_off" title="Poner de favorito"></a>
                {/if}
            {/acl}
            </td>
            <td class="center">
            {acl isAllowed="KIOSKO_HOME"}
                {if $cover->in_home == 1}
                    <a href="{url name=admin_cover_toggleinhome id=$cover->id status=0 page=$page category=$category}" class="no_home" title="{t}Take out from home{/t}"></a>
                {else}
                    <a href="{url name=admin_cover_toggleinhome id=$cover->id status=1 page=$page category=$category}" class="go_home" title="{t}Put in home{/t}"></a>
                {/if}
            {/acl}
            </td>
            <td class="center">
                <div class="btn-group">
                    {acl isAllowed="KIOSKO_UPDATE"}
                    <a class="btn" href="{url name=admin_cover_show id=$cover->pk_kiosko}" title="{t}Edit{/t}">
                        <i class="icon-pencil"></i> {t}Edit{/t}
                    </a>
                    {/acl}
                    {acl isAllowed="KIOSKO_DELETE"}
                    <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                           data-url="{url name=admin_cover_delete id=$cover->pk_kiosko}"
                           href="{url name=admin_cover_delete id=$cover->pk_kiosko}" >
                        <i class="icon-trash icon-white"></i>
                    </a>
                    {/acl}
                </div>
            </td>

        </tr>
        {foreachelse}
        <tr>
            <td class="empty" colspan="11">{t}There is no covers{/t}</td>
        </tr>
        {/foreach}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11" class="center">
                    <div class="pagination">
                        {$pagination->links}
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <input type="hidden" name="category" id="category" value="{$category}" />

</div>
</form>
{include file="covers/modals/_modalDelete.tpl"}
{include file="covers/modals/_modalBatchDelete.tpl"}
{include file="covers/modals/_modalAccept.tpl"}

<script>
// <![CDATA[
    jQuery('#batch-publish, #batch-unpublish').on('click', function(e, ui){
        jQuery('#formulario').attr('action', '{url name=admin_covers_batchpublish}');
    });

    {if $category eq 'widget'}
        jQuery(document).ready(function() {
            makeSortable();
        });
    {/if}
// ]]>
</script>
{/block}
