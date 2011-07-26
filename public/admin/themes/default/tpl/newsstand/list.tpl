{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}ePaper Manager{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" title="{t}New cover{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}article_add.png" title="Nueva" alt="Nueva"><br />{t}New ePaper{/t}
                </a>
            </li>
        </ul>
    </div>
</div>


<div class="wrapper-content">

<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">

    <div id="content-wrapper">
        {* ZONA MENU CATEGORIAS ******* *}
    <ul id="tabs" style="margin-left:10px;">
        {section name=as loop=$allcategorys}
        <li>
            {assign var=ca value=$allcategorys[as]->pk_content_category}
            <a href="kiosko.php?action=list&category={$ca}#" {if $category==$ca} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}{/if} >{$allcategorys[as]->title}</a>
        </li>
        {/section}
    </ul>

    <br />

    <div id="{$category}">
        <table class="adminheading">
            <tr>
                <th>Portadas</th>
            </tr>
        </table>
        <div id="pagina">
            {* NOTICIA DESTACADA ******* *}
            <table class="adminlist" border=0>

                {if count($portadas) > 0}
                <thead>
                    <th align="center" style="width:100px;">Portada</th>
                    <th align="center">T&iacute;tulo</th>
                    <th align="center" style="width:100px;">Fecha</th>
                    <th align="center">Publisher</th>
                    <th align="center">Última edición</th>
                    <th align="center" style="width:10px;">Favorito</th>
                    <th align="center" style="width:110px;">Publicado</th>
                    <th align="center" style="width:50px;">Editar</th>
                    <th align="center" style="width:50px;">Elim</th>
                </thead>
                {/if}

                {section name=as loop=$portadas}
                <tr {cycle values="class=row0,class=row1"}>
                    <td style="padding:10px;font-size: 11px;">
                        <img src="{$MEDIA_IMG_PATH_WEB}kiosko{$portadas[as]->path}{$portadas[as]->name|regex_replace:"/.pdf$/":".jpg"}" title="{$portadas[as]->title|clearslash}" alt="{$portadas[as]->title|clearslash}" width="80" onmouseover="Tip('<img src={$MEDIA_IMG_PATH_WEB}kiosko{$portadas[as]->path}{$portadas[as]->name|regex_replace:"/.pdf$/":".jpg"} >', SHADOW, true, ABOVE, true, WIDTH, 200)" onmouseout="UnTip()" />
                    </td>
                    <td style="padding:10px;font-size: 11px;">
                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$portadas[as]->pk_kiosko}');" title="{$portadas[as]->title|clearslash}">
                        {$portadas[as]->title|clearslash}</a>
                    </td>
                    <td style="padding:10px;font-size: 11px;">
                        {$portadas[as]->date}
                    </td>
                    <td  class='no_view' style="width:110px;" align="center">
                        {$portadas[as]->publisher}
                    </td>
                    <td  class='no_view' style="width:110px;" align="center">
                        {$portadas[as]->editor}
                    </td>
                    <td style="padding:10px;font-size: 11px;">
                        {if $portadas[as]->favorite == 1}
                            <a href="?id={$portadas[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Quitar de favorito"></a>
                        {else}
                            <a href="?id={$portadas[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Poner de favorito"></a>
                        {/if}
                    </td>
                    <td style="padding:10px;width:10%;" align="center">
                        {if $portadas[as]->available == 1}
                            <a href="?id={$portadas[as]->pk_kiosko}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}&amp;category={$category}" title="Publicado">
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                            </a>
                        {else}
                            <a href="?id={$portadas[as]->pk_kiosko}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}&amp;category={$category}" title="Pendiente">
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                            </a>
                        {/if}
                    </td>
                    <td style="padding:10px;width:10%;" align="center">
                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$portadas[as]->pk_kiosko}');" title="Modificar">
                            <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                        </a>
                    </td>
                    <td style="padding:10px;width:10%;" align="center">
                        <a href="#" onClick="confirm('¿Seguro que desea eliminar la portada?');enviar(this, '_self', 'delete', '{$portadas[as]->pk_kiosko}');" title="Eliminar">
                            <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                        </a>
                    </td>

                </tr>
                {sectionelse}
                <tr>
                    <td align="center" colspan=9 atyle="padding:20px;">{t}There is no stands{/t}</td>
                </tr>
                {/section}


                <tfoot>
                    <tr>
                        <td colspan="9" style="padding:10px;font-size: 12px;" align="center">
                            {if count($portadas) gt 0}
                                {$paginacion->links}
                            {/if}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <input type="hidden" id="action" name="action" value="" />
    </div><!--fin content-wrapper-->

</form>

{/block}
