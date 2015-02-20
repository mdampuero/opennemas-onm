{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .utilities-conf {
        position:absolute;
        top:0;
        right:0;
    }
</style>
{/block}

{block name="footer-js" append}
<script type="text/javascript">
    var mediapicker = $('#media-uploader').mediaPicker({
        upload_url: "{url name=admin_image_create category=0}",
        browser_url : "{url name=admin_media_uploader_browser}",
        months_url : "{url name=admin_media_uploader_months}",
        maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
        // initially_shown: true,
        handlers: {
            'assign_content' : function( event, params ) {
                var mediapicker = $(this).data('mediapicker');
                var image_element = mediapicker.buildHTMLElement(params);

                var container = $('#related_media').find('.'+params['position']);

                var image_data_el = container.find('.image-data');
                image_data_el.find('.related-element-id').val(params.content.pk_photo);
                image_data_el.find('.image').html(image_element);
                container.addClass('assigned');
            }
        }
    });
    var video_manager_url = {
        get_information: '{url name=admin_videos_get_info}',
        fill_tags: '{url name=admin_utils_calculate_tags}'
    }

    jQuery(document).ready(function($){
        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });
    });

    $('#title').on('change', function(e, ui) {
        fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
    });

    $('.article_images .unset').on('click', function (e, ui) {
        e.preventDefault();

        var parent = jQuery(this).closest('.contentbox');

        parent.find('.related-element-id').val('');
        parent.find('.image').html('');

        parent.removeClass('assigned');
    });
</script>
    {javascripts src="@AdminTheme/js/onm/video.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
{/block}

{block name="content"}
<form action="{if isset($video)}{url name=admin_videos_update id=$video->id}{else}{url name=admin_videos_create}{/if}" method="POST" name="formulario" id="formulario" enctype="multipart/form-data">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Videos{/t}
                    </h4>
                </li>
                <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
                <li class="quicklinks hidden-xs">
                    <h5>{if !isset($video)}{t}Creating video{/t}{else}{t}Editing video{/t}{/if}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name=admin_videos category=$category|default:""}" class="btn btn-link" title="{t}Go Back{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                    {if isset($video->id)}
                        {acl isAllowed="VIDEO_UPDATE"}
                            <button class="btn btn-primary" type="submit">
                        {/acl}
                    {else}
                        {acl isAllowed="VIDEO_CREATE"}
                            <button class="btn btn-primary" type="submit">
                        {/acl}
                    {/if}
                            <span class="fa fa-save"></span>
                            {t}Save{/t}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="content">

    {render_messages}
    <div class="row">
        <div class="col-md-8">
            <div class="grid simple">
                <div class="grid-body">
                    {if $type == "file" || (isset($video) && $video->author_name == 'internal')}
                        {include file="video/partials/_form_video_internal.tpl"}
                    {elseif $type == "external" || (isset($video) && $video->author_name == 'external')}
                        {include file="video/partials/_form_video_external.tpl"}
                    {elseif $type == "script" || (isset($video) && $video->author_name == 'script')}
                        {include file="video/partials/_form_video_script.tpl"}
                    {else}
                        {include file="video/partials/_form_video_panorama.tpl"}
                    {/if}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="grid simple">
                <div class="grid-title">{t}Attributes{/t}</div>
                <div class="grid-body">

                    <label for="content_status" >
                        <input type="checkbox" value="1" id="content_status" name="content_status" {if $video->content_status eq 1}checked="checked"{/if}>
                        {t}Available{/t}
                    </label>

                    {is_module_activated name="COMMENT_MANAGER"}
                    <label for="with_comment">
                        <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($video) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($video) && $video->with_comment eq 1)}checked{/if} value="1" />
                        {t}Allow comments{/t}
                    </label>
                    {/is_module_activated}

                    <div class="form-group">
                        <label for="category" class="form-label">{t}Category{/t}</label>
                        <div class="controls">
                            {include file="common/selector_categories.tpl" name="category" item=$video}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fk_author" class="form-label">{t}Author{/t}</label>
                        <div class="controls">
                            {acl isAllowed="CONTENT_OTHER_UPDATE"}
                                <select name="fk_author" id="fk_author">
                                    {html_options options=$authors selected=$video->fk_author}
                                </select>
                            {aclelse}
                                {if !isset($video->fk_author)}
                                    {$smarty.session.realname}
                                    <input type="hidden" name="fk_author" value="{$smarty.session.userid}">
                                {else}
                                    {$authors[$video->fk_author]}
                                    <input type="hidden" name="fk_author" value="{$video->fk_author}">
                                {/if}
                            {/acl}
                        </div>
                    </div>
                    <div class="form-group">
                        <lable for="metadata" class="form-label">{t}Tags{/t}</h3>
                        <div class="controls">
                            <input  type="text" id="metadata" name="metadata" required="required" value="{$video->metadata}" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="form-vertical video-edit-form">

            <input type="hidden" value="1" name="content_status">
            <input type="hidden" name="type" value="{$smarty.get.type}">
            <input type="hidden" name="id" id="id" value="{$video->id|default:""}" />
        </div>
	</div>
</form>
{/block}
