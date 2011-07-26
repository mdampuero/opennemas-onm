{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsalbum.js"></script>

{/block}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}cropper.js"></script>
     <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsGallery.js"></script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Album manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating Album{/t}{else}{t}Editing Album{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    {acl isAllowed="ALBUM_CREATE"}
                    <a class="admin_add" onClick="album_get_order(); if(check_crop()) enviar(this, '_self', 'validate', '{$album->id}');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />{t}Save and continue{/t}
                    </a>
                    {/acl}
                </li>
                <li>
                    {if isset($album->id)}
                        {acl isAllowed="ALBUM_UPDATE"}
                            <a onClick="javascript:album_get_order(); if(check_crop()) enviar(this, '_self', 'update', '{$album->id}');">
                        {/acl}
                    {else}
                        {acl isAllowed="ALBUM_CREATE"}
                            <a onClick="javascript: album_get_order(); if(check_crop()) enviar(this, '_self', 'create', '0');">
                        {/acl}
                    {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />{t}Save{/t}
                    </a>
                </li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=list">
                        <img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        <ul id="tabs">
            <li>
                <a href="#edicion-contenido">{t}Enter album information{/t}</a>
            </li>
        </ul>

        <div class="panel" id="edicion-contenido" style="width:100%">
            <table border="0" cellpadding="2" cellspacing="2" class="fuente_cuerpo" >
                <tbody>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">{t}Title:{/t}</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="title" name="title" title={t}"Album"{/t}
                                size="80" value="{$album->title|clearslash|escape:"html"}"
                                class="required" onBlur="javascript:get_metadata(this.value);" />
                        </td>
                        <td rowspan="4">
                            <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title">Secci&oacute;n:</label>
                                    </td>
                                    <td nowrap="nowrap">
                                        <select name="category" id="category"  >
                                            {section name=as loop=$allcategorys}
                                                <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                                                {section name=su loop=$subcat[as]}
                                                    <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                                                {/section}
                                            {/section}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> {t}Available:{/t} </label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                            <select name="available" id="available"
                                                class="required" {acl isNotAllowed="ALBUM_AVAILABLE"} disabled="disabled" {/acl}>
                                                <option value="0">{t}No{/t}</option>
                                                <option value="1" selected>{t}Yes{/t}</option>
                                            </select>
                                    </td>
                                </tr>
                                </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;" >
                            <label for="title">Agencia:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="agency" name="agency" title={t}"Album"{/t}
                                size="80" value="{$album->agency|clearslash|escape:"html"}" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">Descripci&oacute;n:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <textarea name="description" id="description"  title={t}"description"{/t} style="width:100%; height:8em;">{t 1=$album->description|clearslash|escape:"html"}%1{/t}</textarea>
                        </td>
                    </tr>

                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="metadata">{t}Keywords:{/t}</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="metadata" name="metadata" size="80"
                               class="required" title={t}"Metadata"{/t} value="{$album->metadata}" />
                            <br><label align='right'><sub>{t}Separated by coma{/t}</sub></label>
                        </td>
                    </tr>
                    {include file="album/album_images.tpl"}
                </tbody>
            </table>
        </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />
    </div>
</form>

{/block}
