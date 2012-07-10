{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery-ui-sliderAccess.js"}
    {script_tag src="/onm/jquery.datepicker.js" language="javascript"}
{/block}

{block name="content"}
<form id="formulario" name="formulario" action="{if !empty($cover->id)}{url name=admin_cover_update id=$cover->id}{else}{url name=admin_cover_create}{/if}" method="POST">

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}ePaper Manager{/t} :: {t}New ePaper{/t}</h2></div>
        <ul class="old-button">
            <li>
            {if isset($video->id)}
                <button type="submit">
            {else}
                <button type="submit">
            {/if}
                    <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br />{t}Save{/t}
                </button>
            </li>
            <li>
                <button type="submit" name="continue" value="1">
                    <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />
                    {t}Save and continue{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_covers category=$category|default:""}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go Back{/t}" alt="{t}Go Back{/t}" ><br />{t}Go Back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>



<div class="wrapper-content">

{render_messages}


    <div id="content-wrapper">
        <table class="adminform">
        <tbody>
            <tr>
                <td valign="top" align="right" style="padding:4px;">
                    <label for="title">{t}Title:{/t}</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap">
                    <input type="text" id="title" name="title" size="80" value="{$cover->title|clearslash}" onBlur="javascript:get_metadata(this.value);" />
                </td>
                <td rowspan=3>
                    <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                        <tr>
                            <td valign="top"  align="right" nowrap="nowrap">
                                <label for="title">{t}Category:{/t}</label>
                            </td>
                            <td nowrap="nowrap">
                                <select name="category" id="category" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                     {section name=as loop=$allcategorys}
                                            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                            <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category || $cover->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                                            {/acl}
                                            {section name=su loop=$subcat[as]}
                                                {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                                                <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category || $cover->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                                                {/acl}
                                            {/section}
                                        {/section}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"  align="right" nowrap="nowrap">
                                <label for="title">{t}Available:{/t}</label>
                            </td>
                            <td valign="top" nowrap="nowrap">
                                <select name="available" id="available" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                    <option value="0" {if $cover->available==0}selected{/if}>No</option>
                                    <option value="1" {if empty($cover) || $cover->available==1}selected{/if}>Si</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"  align="right" nowrap="nowrap">
                                <label for="title">{t}Favorite:{/t}</label>
                            </td>
                            <td valign="top" nowrap="nowrap">
                                <select name="favorite" id="favorite" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                    <option value="0" {if $cover->favorite==0}selected{/if}>{t}No{/t}</option>
                                    <option value="1" {if empty($cover) || $cover->favorite==1}selected{/if}>{t}Yes{/t}</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right" style="padding:4px;">
                    <label for="metadata">{t}Keywords:{/t}</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap"">
                    <input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="{$cover->metadata}" /><br>
                    <label align='right'><sub>{t}Comma separated{/t}</sub></label><br>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right" style="padding:4px;">
                    <label for="title">Fecha:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap">
                    <input type="text" id="date" name="date" size="18" title="Fecha de portada" value="{$cover->date|default:""}" tabindex="-1" class="required" />
                </td>
            </tr>
            <tr>
                <td valign="top" colspan="3">
                    <p style="text-align: center;">
                        <img src="{$KIOSKO_IMG_URL}{$cover->path}{$cover->name|regex_replace:"/.pdf$/":".jpg"}" title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" />
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <input type="hidden" id="id" name="id" value="{$cover->id}" />
    </div><!--fin content-wrapper-->

</form>
{/block}
