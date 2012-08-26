{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .utilities-conf {
        position:absolute;
        top:10px;
        right:10px;
    }
</style>
{/block}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery-ui-sliderAccess.js"}
    {script_tag src="/onm/jquery.datepicker.js"}
    {script_tag src="/swfobject.js"}
{/block}
{block name="footer-js" append}
    {script_tag src="/utilsGallery.js"}
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $("input[name='with_script']").on('change', function(e, ui) {
            if ($(this).val() == '1') {
                $('#script_content').show();
                $('#normal_content').hide();
                $('#hide_flash').hide();
            } else {
                $('#normal_content').show();
                $('#script_content').hide();
                $('#hide_flash').show();
            }
        });

        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });

        $('#type_medida').on('change', function(e, ui){
            var selected_option = $("#type_medida option:selected").attr('value');
            if (selected_option=='CLIC') {
                $('#porclic').show();
                $('#porview, #porfecha').hide();
                $('').hide();
            } else if (selected_option == 'VIEW') {
                $('#porview').show();
                $('#porclic, #porfecha').hide();
            } else if (selected_option=='DATE') {
                $('#porfecha').show();
                $('#porclic, #porview').hide();
            } else {
                $('#porclic, #porview, #porfecha').hide();
            }
        });

        var tabs = $('#position-adv').tabs();
        tabs.tabs('select', '{$place}');

        jQuery('#title').on('change', function(e, ui) {
            fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
        });
    });
    </script>
{/block}
{block name="header-css" append}
<style type="text/css">
object {
    z-index:0;
}
.panel {
    margin:0;
}
.panel-ads label {
    text-transform:none;
}
</style>
{/block}

{block name="content" append}
<form action="{if $advertisement->id}{url name=admin_ad_update id=$advertisement->id}{else}{url name=admin_ad_create}{/if}" method="post" id="formulario">

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
            <div class="title">
                <h2>{t}Ad manager{/t} :: {if $advertisement}{t}Creating banner{/t}{else}{t}Editing banner{/t}{/if}</h2>
            </div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_ads category=$category page=$page}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
    </div>
</div>

