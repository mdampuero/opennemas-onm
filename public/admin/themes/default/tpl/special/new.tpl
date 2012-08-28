{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript">
        jQuery.noConflict();
    </script>
    {script_tag src="/utilsGallery.js"}
{/block}

{block name="header-css" append}
 {css_tag href="/parts/specials.css"}
{/block}

{block name="footer-js" append}
    {script_tag src="/onm/content-provider.js"}
    {script_tag src="/tiny_mce/opennemas-config.js"}

    <script>
        tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
        OpenNeMas.tinyMceConfig.simple.elements = "description";
        tinyMCE.init( OpenNeMas.tinyMceConfig.simple );
    </script>

      <script>
    try {
        new Validation('formulario', { immediate : true });
    } catch(e) { }

    jQuery(document).ready(function($){
        $("#formulario").on("submit", function(e, ui) {
            var els = [];
            jQuery('#column_right').find('ul.content-receiver li').each(function (index, item) {

                els.push({
                    'id' : jQuery(item).data('id'),
                    'content_type': jQuery(item).data('type'),
                    'position': index
                });
            });

            jQuery('input#noticias_right').val(JSON.stringify(els));

            els = [];

            jQuery('#column_left').find('ul.content-receiver li').each(function (index, item) {

                els.push({
                    'id' : jQuery(item).data('id'),
                    'content_type': jQuery(item).data('type'),
                    'position': index
                });
            });

            jQuery('input#noticias_left').val(JSON.stringify(els));
        });
        $('#title').on('change', function(e, ui) {
            fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
        });
    });
    </script>
{/block}

{block name="content"}
<form action="{if $special->id}{url name=admin_special_update id=$special->id}{else}{url name=admin_special_create}{/if}" method="post" name="formulario" id="formulario">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Special manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating Special{/t}{else}{t}Editing Special{/t}{/if}</h2></div>
            <ul class="old-button">

                 <li>
                    {acl isAllowed="SPECIAL_CREATE"}
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </button>
                    {/acl}
                </li>
                <li>
                {if isset($special->id)}
                    {acl isAllowed="SPECIAL_UPDATE"}
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                    </button>
                    {/acl}
                {else}
                    {acl isAllowed="SPECIAL_CREATE"}
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                    </button>
                    {/acl}
                {/if}
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_specials category=$category}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <table class="adminform">
            <tbody>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">{t}Title:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="title" name="title" title={t}"Special"{/t}
                            size="80" value="{$special->title|clearslash|escape:"html"}"/>
                    </td>
                    <td rowspan="2"  style="padding: 4px;">
                        <table  style='background-color:#F5F5F5; padding:8px;'>
                            <tr>
                                <td valign="top"  style="text-align:right;padding: 4px;" nowrap="nowrap">
                                <label for="title">Secci&oacute;n:</label>
                                </td>
                                <td nowrap="nowrap">
                                    <select name="category" id="category" style="width:98%">
                                        {section name=as loop=$allcategorys}
                                            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                            <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                                            {/acl}
                                            {section name=su loop=$subcat[as]}
                                                {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                                                <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                                                {/acl}
                                            {/section}
                                        {/section}
                                    </select>
                            </tr>
                            <tr>
                                <td valign="top" style="text-align:right;padding: 4px;" nowrap="nowrap">
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
                        <label for="title">Subtitle:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="subtitle" name="subtitle" title={t}"Special"{/t}
                            size="80" value="{$special->subtitle|clearslash|escape:"html"}" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="metadata">{t}Keywords:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="metadata" name="metadata" size="80"
                           class="required" title={t}"Metadata"{/t} value="{$special->metadata}" />
                        <br><label align='right'><sub>{t}Separated by coma{/t}</sub></label>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="slug">{t}Slug{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input 	type="text" id="slug" name="slug" title="{t}slug{/t}"
                            size="80" maxlength="256" tabindex="5"
                            {if is_object($special)}
                                    value="{$special->slug|clearslash|escape:"html"}"
                            {else}
                                    value=""
                            {/if}/>
                     </td>
                </tr>
                 <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">Descripci&oacute;n:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" colspan="2">
                        <textarea name="description" id="description"  title="description" style="width:90%; height:10em;">
                            {t 1=$special->description|clearslash}%1{/t}
                        </textarea>
                    </td>
                </tr>

                <tr>
                    <td valign="top" align="right" colspan="3">
                        {include file="special/partials/_load_images.tpl"}
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" colspan="3">
                        {include file="special/partials/_contents_containers.tpl"}
                    </td>
                </tr>
          </tbody>
        </table>
    </div>
    <input type="hidden" id="noticias_right" name="noticias_right" value="">
    <input type="hidden" id="noticias_left" name="noticias_left" value="">

    <input type="hidden" name="id" id="id" value="{$special->id|default:""}" />

</form>

{/block}
