{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery-ui-sliderAccess.js"}
    {script_tag language="javascript" src="/onm/jquery.datepicker.js"}
{/block}

{block name="content"}
<form id="formulario" enctype="multipart/form-data"  name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2>{if !isset($kiosko->id)}{t}New ePaper{/t}{else}{t}Editing ePaper{/t}{/if}</h2>
        </div>
        <ul class="old-button">
            <li>
                <button action="submit">
                    <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save and exit{/t}"><br />{t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
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
                        <label for="title">{t}Title{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="title" name="title" title="Portada" size="80" value="{$kiosko->title|clearslash|default:""}" class="required" onBlur="javascript:get_metadata(this.value);" />
                    </td>
                    <td rowspan="3">
                        <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                            <tr>
                                <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title">{t}Category{/t}</label>
                                </td>
                                <td nowrap="nowrap">
                                    {include file="common/selector_categories.tpl" name="category" item=$kiosko}
                                </td>
                            </tr>
                            <tr>
                                <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title">{t}Available{/t}:</label>
                                </td>
                                <td valign="top" nowrap="nowrap">
                                    <select name="available" id="available" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                        <option value="0" {if $kiosko->available==0}selected{/if}>{t}No{/t}</option>
                                        <option value="1" {if empty($kiosko) || $kiosko->available==1}selected{/if}>{t}Yes{/t}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title">{t}Favorite{/t}:</label>
                                </td>
                                <td valign="top" nowrap="nowrap">
                                    <select name="favorite" id="favorite" class="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                                        <option value="0" {if $kiosko->favorite==0}selected{/if}>{t}No{/t}</option>
                                        <option value="1" {if empty($kiosko) || $kiosko->favorite==1}selected{/if}>{t}Yes{/t}</option>
                                    </select>
                                    <img class="favorite" src="{$params.IMAGE_DIR}selected.png" border="0" alt="En home" align="top" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="metadata">{t}Keywords{/t}: </label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="{$kiosko->metadata|clearslash|default:""}" /><br>
                        <label align='right'><sub>{t}Separated by commas{/t}</sub></label><br>
                    </td>
                </tr>
                <tr>
                    <td valign="top"  align="right" style="padding:4px;">
                        <label for="title">{t}Price{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="number" step="any" id="price" name="price" size="80" value="{$kiosko->price|string_format:"%.2f"|default:""}"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">{t}Date{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                         <input type="text" id="date" name="date" size="18" title="Fecha de portada" value="{$kiosko->date|default:""}" tabindex="-1" class="required" />
                    </td>
                </tr>
                {if $smarty.request.action eq 'new'}
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">{t}Upload PDF{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                         <input type="file" id="file" name="file" title="PDF de portada" class="required" /></div>
                    </td>
                </tr>
                <!-- Hidden form vars -->
                <input type="hidden" id="action" name="action" value="create" />
                {else}
                <tr>
                    <td valign="top" colspan="3">
                        <p style="text-align: center;">
                            <img src="{$KIOSKO_IMG_URL}{$kiosko->path}{$kiosko->name|regex_replace:"/.pdf$/":".jpg"}" title="{$kiosko->title|clearslash}" alt="{$kiosko->title|clearslash}" />
                        </p>
                    </td>
                </tr>
                <!-- Hidden form vars -->
                <input type="hidden" id="action" name="action" value="update" />
                <input type="hidden" id="id" name="id" value="{$kiosko->id}" />
                {/if}
            </tbody>
        </table>

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
