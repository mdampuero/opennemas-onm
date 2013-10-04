{acl isAllowed='IMAGE_ADMIN'}
{is_module_activated name="IMAGE_MANAGER"}
<div id="related_media" class="control-group">
    <label for="special-image" class="control-label">{t}Image for Special{/t}</label>
    <div class="controls">
        <ul class="related-images thumbnails">
            <li class="contentbox frontpage-image {if isset($photo1) && $photo1->name}assigned{/if}">
                <h3 class="title">{t}Frontpage image{/t}</h3>
                <div class="content">
                    <div class="image-data">
                        <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="image thumbnail">
                            <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}"/>
                        </a>
                        <input type="hidden" name="img1" value="{$special->img1|default:""}" class="related-element-id" />
                    </div>

                    <div class="not-set">
                        {t}Image not set{/t}
                    </div>

                    <div class="btn-group">
                        <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="btn btn-small">{t}Set image{/t}</a>
                        <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

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
{/is_module_activated}
{/acl}
