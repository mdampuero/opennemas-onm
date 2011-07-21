<ul id='thelist' class="gallery_list clearfix" style="width:100%; margin:0px; padding:0px">
   {assign var=num value='1'}
   {section name=n loop=$photos}
        <li>
            <div style="float: left;">
                <a>
                    {if $photos[n]->type_img=='swf' || $photos[n]->type_img=='SWF'}
                        <object style="z-index:-3; cursor:default;"  title="Desc: {$photos[n]->description} Tags: {$photos[n]->metadata} ">
                            <param name="movie" value="{$marty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}{$photos[n]->name}">
                            <param name="autoplay" value="false">  <param name="autoStart" value="0">
                            <embed  width="68" height="40" style="cursor:default;"
                                    src="{$marty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}{$photos[n]->name}"
                                    name="{$photos[n]->pk_photo}" border="0" de:mas="{$photos[n]->name}"
                                    de:url="{$MEDIA_IMG_PATH_URL}{$photos[n]->path_file}"
                                    de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}"
                                    de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}"
                                    de:type_img="{$photos[n]->type_img}"
                                    de:description="{$photos[n]->description}"
                                    title="{$photos[n]->title} - {$photos[n]->description}">
                            </embed>
                        </object>
                        <span  style="float:right; clear:none;">
                            <img id="draggable_img{$num}" class="draggable"
                                 src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif"
                                 style="width:20px" name="{$photos[n]->pk_photo}" border="0"
                                 de:mas="{$photos[n]->name}"
                                 de:url="{$marty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}"
                                 de:ancho="{$photos[n]->width}" de:alto="{$photos[n]->height}"
                                 de:peso="{$photos[n]->size}" de:created="{$photos[n]->created}"
                                 de:type_img="{$photos[n]->type_img}"
                                 de:description="{$photos[n]->description}"
                                 title="Desc: {$photos[n]->description}  Tags: {$photos[n]->metadata}" />
                        </span>
                    {else}
                        <img style="{cssphotoscale width=$photos[n]->width height=$photos[n]->height resolution=67 photo=$photos[n]}"
                            src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}140-100-{$photos[n]->name}"
                            id="draggable_img{$num}" class="draggable" name="{$photos[n]->pk_photo}"
                            border="0" de:mas="{$photos[n]->name}"
                            de:path="{$photos[n]->path_file}" 
                            de:ancho="{$photos[n]->width}"
                            de:alto="{$photos[n]->height}"
                            de:peso="{$photos[n]->size}"
                            de:created="{$photos[n]->created}"
                            de:description="{$photos[n]->description|clearslash|escape:'html'}"
                            de:tags="{$photos[n]->metadata}"
                            onmouseover="return escape('Descripcion:{$photos[n]->description|clearslash|escape:'html'}<br>Etiquetas:{$photos[n]->metadata}');"
                            title="Desc:{$photos[n]->description|clearslash|escape:'html'} - Tags:{$photos[n]->metadata}" />
                    {/if}
                </a>
            </div>
            <script type="text/javascript">
                new Draggable('draggable_img{$num}', { revert:true, scroll: window, ghosting:true } );
            </script>
        </li>
        {assign var=num value=$num+1}
    {/section}
</ul>
{if !empty($imagePager)}
    <div class="pagination" align="center"> {$imagePager} </div>
{/if}
