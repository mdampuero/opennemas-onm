{acl isAllowed='PHOTO_ADMIN'}
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
{/is_module_activated}
{/acl}
