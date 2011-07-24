{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}ePaper Manager{/t} :: {t}Editing ePaper{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
                    <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                </a>
            </li>
            <li>
            {if isset($kiosko->id)}
                <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$kiosko->id}', 'formulario');">
            {else}
                <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
            {/if}
                    <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
                </a>
            </li>
        </ul>

        </ul>
    </div>
</div>


<div class="wrapper-content">

<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">

    <div id="content-wrapper">
        <ul id="tabs">
            <li>
                <a href="#edicion-contenido">Editar Portada</a>
            </li>
        </ul>

        <div class="panel" id="edicion-contenido" style="width:95%">
            <table border="0" cellpadding="2" cellspacing="2" class="fuente_cuerpo" width="96%">
                <tbody>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">Titulo:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="title" name="title" title="Portada" size="80" value="{$kiosko->title|clearslash}" onBlur="javascript:get_metadata(this.value);" />
                        </td>
                        <td rowspan=3>
                            <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title">Portada de:</label>
                                    </td>
                                    <td nowrap="nowrap">
                                        <select name="category" id="category" class="required">
                                            {section name=as loop=$allcategorys}
                                                <option value="{$allcategorys[as]->pk_content_category}" {if $kiosko->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                            {/section}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> Disponible:</label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                        <select name="available" id="available" class="required">
                                            <option value="0" {if $kiosko->available==0}selected{/if}>No</option>
                                            <option value="1" {if $kiosko->available==1}selected{/if}>Si</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> Favorito:</label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                        <select name="favorite" id="favorite" class="required">
                                            <option value="0" {if $kiosko->favorite==0}selected{/if}>No</option>
                                            <option value="1" {if $kiosko->favorite==1}selected{/if}>Si</option>
                                        </select>
                                        <img class="favorite" src="{$params.IMAGE_DIR}selected.png" border="0" alt="En home" align="top" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="metadata">Palabras clave: </label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap"">
                            <input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="{$kiosko->metadata}" /><br>
                            <label align='right'><sub>Separadas por comas</sub></label><br>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">Fecha:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                             {$kiosko->date}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="3">
                            <p style="text-align: center;">
                                <img src="{$MEDIA_IMG_PATH_WEB}kiosko{$kiosko->path}{$kiosko->name|regex_replace:"/.pdf$/":".jpg"}" title="{$kiosko->title|clearslash}" alt="{$kiosko->title|clearslash}" />
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

            <input type="hidden" id="action" name="action" value="" />
    </div><!--fin content-wrapper-->

</form>

{/block}

