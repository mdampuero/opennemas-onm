{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}ePaper Manager{/t} :: {t}New ePaper{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=list" title="Cancelar">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Cancel{/t}
                </a>
            </li>
        </ul>
    </div>
</div>


{render_messages}

<div class="wrapper-content">

<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">

    <div id="content-wrapper">

        <table class="adminheading">
            <th>
                <td></td>
            </th>
        </table>
        <table class="adminform">
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
                                <select name="category" id="category" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                     {section name=as loop=$allcategorys}
                                            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                            <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category || $kiosko->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                                            {/acl}
                                            {section name=su loop=$subcat[as]}
                                                {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                                                <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category || $kiosko->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                                                {/acl}
                                            {/section}
                                        {/section}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"  align="right" nowrap="nowrap">
                                <label for="title"> Disponible:</label>
                            </td>
                            <td valign="top" nowrap="nowrap">
                                <select name="available" id="available" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                    <option value="0" {if $kiosko->available==0}selected{/if}>No</option>
                                    <option value="1" {if empty($kiosko) || $kiosko->available==1}selected{/if}>Si</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"  align="right" nowrap="nowrap">
                                <label for="title"> Favorito:</label>
                            </td>
                            <td valign="top" nowrap="nowrap">
                                <select name="favorite" id="favorite" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                    <option value="0" {if $kiosko->favorite==0}selected{/if}>No</option>
                                    <option value="1" {if empty($kiosko) || $kiosko->favorite==1}selected{/if}>Si</option>
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
                    <input type="text" id="date" name="date" size="18" title="Fecha de portada" value="{$kiosko->date|default:""}" tabindex="-1" class="required" />
                </td>
            </tr>
            <tr>
                <td valign="top" colspan="3">
                    <p style="text-align: center;">
                        <img src="{$KIOSKO_IMG_URL}{$kiosko->path}{$kiosko->name|regex_replace:"/.pdf$/":".jpg"}" title="{$kiosko->title|clearslash}" alt="{$kiosko->title|clearslash}" />
                    </p>
                </td>
            </tr>
        </tbody>
        </table>
         <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button red">{t}Save{/t}</button>
            </div>
        </div>

        {* Replaced by the Control.DatePicker prototype widget *}
        {dhtml_calendar inputField="date" button="date" singleClick=true ifFormat="%Y-%m-%d" firstDay=1 align="CR"}
    <input type="hidden" id="action" name="action" value="update" />
    <input type="hidden" id="id" name="id" value="{$kiosko->id}" />
    </div><!--fin content-wrapper-->

</form>

{/block}
