{extends file="base/admin.tpl"}

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
        $('#with_script').on('click', function(e, ui) {
            if ($(this).is(':checked') === true){
                $('#div_script').show();
                $('#div_url1').hide();
                $('#advertisement-images').hide();
            } else {
                $('#advertisement-images').show();
                $('#photos').show();
                $('#div_url1').show();
                $('#div_script').hide();
            }
        });

        $('#type_medida').on('change', function(e, ui){
            var selected_option = $("#type_medida option:selected").attr('value');
            log(selected_option);
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
label {
    display:block;
    color:#666;
    text-transform:uppercase;
}
.panel {
    background:White;
}
fieldset {
    border:none;
    border-top:1px solid #ccc;
}
legend {
    color:#666;
    text-transform:uppercase;
    font-size:13px;
    padding:0;
    padding-bottom:4px;
    margin:0;
    line-height:14px;
    border-bottom:1px solid #ccc;
}
.panel {
    margin:0;
}
.panel-ads label {
    text-transform:none;
}
input, select, textarea {
    margin-bottom:10px;
}
</style>
{/block}

{block name="content" append}
<form action="{if $advertisement->id}{url name=admin_ad_update id=$advertisement->id}{else}{url name=admin_ad_create}{/if}" method="post" id="formulario">

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
            <div class="title">
                <h2>{t}Ad manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating banner{/t}{else}{t}Editing banner{/t}{/if}</h2>
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

        <div class="panel clearfix" style="display:block">
            <fieldset style="vertical-align:top">
                <legend>{t}Basic information{/t}</legend>


                <div style="display:inline-block; width:78%; vertical-align:top">
                    <label for="title">{t}Name{/t}</label>
                    <input  type="text" id="title" name="title" title="Publicidad"
                            tabindex=1
                            value="{$advertisement->title|clearslash|escape:"html"|default:""}"
                            class="required"
                            style="width:90%"/>

                    <label for="metadata">{t}Keywords:{/t} <small>{t}Separated by commas{/t}</small></label>

                    <input type="text" id="metadata" name="metadata" style="width:90%" tabindex=2
                       title="Metadatos" value="{$advertisement->metadata|strip|default:""}">

                    <div style="display:inline-block; width:30%; vertical-align:top">
                        <label for="category">{t}Sections{/t}</label>
                        <select name="category[]" id="category" class="required" multiple tabindex=4 style="width:95%">
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

                    </div><!-- / -->

                    <div style="display:inline-block; width:30%; vertical-align:top">
                        <label>{t}View restrictions:{/t}</label>
                        <select name="type_medida" id="type_medida">
                            <option value="NULL" {if !isset($advertisement) || is_null($advertisement->type_medida)}selected="selected"{/if}>{t}Without limits{/t}</option>
                            <option value="CLIC" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'CLIC'}selected="selected"{/if}>{t}Click count{/t}</option>
                            <option value="VIEW" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'VIEW'}checked="checked"{/if}>{t}Views count{/t}</option>
                            <option value="DATE" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'DATE'}checked="checked"{/if}>{t}Date range{/t}</option>
                        </select>
                    </div>

                    <div style="display:{if $advertisement->with_script==1}none{else}inline-block{/if}; width:30%; vertical-align:top">

                        <div id="porclic" style="display:{if $advertisement->type_medida!='CLIC'}none{/if};">
                            <label for="title">{t}# of clicks:{/t}</label>
                            <input type="text" id="num_clic" name="num_clic" title="Numero de clic"
                                value="{$advertisement->num_clic|default:""}" />
                            {if $smarty.request.action eq "read"}
                                {if isset($advertisement) && $advertisement->type_medida == 'CLIC'}
                                    {t}Actual click count:{/t} {$advertisement->num_clic_count}
                                {/if}
                                <input type="hidden" id="num_clic_count" name="num_clic_count" title="Numero de clic"
                                    value="{$advertisement->num_clic_count|default:""}" />
                            {/if}
                        </div>

                        <div id="porview" style="display:{if $advertisement->type_medida!='VIEW'}none{/if};">
                            <label for="title">{t}Max views{/t}</label>
                            <input type="text" id="num_view" name="num_view" title="Numero de visionados"
                                value="{$advertisement->num_view}" />
                            {if isset($advertisement) && $advertisement->type_medida == 'VIEW' && $advertisement->views > 0}
                                {t}Actual views count:{/t} {$advertisement->views}
                            {/if}
                        </div>

                        <div id="porfecha" style="width:190px;{if  $advertisement->type_medida neq 'DATE'} display:none{else}display:block{/if};">
                            <label for="title">{t}Date range{/t}</label>
                            {t}From:{/t} <input type="text" id="starttime"  name="starttime" title="Fecha inicio publicacion"
                                value="{if isset($advertisement) && $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />
                            {t}To:{/t} <input type="text" id="endtime" name="endtime" title="Fecha fin publicacion"
                                value="{if isset($advertisement) && $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />
                        </div>

                    </div>
                </div><!-- / -->

                <div style="display:inline-block; width:20%">
                    <label for="available">{t}Published:{/t}</label>
                    <select name="available" id="available" style="width:50px;" {acl isNotAllowed="ADVERTISEMENT_AVAILA"}disabled="disabled"{/acl}>
                        <option value="1" {if isset($advertisement->available) && $advertisement->available == 1}selected="selected"{/if}>Si</option>
                        <option value="0" {if isset($advertisement->available) && $advertisement->available == 0}selected="selected"{/if}>No</option>
                    </select>
                    <br/>

                    <label for="overlap" style="display:inline-block;">{t}Hide Flash events:{/t}</label>
                    <input type="checkbox" name="overlap" id="overlap" value="1" {if isset($advertisement->overlap) && $advertisement->overlap == 1}checked="checked"{/if} />

                    <div id="timeout_container" style="display:{if !isset($advertisement) || $advertisement->type_advertisement!=50}none{/if};">
                        <label for="timeout">{t escape="off"}Display during<br/><small>( -1 allways visible)</small>{/t}:</label>
                        <input type="text" id="timeout" name="timeout" size="2" title="Segundos antes de desaparecer"
                            value="{$advertisement->timeout|default:"4"}" style="text-align: right;" />
                    </div>
                </div><!-- / -->

            </fieldset>

            <fieldset>
                <legend>{t}Content{/t}</legend>

                <label for="with_script" style="display:inline-block;">{t}Ad with JavaScript:{/t}</label>
                <input type="checkbox" id="with_script" name="with_script" value="1" tabindex=5
                    {if isset($advertisement) && $advertisement->with_script == 1}checked="checked"{/if} />


                <div id="div_url1" style="display:{if !isset($advertisement) || $advertisement->with_script==0}block{else}none{/if};">
                    <label for="title" style="display:inline-block;">{t}Ad url:{/t}</label>
                    <input type="text" id="url" name="url" class="validate-url" title="Direccion web Publicidad"
                        style="width:90%" value="{$advertisement->url|default:"http://"}" tabindex=3 />
                </div>

                {include file="advertisement/partials/advertisement_images.tpl"}

                <div id="div_script" style="display:{if isset($advertisement) && $advertisement->with_script ==1}block{else}none{/if};">
                    <textarea name="script" id="script" class="validate-script" title="script de publicidad" style="width:99%; height:8em;">{$advertisement->script|default:'&lt;script type="text/javascript"&gt;/* Código javascript */&lt;/script&gt;'}</textarea>
                </div>

            </fieldset>

            <fieldset>
                <legend>{t}Position{/t}</legend>

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
            </fieldset>
        </div><!-- / -->

        <input type="hidden" name="filter[type_advertisement]" value="{$smarty.request.filter.type_advertisement|default:""}" />
        <input type="hidden" name="filter[available]" value="{$smarty.request.filter.available|default:""}" />
        <input type="hidden" name="filter[type]" value="{$smarty.request.filter.type|default:""}" />
    </div>
</form>
{/block}
