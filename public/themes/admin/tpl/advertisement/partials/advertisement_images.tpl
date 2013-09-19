<div id="related_media">
    <ul class="related_images thumbnails clearfix">
        <li class="contentbox ad-image {if isset($photo1) && $photo1->name}assigned{/if}">
            <div class="content">
                <div class="image-data">
                    <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="image thumbnail">
                        {if isset($photo1) && strtolower($photo1->type_img)=='swf'}
                        <div id="flash-container-replace"></div>
                        <!-- /flash-container-replace -->
                        <script>
                            var flashvars = {};
                            var params = { wmode: "opaque" };
                            var attributes = {};
                            swfobject.embedSWF("{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}", "flash-container-replace", "270", "150", "9.0.0", false, flashvars, params, attributes);
                        </script>
                        {elseif isset($photo1) && $photo1->name}
                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}" />
                        {/if}
                    </a>
                    <div class="image-information" class="article-resource-image-info">

                        <div class="image-title">{$photo1->name|default:'default_img.jpg'}</div>
                        <div class="info">
                            <div class="image_size">{$photo1->width|default:0} x {$photo1->height|default:0}</div>
                            <div class="file_size">{$photo1->size|default:0} Kb</div>
                            <div class="created_time">{$photo1->created|default:""}</div>
                        </div>
                    </div>
                    <div class="article-resource-footer">
                        <input class="related-element-id" type="hidden" name="img" value="{$advertisement->img|default:""}" />
                    </div>
                </div>

                <div class="not-set">
                    {t}Image not set{/t}
                </div>

                <div class="btn-group pull-right">
                    <a href="#media-uploader" data-toggle="modal" data-position="ad-image" class="btn btn-small">{t}Set image{/t}</a>
                    <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                </div>
            </div>
        </li>
    </ul>
</div>
<style>
.related_images li {
    margin:0;
}
.image-data .thumbnail {
    float:left;
    margin-right:10px;
}
.image-information {
    float:left
}
.btn-group {
    clear:both;
    display:block;
}
.image-information .image-title {
    font-weight:bold;
}

.image-information .info {
    font-size: .9em;
    color: gray;
}

</style>
<script type="text/javascript">

jQuery(document).ready(function($) {

    $('#formulario').on('change', "input[name='with_script']", function(e, ui) {
        if ($(this).val() == '1') {
            $('#script_content').show();
            $('#normal_content').hide();
            $('#hide_flash').hide();
            $('#div_url1').hide();
            $('#url').removeAttr('required');
        } else {
            $('#normal_content').show();
            $('#script_content').hide();
            $('#hide_flash').show();
            $('#div_url1').show();
            $('#url').attr('required', 'required');
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

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    var tabs = $('#position-adv').tabs();
    tabs.tabs('select', '{$place}');

});
</script>
