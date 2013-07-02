<ul id='thelist' class="gallery_list clearfix" style="width:100%; margin:0px; padding:0px">
   {assign var=num value='1'}
   {section name=n loop=$photos}
        <li class="thumbnail">
            <div style="float: left;">
                <a href="#">
                    {if $photos[n]->type_img=='swf' || $photos[n]->type_img=='SWF'}
                        <object width="68" height="40" style="z-index:-3; cursor:default;">
                            <param name="movie" value="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}{$photos[n]->name}">
                            <param name="autoplay" value="false">
                            <param name="autoStart" value="0">
                            <embed  width="68" height="40"
                                    src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}{$photos[n]->name}"
                                    name="{$photos[n]->pk_photo}">
                            </embed>
                        </object>
                        <span  style="float:right; clear:none; width:100%; height:100%; z-index:1;">
                            <img id="draggable_img{$num}"
                                 class="draggable-handler"
                                 style="width:16px;height:16px;"
                                 src="/themes/admin/images/flash.gif"
                                 data-id="{$photos[n]->pk_photo}"
                                 data-url="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}"
                                 data-filename="{$photos[n]->name}"
                                 data-filepath="{$photos[n]->path_file}"
                                 data-width="{$photos[n]->width}"
                                 data-height="{$photos[n]->height}"
                                 data-weight="{$photos[n]->size}"
                                 data-created="{$photos[n]->created}"
                                 data-type-img="{$photos[n]->type_img}"
                                 data-description="{$photos[n]->description}"
                                 data-tags="{$photos[n]->metadata}"
                                 alt="{$photos[n]->description}"
                                 title="({$photos[n]->width}x{$photos[n]->height}) {$photos[n]->description}"
                                 />
                        </span>
                    {else}
                        {dynamic_image
                            transform="thumbnail,200,200"
                            src="{$photos[n]->path_file}{$photos[n]->name}"
                            id="draggable_img{$num}"
                            class="draggable-handler"
                            data-id="{$photos[n]->pk_photo}"
                            data-url="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}{$photos[n]->name}"
                            data-filename="{$photos[n]->name}"
                            data-filepath="{$photos[n]->path_file}"
                            data-width="{$photos[n]->width}"
                            data-height="{$photos[n]->height}"
                            data-weight="{$photos[n]->size}"
                            data-created="{$photos[n]->created}"
                            data-type-img="{$photos[n]->type_img}"
                            data-description="{$photos[n]->description|clearslash|escape:'html'}"
                            data-tags="{$photos[n]->metadata}"
                            alt="{$photos[n]->description}"
                            title="({$photos[n]->width}x{$photos[n]->height}) {$photos[n]->description}"}
                    {/if}
                </a>
            </div>
        </li>
        {assign var=num value=$num+1}
    {sectionelse}
    {t}No available images with your search criteria{/t}
    {/section}
</ul>

{$imagePager}

<script>
jQuery(document).ready(function($){
    $( "#photos_container #photos .draggable-handler" ).draggable({ opacity: 0.5, helper: "clone"});
});
</script>