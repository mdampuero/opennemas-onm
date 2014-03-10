{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/onm/jquery.datepicker.js"}
    {script_tag src="/swfobject.js"}
{/block}
{block name="footer-js" append}
    <script type="text/javascript">
    var advertisement_urls = {
        calculate_tags : '{url name=admin_utils_calculate_tags}'
    }
    </script>
    {include file="media_uploader/media_uploader.tpl"}
    <script>
    var mediapicker = $('#media-uploader').mediaPicker({
        upload_url: "{url name=admin_image_create category=0}",
        browser_url : "{url name=admin_media_uploader_browser}",
        months_url : "{url name=admin_media_uploader_months}",
        maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
        filter_by: 'ads',
        handlers: {
            'assign_content' : function( event, params ) {
                var mediapicker = $(this).data('mediapicker');

                var container = $('#related_media').find('.'+params['position']);

                var image_data_el = container.find('.image-data');
                image_data_el.find('.related-element-id').val(params.content.pk_photo);
                container.addClass('assigned');

                if (params.content.type_img == 'swf') {
                    var image_element = mediapicker.getHTMLforSWF(params.content);
                    container.find('.flash-based-warning').show()
                } else {
                    var image_element = mediapicker.buildHTMLElement(params);
                    container.find(".flash-based").hide();
                };

                image_data_el.find('.image').html(image_element);

                // Change the image information to the new one
                container.find(".image_title").html(params.content.filename);
                container.find(".image_size").html(params.content.width + " x "+ params.content.height + " px");
                container.find(".file_size").html(params.content.size + " Kb");
                container.find(".created_time").html(params.content.created);
            }
        }
    });

    jQuery(document).ready(function($) {

        $('#formulario').on('change', "#with_script", function(e, ui) {
            var current_value = $(this).val();

            $('.content_blocks > div').hide();
            $('#content_type_' + current_value).show();

            $('#hide_flash').hide();

            if (current_value == '1') {
                $('#div_url1').hide();
                $('#url, #params_height, #params_width').removeAttr('required');
            } else if (current_value == '2') {
                $('#div_url1').hide();
                $('#url, #params_height, #params_width').removeAttr('required');
            } else {
                $('#hide_flash').show();
                $('#div_url1').show();
                $('#url, #params_height, #params_width').attr('required', 'required');
            }
        }).on('change', '#type_medida', function(e, ui){
            var selected_option = $("#type_medida option:selected").attr('value');
            if (selected_option == 'DATE') {
                $('#porfecha').show();
            } else {
                $('#porfecha').hide();
            }
        }).on('change', '#title', function(e, ui) {
            fill_tags(jQuery('#title').val(),'#metadata', advertisement_urls.calculate_tags);
        }).on('click', '#related_media .unset', function (e, ui) {
            e.preventDefault();

            var parent = jQuery(this).closest('.contentbox');

            parent.find('.related-element-id').val('');
            parent.find('.related-element-footer').val('');
            parent.find('.image').html('');

            parent.removeClass('assigned');
        });

        // $('#formulario').onmValidate({
        //     'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        // });

        var tabs = $('#position-adv').tabs();
        tabs.tabs('select', '{$place}');
    });
    </script>
{/block}

{block name="content" append}
<form action="{if $advertisement->id}{url name=admin_ad_update id=$advertisement->id category=$category page=$page filter=$filter}{else}{url name=admin_ad_create filter=$filter}{/if}" method="post" id="formulario">

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
            <div class="title">
                <h2>{t}Advertisement{/t} :: {if empty($advertisement->id)}{t}Creating banner{/t}{else}{t}Editing banner{/t}{/if}</h2>
            </div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_ads category=$category page=$page filter=$filter}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
    </div>
</div>

