{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery-ui-sliderAccess.js"}
    {script_tag language="javascript" src="/onm/jquery.datepicker.js"}
{/block}

{block name="footer-js" append}
    <script type="text/javascript">
    jQuery('#title').on('change', function(e, ui) {
        fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
    });
    </script>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}ePaper Manager{/t} :: {t}New ePaper{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_covers}" title="Cancelar">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Cancel{/t}
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="wrapper-content">
    <form action="{if is_null($cover-id)}{url name=admin_covers_create}{else}{url name=admin_covers_update}{/if}"  id="formulario" enctype="multipart/form-data"  name="formulario" method="POST">
        <table class="adminform">
            <tbody>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">Titulo:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="title" name="title" title="Portada" size="80" value="{$kiosko->title|clearslash|default:""}" class="required" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="metadata">Palabras clave: </label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="{$kiosko->metadata|clearslash|default:""}" /><br>
                        <label align='right'><sub>Separadas por comas</sub></label><br>
                    </td>
                    <td rowspan=3>
                        <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                            <tr>
                                <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title">Portada de:</label>
                                </td>
                                <td nowrap="nowrap">
                                    <select name="category" id="category" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                        {section name=as loop=$allcategorys}
                                            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                            <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if}>{t 1=$allcategorys[as]->title}%1{/t}</option>
                                            {/acl}
                                            {section name=su loop=$subcat[as]}
                                                {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                                                <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
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
                                            <option value="1" {if $kiosko->available==1}selected{/if}>Si</option>
                                        </select>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title"> Favorito:</label>
                                </td>
                                <td valign="top" nowrap="nowrap">
                                        <select name="favorite" id="favorite" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                            <option value="0">No</option>
                                            <option value="1" selected>Si</option>

                                    <img class="favorite" src="{$params.IMAGE_DIR}selected.png" border="0" alt="En home" align="top" />
                                </td>
                            </tr>
                        </table>
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
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">PDF de portada:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                         <input type="file" id="file" name="file" title="PDF de portada" class="required" /></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button red">{t}Save{/t}</button>
            </div>
        </div>
    </div><!--fin content-wrapper-->

</form>
{capture assign="language"}{setting name=site_language}{/capture}
{assign var="lang" value=$language|truncate:2:""}
{if !empty($lang)}
    {assign var="js" value="/jquery/jquery_i18n/jquery.ui.datepicker-"|cat:$lang|cat:".js"}
    {script_tag language="javascript" src=$js}
    <script>
    jQuery(document).ready(function() {
        jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "{$lang}" ] );
    });
    </script>
{/if}
{/block}
