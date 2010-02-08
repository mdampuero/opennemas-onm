{include file="pc_header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
    <ul class="tabs2">
        {section name=as loop=$allcategorys}
            <li>
                 {assign var=ca value=`$allcategorys[as]->pk_content_category`}
                <a href="pc_opinion.php?action=list&category={$ca}" {if $category==$ca } style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} {/if} >{$allcategorys[as]->title}</a>
            </li>
        {/section}
    </ul>
    <br />
    {include file="pc_botonera_up.tpl"}
	
    <div id="{$category}">
    <table class="adminheading">
        <tr>
            <th nowrap>Plan conecta: Opini&oacute;n</th>
        </tr>
    </table>

    <table class="adminlist">
	<tr>  
            <th class="title"></th>
            <th class="title" align="left">Ver</th>
            <th class="title" align="left">Título</th>
            <th class="title" align="left">Autor</th>
            <th class="title" align="left">IP</th>
            <th align="center">Fecha</th>
            <th align="center">Visto</th>
            <th align="center">Favorito</th>
            <th align="center">Publicado</th>
            <th align="center">Archivar</th>
            <th align="center">Modificar</th>
            <th align="center">Eliminar</th>
        </tr>

        {section name=c loop=$opinions}
            <tr {cycle values="class=row0,class=row1"} >
                <td style="padding:10px;font-size: 11px;width:20px;">
                        <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$opinions[c]->id}"  style="cursor:pointer;">
                </td>
                <td style="padding:1px;font-size: 11px;width:50px;" >
                   <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;" onmouseout="UnTip()" onmouseover="Tip('{$opinions[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)" />
                </td>
                <td style="padding:1px;font-size: 11px;"  onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();" >
                        {$opinions[c]->title|clearslash}
                </td>
                <td style="padding:1px;font-size: 11px;width:120px;" align="left">
                        {assign var='id_author' value=$opinions[c]->fk_user}
                        {$conecta_users[$id_author]->nick}
                </td>
                <td style="padding:10px;font-size: 11px;width:120px;" align="center">
                        {$opinions[c]->ip}
                </td>
                <td style="padding:1px;width:120px;font-size: 11px;" align="center">
                                {$opinions[c]->created}
                </td>
                <td style="padding:1px;font-size: 11px;width:100px;" align="center">
                        {$opinions[c]->views}
                </td>
                <td style="padding:1px;font-size: 11px;width:60px;" align="center">
                        {if $opinions[c]->favorite == 1}
                                <a href="?id={$opinions[c]->id}&amp;action=change_favorite&amp;status=0&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Publicado"></a>
                        {else}
                                <a href="?id={$opinions[c]->id}&amp;action=change_favorite&amp;status=1&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Pendiente"></a>
                        {/if}
                </td>
                <td style="padding:1px;font-size: 11px;width:60px;" align="center">
                        {if $opinions[c]->available == 1}
                                <a href="?id={$opinions[c]->id}&amp;action=change_available&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
                                        <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                        {else}
                                <a href="?id={$opinions[c]->id}&amp;action=change_available&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
                                        <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                        {/if}
                </td>
                <td style="padding:1px;width:60px;font-size: 11px;" align="center">
                                        <a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Publicado">
                                        <img src="{php}echo($this->image_dir);{/php}save_hemeroteca_icon.png" border="0" alt="Archivar" /></a>
                                </td>
                <td style="padding:1px;font-size: 11px;width:60px;" align="center">
                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$opinions[c]->id}');" title="Modificar">
                                <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
                </td>
                <td style="padding:1px;font-size: 11px;width:60px;" align="center">
                        <a href="#" onClick="javascript:confirmar(this, '{$opinions[c]->id}');" title="Eliminar">
                                <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                </td>
            </tr>
        {sectionelse}
            <tr>
                <td align="center" colspan=5><br><br><p><h2><b>Ninguna opini&oacute;n guardada</b></h2></p><br><br></td>
            </tr>
        {/section}
        {if count($opinions) gt 0}
            <tr>
                <td colspan="10" align="center">{$paginacion->links}</td>
            </tr>
        {/if}
    </table>