<div class="wrapper-content">

    {render_messages}

    <div class="form-horizontal panel">

        <h5>{t}General information{/t}</h5>
        <div class="control-group">
            <label for="title" class="control-label">{t}Title{/t}</label>
            <div class="controls">
                <input  type="text" id="title" name="title" required="required" class="input-xxlarge"
                    value="{$advertisement->title|clearslash|escape:"html"|default:""}" />
            </div>
        </div>
        <div class="control-group" style="display:none">
            <label for="metadata" class="control-label">{t}Keywords{/t}</label>
            <div class="controls">
                <input type="text" id="metadata" name="metadata" class="input-xxlarge" required="required"
                    title="Metadatos" value="{$advertisement->metadata|strip|default:""}">
            </div>
        </div>

        <div class="control-group">
            <label for="available" class="control-label">{t}Activated{/t}</label>
            <div class="controls">
                <input type="checkbox" name="content_status" id="available" value="1"
                    {if isset($advertisement->available) && $advertisement->available == 1}checked="checked"{/if}
                    {acl isNotAllowed="ADVERTISEMENT_AVAILA"}disabled="disabled"{/acl} />
            </div>
        </div>


        <h5>{t}When to show this ad{/t}</h5>
        <div class="control-group">
            <label for="typ_medida" class="control-label">{t}Restrictions{/t}</label>
            <div class="controls">
                <select name="type_medida" id="type_medida">
                    <option value="NULL" {if !isset($advertisement) || is_null($advertisement->type_medida)}selected="selected"{/if}>{t}Without limits{/t}</option>
                    <option value="DATE" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'DATE'}selected="selected"{/if}>{t}Date range{/t}</option>
                </select>
                <div class="help-block">{t}Show this ad if satisfy one condition{/t}.</div>
            </div>
        </div>
        <div class="control-group" id="porfecha" style="{if $advertisement->type_medida neq 'DATE'}display:none{/if};">
            <label for="starttime" class="control-label">{t}Date range{/t}</label>
            <div class="controls">
                <label for="starttime">{t}From{/t}</label>
                <input type="datetime" id="starttime"  name="starttime" title="Fecha inicio publicacion"
                    value="{if isset($advertisement) && $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />
                <label for="endtime">{t}Until{/t}</label>
                <input type="datetime" id="endtime" name="endtime" title="Fecha fin publicacion"
                    value="{if isset($advertisement) && $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />
                <div class="help-block">{t}Show this ad within a range of dates.{/t}.</div>
            </div>
        </div>

        <h5>{t}Contents{/t}</h5>
        <div class="control-group">
            <label class="control-label" for="with_script">{t}Type{/t}</label>
            <div class="controls">
                <select name="with_script" id="with_script">
                    <option value="0" {if !isset($advertisement) || $advertisement->with_script == 0}selected="selected"{/if}>{t}Image or Flash object{/t}</option>
                    <option value="1" {if isset($advertisement) && $advertisement->with_script == 1}selected="selected"{/if}>{t}HTML or Javascript code{/t}</option>
                    {if !empty($server_url)}
                    <option value="2" {if isset($advertisement) && $advertisement->with_script == 2}selected="selected"{/if}>{t}Open X zone{/t}</option>
                    {/if}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label for="content" class="control-label">&nbsp;</label>
            <div class="controls content_blocks">
                <div id="content_type_0" style="{if !isset($advertisement) || $advertisement->with_script == 0}display:block{else}display:none{/if};">
                    {include file="advertisement/partials/advertisement_images.tpl"}
                </div>
                <div id="content_type_1" style="{if isset($advertisement) && $advertisement->with_script ==1}display:block{else}display:none{/if};">
                    <textarea name="script" id="script" class="input-xxlarge" rows="10" style="width:95%">{$advertisement->script|escape:'htmlall'|default:'&lt;script type="text/javascript"&gt;/* JS code */&lt;/script&gt;'}</textarea>
                </div>
                {if !empty($server_url)}
                <div id="content_type_2" style="{if isset($advertisement) && $advertisement->with_script ==2}display:block{else}display:none{/if};">
                    <label for="openx_zone">{t}Open X zone id{/t}</label>
                    <input type="text" name="openx_zone_id" value="{$advertisement->params['openx_zone_id']}">

                    <div class="help-block">{t 1=$server_url}OpenX/Revive Ad server uses an id to identify an advertisement. Please fill the zone id from your OpenX/Revive server %1{/t}</div>
                </div>
                {/if}
            </div>
        </div>

        <div class="control-group" style="{if isset($advertisement) && $advertisement->with_script != 2}display:block{else}display:none{/if};">
            <label class="control-label"></label>
            <div class="controls">
                <div class="form-inline-block">
                    <div class="control-group">
                        <label for="params_width" class="control-label">{t}Width{/t}</label>
                        <div class="controls">
                            <input type="number" id="params_width" name="params_width" value="{$advertisement->params['width']}" {if isset($advertisement) && $advertisement->with_script != 2}required="required"{/if} min="0">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="params_height" class="control-label">{t}Height{/t}</label>
                        <div class="controls">
                            <input type="number" id="params_height" name="params_height" value="{$advertisement->params['height']}" {if isset($advertisement) && $advertisement->with_script != 2}required="required"{/if} min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-group" style="{if !isset($advertisement) || (($advertisement->type_advertisement + 50) % 100) != 0}display:none{/if};">
            <label for="timeout" class="control-label">{t}Display banner while{/t}</label>
            <div class="controls">
                <input type="number" id="timeout" name="timeout" value="{$advertisement->timeout|default:"4"}" min="0" max="100"/>
                <div class="help-block">{t}Amount of seconds that this banner will block all the page.{/t}</div>
            </div>
        </div>


        <div class="control-group" id="div_url1" style="display:{if !isset($advertisement) || $advertisement->with_script==0}block{else}none{/if};">
            <label for="url" class="control-label">{t}Url{/t}</label>
            <div class="controls">
                <input type="url" id="url" name="url" class="input-xxlarge" value="{$advertisement->url}" placeholder="http://" {if !empty($advertisement)  && ($advertisement->with_script == 0)} required="required"{/if} />
            </div>
        </div>

        <h5>{t}Where to show this ad{/t}</h5>
        <div class="control-group">
            <label for="category" class="control-label">{t}Category{/t}</label>
            <div class="controls">
                <select name="category[]" id="category" required="required" multiple>
                {if isset($advertisement->id)}
                    <option value="0" {if isset($advertisement) && in_array(0,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Frontpage{/t}</option>
                    <option value="4" {if isset($advertisement) && in_array(4,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Opinion{/t}</option>
                    <option value="3" {if isset($advertisement) && in_array(3,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Album{/t}</option>
                    <option value="6" {if isset($advertisement) && in_array(6,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Video{/t}</option>

                    {section name=as loop=$allcategorys}
                        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                        <option value="{$allcategorys[as]->pk_content_category}"
                            {if isset($advertisement) && in_array($allcategorys[as]->pk_content_category,$advertisement->fk_content_categories)}selected="selected"{/if}>
                            {$allcategorys[as]->title}
                        </option>
                        {/acl}
                        {section name=su loop=$subcat[as]}
                            {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                            <option value="{$subcat[as][su]->pk_content_category}"
                                {if isset($advertisement) && in_array($subcat[as][su]->pk_content_category,$advertisement->fk_content_categories)}selected="selected"{/if}>
                                &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                            </option>
                            {/acl}
                        {/section}
                    {/section}
                {else}
                    <option value="0" {if $category == 0}selected="selected"{/if}>{t}Frontpage{/t}</option>
                    {is_module_activated name="OPINION_MANAGER"}
                    <option value="4" {if $category == 4}selected="selected"{/if}>{t}Opinion{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="ALBUM_MANAGER"}
                    <option value="3" {if $category == 3}selected="selected"{/if}>{t}Album{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="VIDEO_MANAGER"}
                    <option value="6" {if $category == 6}selected="selected"{/if}>{t}Video{/t}</option>
                    {/is_module_activated}

                    {section name=as loop=$allcategorys}
                        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                        <option value="{$allcategorys[as]->pk_content_category}"
                            {if $category eq $allcategorys[as]->pk_content_category}selected="selected"{/if}>
                            {$allcategorys[as]->title}
                        </option>
                        {/acl}
                        {section name=su loop=$subcat[as]}
                            {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                            <option value="{$subcat[as][su]->pk_content_category}"
                                {if $category eq $subcat[as][su]->pk_content_category}selected="selected"{/if}>
                                &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                            </option>
                            {/acl}
                        {/section}
                    {/section}
                {/if}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="position" class="control-label">{t}Position{/t}</label>
            <div class="controls">
                <div id="position-adv" class="tabs">
                    <ul>
                        <li><a href="#publi-portada">{t}Frontpage{/t}</a></li>
                        <li><a href="#publi-interior">{t}Inner article{/t}</a></li>
                        {is_module_activated name="VIDEO_MANAGER"}
                        <li><a href="#publi-video">{t}Video frontpage{/t}</a></li>
                        <li><a href="#publi-video-interior">{t}Inner video{/t}</a></li>
                        {/is_module_activated}
                        {is_module_activated name="OPINION_MANAGER"}
                        <li><a href="#publi-opinion">{t}Opinion frontpage{/t}</a></li>
                        <li><a href="#publi-opinion-interior">{t}Inner opinion{/t}</a></li>
                        {/is_module_activated}
                        {is_module_activated name="ALBUM_MANAGER"}
                        <li><a href="#publi-gallery">{t}Galleries{/t}</a></li>
                        <li><a href="#publi-gallery-inner">{t}Gallery Inner{/t}</a></li>
                        {/is_module_activated}
                        {is_module_activated name="POLL_MANAGER"}
                        <li><a href="#publi-poll">{t}Poll{/t}</a></li>
                        <li><a href="#publi-poll-inner">{t}Poll Inner{/t}</a></li>
                        {/is_module_activated}
                        {is_module_activated name="NEWSLETTER_MANAGER"}
                        <li><a href="#publi-newsletter">{t}Newsletter{/t}</a></li>
                        {/is_module_activated}
                        <li><a href="#publi-others">{t}Others{/t}</a></li>
                    </ul>

                    <div id="publi-portada">
                        {include file="advertisement/partials/advertisement_positions.tpl"}
                    </div>

                    <div id="publi-interior">
                        {include file="advertisement/partials/advertisement_positions_interior.tpl"}
                    </div>
                    {is_module_activated name="VIDEO_MANAGER"}
                    <div id="publi-video">
                        {include file="advertisement/partials/advertisement_positions_video.tpl"}
                    </div>
                    <div id="publi-video-interior">
                        {include file="advertisement/partials/advertisement_positions_video_interior.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="OPINION_MANAGER"}
                    <div id="publi-opinion">
                        {include file="advertisement/partials/advertisement_positions_opinion.tpl"}
                    </div>
                    <div id="publi-opinion-interior">
                        {include file="advertisement/partials/advertisement_positions_opinion_interior.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="ALBUM_MANAGER"}
                    <div id="publi-gallery">
                        {include file="advertisement/partials/advertisement_positions_gallery.tpl"}
                    </div>
                    <div id="publi-gallery-inner">
                        {include file="advertisement/partials/advertisement_positions_gallery_inner.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="POLL_MANAGER"}
                    <div id="publi-poll">
                        {include file="advertisement/partials/advertisement_positions_poll.tpl"}
                    </div>
                    <div id="publi-poll-inner">
                        {include file="advertisement/partials/advertisement_positions_poll_inner.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="NEWSLETTER_MANAGER"}
                    <div id="publi-newsletter">
                        {include file="advertisement/partials/advertisement_positions_newsletter.tpl"}
                    </div>
                    {/is_module_activated}
                    <div id="publi-others">
                        {foreach $themeAds as $adId => $ad}
                        <tr>
                            <td colspan="2">
                                <label>
                                    {$ad['name']}
                                    <input type="radio" name="type_advertisement" value="{$adId}" {if isset($advertisement) && $advertisement->type_advertisement == $adId}checked="checked" {/if}/>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr/></td>
                        </tr>
                        {/foreach}
                    </div>
                </div><!-- /position-adv -->
            </div>
        </div>

    </div>
</form>
{/block}
