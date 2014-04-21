<div id="related_media">
    <ul class="related_images thumbnails clearfix">
        <li class="contentbox ad-image {if isset($photo1) && $photo1->name}assigned{/if}">
            <div class="clearfix">
                <div class="image-data clearfix">
                    <a href="#media-uploader" {acl isAllowed='PHOTO_ADMIN'}data-toggle="modal"{/acl} data-position="frontpage-image" class="image thumbnail">
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
                        <div class="image_title">{$photo1->name}</div>
                        <div class="info">
                            <div class="image_size">{$photo1->width|default:0} x {$photo1->height|default:0}</div>
                            <div class="file_size">{$photo1->size|default:0} Kb</div>
                            <div class="created_time">{$photo1->created|default:""}</div>
                            <div class="flash-based-warning" style="{if strtolower($photo1->type_img) !=='swf'}display:none{/if}">
                                <div class="flash-based"><i class="icon-warning-sign"></i> {t}Flash based{/t}</div>
                                <br>
                                <label for="overlap" class="overlap-message">
                                    <input type="checkbox" name="overlap" id="overlap" value="1" {if isset($advertisement->overlap) && $advertisement->overlap == 1}checked="checked"{/if} />
                                    {t}Overide default click handler.{/t} <i class="icon-question-sign" title="{t}When you click in some Flash-based advertisements they redirect you to another web site. If you want to overlap that address with that specified by you above you should mark this.{/t}"> </i>
                                </label>
                            </div>

                        </div>
                    </div>
                    <div class="article-resource-footer">
                        <input class="related-element-id" type="hidden" name="img" value="{$advertisement->img|default:""}" />
                    </div>
                </div>

                <div class="not-set">
                    {t}Image not set{/t}
                </div>

                <div class="btn-group">
                    <a href="#media-uploader" {acl isAllowed='PHOTO_ADMIN'}data-toggle="modal"{/acl} data-position="ad-image" class="btn btn-small">{t}Select image{/t}</a>
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
    margin:10px 0;
}
.flash-based {
    margin:10px 0;
}

.contentbox{
    border:0 none;
    margin-bottom:10px;
}

.overlap-message {
    display:block;
    clear:both;
}

@media (min-width:800px) {
    .image-data > * {
        max-width:49%;
    }

    .content_part > div {
        margin-left:18px;
    }
}
</style>