<div class="wrapper-content">

    <div class="form-horizontal panel">
        <div class="control-group">
            <label for="title" class="control-label">{t}Title{/t}</label>
            <div class="controls">
                <input  type="text" id="title" name="title" required="required" class="input-xxlarge"
                    value="{$advertisement->title|clearslash|escape:"html"|default:""}" />
            </div>
        </div>
        <div class="control-group">
            <label for="metadata" class="control-label">{t}Keywords{/t}</label>
            <div class="controls">
                <input type="text" id="metadata" name="metadata" class="input-xxlarge" required="required"
                    title="Metadatos" value="{$advertisement->metadata|strip|default:""}">
            </div>
        </div>

        <div class="control-group">
            <label for="metadata" class="control-label">{t}Actived{/t}</label>
            <div class="controls">
                <input type="checkbox" name="available" id="available" {if isset($advertisement->available) && $advertisement->available == 1}checked="checked"{/if} {acl isNotAllowed="ADVERTISEMENT_AVAILA"}disabled="disabled"{/acl} />

            </div>
        </div>

        <div class="control-group" id="div_url1" style="display:{if !isset($advertisement) || $advertisement->with_script==0}block{else}none{/if};">
            <label for="url" class="control-label">{t}Url{/t}</label>
            <div class="controls">
                <input type="text" id="url" name="url" class="input-xxlarge" required="required"
                    value="{$advertisement->url}" placeholder="http://" />
            </div>
        </div>

        <div class="control-group">
            <label for="category" class="control-label">{t}Category{/t}</label>
            <div class="controls">
                <select name="category[]" id="category" required="required" multiple>
                {if isset($advertisement->id)}
                    <option value="0" {if isset($advertisement) && in_array(0,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Frontpage{/t}</option>
                    <option value="4" {if isset($advertisement) && in_array(4,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Opinion{/t}</option>

                    {section name=as loop=$allcategorys}
                        <option value="{$allcategorys[as]->pk_content_category}"
                            {if isset($advertisement) && in_array($allcategorys[as]->pk_content_category,$advertisement->fk_content_categories)}selected="selected"{/if}>
                            {$allcategorys[as]->title}
                        </option>
                        {section name=su loop=$subcat[as]}
                            <option value="{$subcat[as][su]->pk_content_category}"
                                {if isset($advertisement) && in_array($allcategorys[as]->pk_content_category,$advertisement->fk_content_categories)}selected="selected"{/if}>
                                &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                            </option>
                        {/section}
                    {/section}
                {else}
                    <option value="0" {if $category == 0}selected="selected"{/if}>{t}Frontpage{/t}</option>
                    {is_module_activated name="OPINION_MANAGER"}
                    <option value="4" {if $category == 4}selected="selected"{/if}>{t}Opinion{/t}</option>
                    {/is_module_activated}


                    {section name=as loop=$allcategorys}
                        <option value="{$allcategorys[as]->pk_content_category}"
                            {if $category eq $allcategorys[as]->pk_content_category}selected="selected"{/if}>
                            {$allcategorys[as]->title}
                        </option>
                        {section name=su loop=$subcat[as]}
                            <option value="{$subcat[as][su]->pk_content_category}"
                                {if $category eq $subcat[as][su]->pk_content_category}selected="selected"{/if}>
                                &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                            </option>
                        {/section}
                    {/section}
                {/if}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">{t}Restrictions{/t}</label>
            <div class="controls">
                <select name="type_medida" id="type_medida">
                    <option value="NULL" {if !isset($advertisement) || is_null($advertisement->type_medida)}selected="selected"{/if}>{t}Without limits{/t}</option>
                    <option value="CLIC" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'CLIC'}selected="selected"{/if}>{t}Click count{/t}</option>
                    <option value="VIEW" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'VIEW'}selected="selected"{/if}>{t}Views count{/t}</option>
                    <option value="DATE" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'DATE'}selected="selected"{/if}>{t}Date range{/t}</option>
                </select>
                <div class="help-block">{t}Show this ad if satisfy one condition{/t}.</div>
            </div>
        </div>
        <div class="control-group" id="porclic" style="{if $advertisement->type_medida!='CLIC'}display:none{/if};">
            <label for="num_clic" class="control-label">{t}# of clics{/t}</label>
            <div class="controls">
                <input type="text" id="num_clic" name="num_clic" value="{$advertisement->num_clic|default:""}" />
                <input type="hidden" id="num_clic_count" name="num_clic_count" title="Numero de clic" value="{$advertisement->num_clic_count|default:""}" />
                {if isset($advertisement) && $advertisement->type_medida == 'CLIC'}<div class="help-inline">{t}Actual click count:{/t} {$advertisement->num_clic_count}</div>{/if}
                <div class="help-block">{t}Show this ad only if users had clicked in it less than a number of times.{/t}.</div>
            </div>
        </div>
        <div class="control-group" id="porview" style="{if $advertisement->type_medida!='VIEW'}display:none{/if};">
            <label for="num_view" class="control-label">{t}Max views{/t}</label>
            <div class="controls">
                <input type="text" id="num_view" name="num_view" value="{$advertisement->num_view}" />
                {if isset($advertisement) && $advertisement->type_medida == 'VIEW' && $advertisement->views > 0}<div class="help-inline">{t}Actual views count:{/t} {$advertisement->views}</div>{/if}
                <div class="help-block">{t}Show this ad only if this add had been printed less than a number of times.{/t}.</div>
            </div>
        </div>
        <div class="control-group" id="porfecha" style="{if $advertisement->type_medida neq 'DATE'}display:none{/if};">
            <label for="starttime" class="control-label">{t}Date range{/t}</label>
            <div class="controls">
                <label for="starttime">{t}From{/t}</label>
                <input type="text" id="starttime"  name="starttime" title="Fecha inicio publicacion"
                    value="{if isset($advertisement) && $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />
                <label for="endtime">{t}Until{/t}</label>
                <input type="text" id="endtime" name="endtime" title="Fecha fin publicacion"
                    value="{if isset($advertisement) && $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />
                <div class="help-block">{t}Show this ad within a range of dates.{/t}.</div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">{t}Content{/t}</label>
            <div class="controls">
                <label for="with_script_0"><input type="radio" name="with_script" id="with_script_0" value="0" {if !isset($advertisement) || $advertisement->with_script == 0}checked="checked"{/if}> {t}Image or flash from library{/t}</label>
                <label for="with_script_1"><input type="radio" name="with_script" id="with_script_1" value="1" {if isset($advertisement) && $advertisement->with_script == 1}checked="checked"{/if}> {t}Custom HTML or Javascript code{/t}</label>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">&nbsp;</label>
            <div class="controls">
                <div id="normal_content" style="{if !isset($advertisement) || $advertisement->with_script == 0}display:block{else}display:none{/if};">
                    {include file="advertisement/partials/advertisement_images.tpl"}
                </div>
                <div id="script_content" style="{if isset($advertisement) && $advertisement->with_script ==1}display:block{else}display:none{/if};">
                    <textarea name="script" id="script" class="input-xxlarge" rows="10">{$advertisement->script|default:'&lt;script type="text/javascript"&gt;/* JS code */&lt;/script&gt;'}</textarea>
                </div>
            </div>
        </div>
        <div class="control-group" id="hide_flash" style="{if isset($advertisement->overlap) && $advertisement->with_script == 0}display:block{else}display:block{/if}">
            <label for="overlap" class="control-label">{t}Hide Flash events{/t}</label>
            <div class="controls">
                <input type="checkbox" name="overlap" id="overlap" value="1" {if isset($advertisement->overlap) && $advertisement->overlap == 1}checked="checked"{/if} />
                <div class="help-inline">{t}Mark this if you want to overide the default click handler for Flash based ads.{/t}</div>
            </div>
        </div>
        <div class="control-group" style="{if !isset($advertisement) || $advertisement->type_advertisement != 50}display:none{/if};">
            <label for="with_script" class="control-label">{t}Display time{/t}</label>
            <div class="controls">
                <input type="text" id="timeout" name="timeout" value="{$advertisement->timeout|default:"4"}" />
                <div class="help-block">{t}This banner blocks all the page so hide it after this amount of seconds.{/t}</div>
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
                </div><!-- /position-adv -->
            </div>
        </div>

    </div>

    <input type="hidden" name="filter[type_advertisement]" value="{$smarty.request.filter.type_advertisement|default:""}" />
    <input type="hidden" name="filter[available]" value="{$smarty.request.filter.available|default:""}" />
    <input type="hidden" name="filter[type]" value="{$smarty.request.filter.type|default:""}" />
</form>
{/block}
