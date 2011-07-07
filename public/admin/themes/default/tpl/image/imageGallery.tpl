<ul id='thelist' class="gallery_list clearfix" style="width:100%; margin:0px; padding:0px">
   {assign var=num value='1'}
   {section name=n loop=$photos}
        <li>
            <div style="float: left;">
                <a>
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