</div>
{/if}

 {dialogo script="print"}
{* FORMULARIO PARA ENGADIR E ACTUALIZAR ************************************** *}

{if isset($smarty.request.action) && $smarty.request.action eq "new" || (isset($smarty.request.action) && $smarty.request.action eq "read")}
    {include file="pc_botonera_up.tpl"}
	
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="99%">
    <tbody>
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="30%">
                    <label for="title">T&iacute;tulo:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" width="70%">
                    <input type="text" id="title" name="title" title="Título de la noticia" onBlur="javascript:get_tags(this.value);"
                            value="{$opinion->title|clearslash|escape:"html"}" class="required" size="100" />
            </td>
             <td style="padding:4px;" nowrap="nowrap" rowspan='5'>
                    <table style='background-color:#F5F5F5; padding:8px;' border='0' cellpadding="8">
                         <tr>
                            <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                <label for="title"> Secci&oacute;n: </label>
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                    <select name="fk_pc_content_category" id="fk_content_type" class="required">
                                        {section name=as loop=$categorys}
                                             <option value="{$categorys[as]->pk_content_category}" {if ($category eq $categorys[as]->pk_content_category || $opinion->fk_pc_content_category eq $categorys[as]->pk_content_category)} selected{/if}>{$categorys[as]->title}</option>
                                        {/section}
                                    </select>
                            </td>

                        </tr>
                       <tr>
                            <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                <label for="title"> Disponible: </label>
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                    <select name="available" id="avaliable" class="required">
                                        <option value="0" {if $opinion->available eq 0} selected {/if}>No</option>
                                        <option value="1"  {if $opinion->available eq 1} selected {/if}>Si</option>
                                    </select>
                            </td>

                        </tr> <tr>
                            <td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">
                                <label for="title"> Archivar: </label>
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                <select name="content_status" id="content_status" class="required">
                                    <option value="0"  {if $opinion->content_status eq 0} selected {/if}>No</option>
                                    <option value="1" {if $opinion->content_status eq 1} selected {/if}>Si</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                    <label for="title">Autor:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap">
                    <select name="fk_user" class="validate-selection">
                            <option>-- Seleccione autor --</option>
                            {foreach  key=as from=$conecta_users item=author}
                                {if $author->status neq 0}
                                  <option value="{$as}" {if $opinion->fk_user eq $as}selected{/if}>{$author->nick}</option>
                                {/if}
                            {/foreach}
                        </select>
                   <label for="title"> IP: </label>
                   <input type="text" readonly id="ip" name="ip" title="ip" value="{$opinion->ip}" />
                </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                    <label for="title">Pais:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap">
                <input type="text" id="country" name="country" title="country"
                        value="{$opinion->country}" class="required" size="40" />
                        <label for="title">Localidad:</label><input type="text" id="locality" name="locality" title="locality"
                        value="{$opinion->locality}" class="required" size="40" />

            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                    <label for="title">Palabras Clave:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap">
                    <input type="text" id="metadata" name="metadata" title="metadata"
                            value="{$opinion->metadata}" class="required" size="90" />

            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="30%">
                    <label for="body">Cuerpo:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" width="70%">
                    <textarea name="body" id="body"
                            title="Opinion" style="width:100%; height:20em;">{$opinion->body|clearslash}</textarea>
            </td>
        </tr>
    </tbody>
    </table>


    <script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
    {literal}
    <script type="text/javascript" language="javascript">
        tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
    </script>

    <script type="text/javascript" language="javascript">
        OpenNeMas.tinyMceConfig.advanced.elements = "body";
        tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
    </script>
    {/literal}
{/if}

{include file="footer.tpl"}