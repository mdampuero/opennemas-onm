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
                    <th align="center" style="width:100px;">{t}Cover{/t}</th>
                    <th align="center">{t}Title{/t}</th>
                    <th align="center" style="width:90px;">{t}Date{/t}</th>
                    <th align="center" style="width:10px;">{t}Publisher{/t}</th>
                    <th align="center" style="width:90px;">{t}Last editor{/t}</th>
                    <th align="center" style="width:10px;">{t}Favorte{/t}</th>
                    <th align="center" style="width:10px;">{t}Published{/t}</th>
                    <th align="center" style="width:50px;">{t}Actions{/t}</th>
                </thead>
                {/if}

                {section name=as loop=$portadas}
                <tr {cycle values="class=row0,class=row1"}>
                    <td >
                        <img src="{$MEDIA_IMG_PATH_WEB}kiosko{$portadas[as]->path}{$portadas[as]->name|regex_replace:"/.pdf$/":".jpg"}" title="{$portadas[as]->title|clearslash}" alt="{$portadas[as]->title|clearslash}" width="80" onmouseover="Tip('<img src={$MEDIA_IMG_PATH_WEB}kiosko{$portadas[as]->path}{$portadas[as]->name|regex_replace:"/.pdf$/":".jpg"} >', SHADOW, true, ABOVE, true, WIDTH, 200)" onmouseout="UnTip()" />
                    </td>
                    <td >
                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$portadas[as]->pk_kiosko}');" title="{$portadas[as]->title|clearslash}">
                        {$portadas[as]->title|clearslash}</a>
                    </td>
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
                        {if $portadas[as]->favorite == 1}
                            <a href="?id={$portadas[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Quitar de favorito"></a>
                        {else}
                            <a href="?id={$portadas[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Poner de favorito"></a>
                        {/if}
                    </td>
                    <td align="center">
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
                    <td align="center">
                        <ul class="action-buttons">
                            <li>
                                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$portadas[as]->pk_kiosko}');" title="Modificar">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                </a>
                            </li>
                            <li>
                                <a href="#" onClick="confirm('¿Seguro que desea eliminar la portada?');enviar(this, '_self', 'delete', '{$portadas[as]->pk_kiosko}');" title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                </a>
                            </li>
                        </ul>


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
