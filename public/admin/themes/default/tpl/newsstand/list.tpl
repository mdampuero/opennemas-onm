{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario">

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Newsstand Manager{/t}::&nbsp; {if !empty($datos_cat[0])}{$datos_cat[0]->title}{else}{t}Widget Home{/t}{/if}</h2></div>
        <ul class="old-button">
            {acl isAllowed="KIOSKO_DELETE"}
            <li>
                <a class="delChecked" data-controls-modal="modal-kiosko-batchDelete" href="#" title="{t}Delete{/t}">
                    <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="KIOSKO_AVAILABLE"}
            <li>
                    <button value="batchnoFrontpage" name="buton-batchnoFrontpage" id="buton-batchnoFrontpage" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
               </li>
               <li>
                   <button value="batchFrontpage" name="buton-batchFrontpage" id="buton-batchFrontpage" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
            </li>
            {/acl}
            {acl isAllowed="KIOSKO_CREATE"}
            <li class="separator"></li>
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" title="{t}New cover{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}article_add.png" title="Nueva" alt="Nueva"><br />{t}New ePaper{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="KIOSKO_WIDGET"}
             {if $category eq 'favorite'}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:saveSortPositions('{$smarty.server.PHP_SELF}');" title="Guardar Positions" alt="Guardar Posiciones">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar Cambios" alt="Guardar Posiciones"><br />{t}Save positions{/t}
                    </a>
                </li>
            {/if}
            {/acl}
        </ul>
    </div>
</div>


<div class="wrapper-content">

{render_messages}

    {* ZONA MENU CATEGORIAS ******* *}

    <ul class="pills clearfix">
         <li>
            <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=favorite" {if $category=='favorite'}class="active"{/if}>{t}WIDGET{/t}</a>
         </li>
         {include file="menu_categories.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
    </ul>

    <br />
    {* MENSAJES DE AVISO GUARDAR POS******* *}
    <div id="warnings-validation"></div>

    <table class="listing-table">

        {if count($portadas) > 0}
        <thead>
            <tr>
                <th style="width:15px;"><input type="checkbox" id="toggleallcheckbox"></th>
                <th align="center" style="width:100px;">{t}Cover{/t}</th>
                <th align="center">{t}Title{/t}</th>
                {if $category=='widget' || $category=='all'}<th style="width:65px;" class="center">{t}Section{/t}</th>{/if}
                <th align="center" style="width:90px;">{t}Date{/t}</th>
                <th align="center" style="width:10px;">{t}Publisher{/t}</th>
                <th align="center" style="width:90px;">{t}Last editor{/t}</th>
                <th align="center" style="width:10px;">{t}Published{/t}</th>
                {if $category!='widget' && $category!='all'} <th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}
                <th align="center" style="width:10px;">{t}Home{/t}</th>
                <th align="center" style="width:50px;">{t}Actions{/t}</th>
            </tr>
        </thead>
        {else}
        <thead>
            <tr>
                <th colspan="9">
                    &nbsp;
                </th>

            </tr>
        </thead>
        {/if}
        <tbody class="sortable">
        {section name=as loop=$portadas}
        <tr data-id="{$portadas[as]->pk_kiosko}">
            <td class="center">
                    <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$portadas[as]->id}"  style="cursor:pointer;" >
            </td>
            <td >
                <img src="{$KIOSKO_IMG_URL}{$portadas[as]->path}{$portadas[as]->name|regex_replace:"/.pdf$/":".jpg"}"
                     title="{$portadas[as]->title|clearslash}" alt="{$portadas[as]->title|clearslash}" height="80"
                     onmouseover="Tip('<img src={$KIOSKO_IMG_URL}{$portadas[as]->path}650-{$portadas[as]->name|regex_replace:"/.pdf$/":".jpg"} >', SHADOW, true, ABOVE, true, WIDTH, 640)" onmouseout="UnTip()" />
            </td>
            <td >
                {$portadas[as]->title|clearslash}
            </td>
            {if $category=='widget' || $category=='all'}
                <td class="center">
                     {$albums[as]->category_title}
                </td>
            {/if}
            <td align="center">
                {$portadas[as]->date}
            </td>
            <td  align="center">
                {$portadas[as]->publisher}
            </td>
            <td  align="center">
                {$portadas[as]->editor}
            </td>
             <td align="center">
                {acl isAllowed="KIOSKO_AVAILABLE"}
                    {if $portadas[as]->available == 1}
                        <a href="?id={$portadas[as]->pk_kiosko}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}&amp;category={$category}" title="Publicado">
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                        </a>
                    {else}
                        <a href="?id={$portadas[as]->pk_kiosko}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}&amp;category={$category}" title="Pendiente">
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                        </a>
                    {/if}
                {/acl}
            </td>
            {if $category!='widget' && $category!='all'}
            <td align="center">
            {acl isAllowed="KIOSKO_FAVORITE"}
                {if $portadas[as]->favorite == 1}
                    <a href="?id={$portadas[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Quitar de favorito"></a>
                {else}
                    <a href="?id={$portadas[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Poner de favorito"></a>
                {/if}
            {/acl}
            </td>
            {/if}
            <td class="center">
                {acl isAllowed="KIOSKO_HOME"}
                    {if $portadas[as]->in_home == 1}
                       <a href="?id={$portadas[as]->id}&amp;action=change_inHome&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="no_home" title="{t}Take out from home{/t}"></a>
                    {else}
                        <a href="?id={$portadas[as]->id}&amp;action=change_inHome&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="go_home" title="{t}Put in home{/t}"></a>
                    {/if}
                {/acl}
            </td>
            <td align="center">
                <ul class="action-buttons">
                    <li>
                        {acl isAllowed="KIOSKO_UPDATE"}
                        <a href="{$smarty.server.PHP_SELF}?action=read&id={$portadas[as]->pk_kiosko}" title="Modificar">
                            <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                        </a>
                        {/acl}
                    </li>
                    <li>
                        {acl isAllowed="KIOSKO_DELETE"}
                        <a class="del" data-controls-modal="modal-from-dom"
                               data-id="{$portadas[as]->pk_kiosko}"
                               data-title="{$portadas[as]->title|capitalize}"  href="#" >
                            <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                        </a>
                        {/acl}
                    </li>
                </ul>
            </td>

        </tr>
        {sectionelse}
        <tr>
            <td class="empty" colspan="10">{t}There is no stands{/t}</td>
        </tr>
        {/section}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" style="padding:10px;font-size: 12px;" align="center">
                    {if count($portadas) gt 0}
                        {$paginacion->links}
                    {/if}
                    &nbsp;
                </td>
            </tr>
        </tfoot>
    </table>
    <input type="hidden" name="category" id="category" value="{$category}" />
    <input type="hidden" id="status" name="status" value="" />
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />

     <script>
        jQuery('#buton-batchnoFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "0");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
        jQuery('#buton-batchFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "1");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
    </script>
</div>
</form>
{include file="newsstand/modals/_modalDelete.tpl"}
{include file="newsstand/modals/_modalBatchDelete.tpl"}
{include file="newsstand/modals/_modalAccept.tpl"}

{if $category eq 'favorite'}
    <script type="text/javascript">

        // <![CDATA[
            jQuery(document).ready(function() {
                makeSortable();
            });
        // ]]>
    </script>
{/if}
{/block}
