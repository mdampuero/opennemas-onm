{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsspecial.js" language="javascript"}

{/block}

{block name="footer-js" append}
    {script_tag src="/cropper.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Special manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating Special{/t}{else}{t}Editing Special{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    {acl isAllowed="SPECIAL_CREATE"}
                    <a class="admin_add" onClick="enviar(this, '_self', 'validate', '{$special->id}');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />{t}Save and continue{/t}
                    </a>
                    {/acl}
                </li>
                <li>
                    {if isset($special->id)}
                        {acl isAllowed="SPECIAL_UPDATE"}
                            <a onClick="javascript: enviar(this, '_self', 'update', '{$special->id}');">
                        {/acl}
                    {else}
                        {acl isAllowed="SPECIAL_CREATE"}
                            <a onClick="javascript: enviar(this, '_self', 'create', '0');">
                        {/acl}
                    {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />{t}Save{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=list&category={$smarty.request.category}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        <table class="adminheading">
            <tr>
                <td>
                    {t}Enter special information{/t}
                </td>
            </tr>
        </table>

        <table class="adminform" >
            <tbody>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">{t}Title:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="title" name="title" title={t}"Special"{/t}
                            size="60" value="{$special->title|clearslash|escape:"html"}"
                            class="required" onBlur="javascript:get_metadata(this.value);" />
                    </td>
                    <td rowspan="4">
                        <table style='background-color:#F5F5F5; padding:18px;'>
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
                                            class="required" {acl isNotAllowed="SPECIAL_AVAILABLE"} disabled="disabled" {/acl}>
                                            <option value="0" {if $special->available eq 0} selected {/if}>{t}No{/t}</option>
                                            <option value="1" {if $special->available eq 1} selected {/if}>{t}Yes{/t}</option>

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
                        <input type="text" id="agency" name="agency" title={t}"Special"{/t}
                            size="60" value="{$special->agency|clearslash|escape:"html"}" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">Descripci&oacute;n:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <textarea name="description" id="description"  title={t}"description"{/t} style="width:90%; height:10em;">{t 1=$special->description|clearslash|escape:"html"}%1{/t}</textarea>
                    </td>
                </tr>

                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="metadata">{t}Keywords:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="metadata" name="metadata" size="60"
                           class="required" title={t}"Metadata"{/t} value="{$special->metadata}" />
                        <br><label align='right'><sub>{t}Separated by coma{/t}</sub></label>
                    </td>
                </tr>
                {include file="special/special_image.tpl"}


                {include file="special/special_content.tpl"}


<script type="text/javascript" language="javascript">
tinyMCE.init({
	mode : "exact",
	elements : "description"
});
</script>
 
    <!-- </div> -->

<input type="hidden" id="noticias_right" name="noticias_right" value="">

<input type="hidden" id="noticias_left" name="noticias_left" value=""> 




            </tbody>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>

{/block}
