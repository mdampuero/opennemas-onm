{extends file="base/admin.tpl"}

{block name="header-css" append}
    {stylesheets src="@AdminTheme/css/parts/specials.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}">
    {/stylesheets}
    <style>
    .thumbnails>li {
        margin:0;
    }
    .thumbnails {
        margin:0;
    }
    </style>
{/block}

{block name="footer-js" append}
    {javascripts src="@AdminTheme/js/onm/content-provider.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
    <script>
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

            jQuery('#noticias_right_input').val(JSON.stringify(els));

            els = [];

            jQuery('#column_left').find('ul.content-receiver li').each(function (index, item) {
                els.push({
                    'id' : jQuery(item).data('id'),
                    'content_type': jQuery(item).data('type'),
                    'position': index
                });
            });

            jQuery('#noticias_left_input').val(JSON.stringify(els));
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
            <div class="title"><h2>{if !isset($special->id)}{t}Creating special{/t}{else}{t}Editing special{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    {if !is_null($special->id)}
                    {acl isAllowed="SPECIAL_UPDATE"}
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Update{/t}
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
        {render_messages}
        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="title" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" required="required" class="input-xxlarge"
                            value="{$special->title|clearslash|escape:"html"}"/>
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Metadata{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" required="required" class="input-xxlarge"
                            value="{$special->metadata|clearslash|escape:"html"}"/>
                    <div class="help-block">{t}List of words separated by commas.{/t}</div>
                </div>
            </div>
            <div class="control-group">
                <label for="category" class="control-label">{t}Category{/t}</label>
                <div class="controls">
                    {include file="common/selector_categories.tpl" name="category" item=$special}
                </div>
            </div>
            <div class="control-group">
                <label for="content_status" class="control-label">{t}Available{/t}</label>
                <div class="controls">
                    <input type="checkbox" name="content_status" id="content_status" value="1" {if $special->content_status eq 1} checked="checked"{/if}>
                </div>
            </div>
            <div class="control-group">
                <label for="subtitle" class="control-label">{t}Subtitle{/t}</label>
                <div class="controls">
                    <input type="text" id="subtitle" name="subtitle" class="input-xxlarge" value="{$special->subtitle|clearslash|escape:"html"}" />
                </div>
            </div>
            <div class="control-group">
                <label for="slug" class="control-label">{t}Slug{/t}</label>
                <div class="controls">
                    <input  type="text" id="slug" name="slug" class="input-xlarge"
                            value="{$special->slug|clearslash|escape:"html"}" />
                </div>
            </div>
            <div class="control-group">
                <label for="description" class="control-label">{t}Description{/t}</label>
                <div class="controls">
                    <textarea name="description" id="description" class="onm-editor">{$special->description|clearslash}</textarea>
                </div>
            </div>
            {include file="special/partials/_load_images.tpl"}

            {include file="special/partials/_contents_containers.tpl"}
        </div>
    </div>
    <input type="hidden" id="noticias_right_input" name="noticias_right_input" value="">
    <input type="hidden" id="noticias_left_input" name="noticias_left_input" value="">

</form>
{include file="media_uploader/media_uploader.tpl"}
<script>
jQuery(document).ready(function($){
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
                image_data_el.find('.related-element-footer').val(params.description);
                image_data_el.find('.image').html(image_element);
                container.addClass('assigned');
            }
        }
    });
    $('.article_images .unset').on('click', function (e, ui) {
        e.preventDefault();

        var parent = jQuery(this).closest('.contentbox');

        parent.find('.related-element-id').val('');
        parent.find('.image').html('');

        parent.removeClass('assigned');
    });
});
</script>
{/block}
