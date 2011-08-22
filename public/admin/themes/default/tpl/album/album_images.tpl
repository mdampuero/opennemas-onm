<style type="text/css">
    #testWrap {
            width: 600px;
            overflow:hidden;
            float: left;
            margin: 20px 0 0 50px; /* Just while testing, to make sure we return the correct positions for the image & not the window */
    }

    #previewArea {
            margin: 20px; 0 0 20px;
            float: left;
            background:#F5F5F5 none repeat scroll 0 0;
            border:2px double #333333;
    }
</style>



<tr>
    <td colspan="3" >
        <p>
            <label>{t}Album images (Double click to select and cut images){/t}</label>
        </p>
	    <div id="scroll-album">
		    <ul class="gallery_list" id="album_list">
                {if !empty($photoData)}
                     {assign var=indi value='1'}          
                     {section name=n loop=$photoData}
                         <li value="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoData[n]->path_file}{$photoData[n]->name}"
                             de:pk_photo="{$photoData[n]->pk_photo}"  id="f{$indi}-{$photoData[n]->pk_photo}">
                             {if strtolower($photoData[n]->type_img)=='swf'}
                                 <a class="album" title="{t}Show image{/t}"
                                    onClick="show_image('img{$photoData[n]->pk_photo}', 'f{$indi}-{$photoData[n]->pk_photo}')">
                                     <object id="change2" >
                                        <param name="movie" value="{$smarty.const.MEDIA_IMG_PATH_URL}{$photoData[n]->path_file}{$photoData[n]->name}"></param>
                                        <embed src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photoData[n]->path_file}{$photoData[n]->name}" width="68" height="50" ></embed>
                                    </object>
                                     <span  style="float:right; clear:none;">
                                        <img id="img{$photoData[n]->pk_photo}" class="draggable2" style="width:16px;height:16px;"
                                         src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif" ondblclick="return false;"
                                         style="{cssimagescale resolution=67 photo=$photoData[n]}"
                                         class="draggable2" id="img{$photoData[n]->pk_photo}"  border="0"
                                         de:pk_photo="{$photoData[n]->pk_photo}"
                                         value="f{$indi}-{$photoData[n]->pk_photo}"
                                         name="{$photoData[n]->name}"
                                         de:mas="{$photoData[n]->name}"
                                         de:type_img="{$photoData[n]->type_img}"
                                         de:url="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoData[n]->path_file}"
                                         de:path="{$photoData[n]->path_file}"
                                         de:dimensions="{$photoData[n]->width} x {$photoData[n]->height} (px)"
                                         de:peso="{$photoData[n]->size}"
                                         de:created="{$photoData[n]->created}"
                                         de:description="{$photoData[n]->description|escape:"html"}"
                                         de:tags="{$photoData[n]->metadata}"
                                         de:footer="{$otherPhotos[n][2]|escape:"html"}" />
                                     </span>
                                 </a>
                             {else}
                                 <a class="album" title="{t}Show image{/t}"
                                    onClick="show_image('img{$photoData[n]->pk_photo}', 'f{$indi}-{$photoData[n]->pk_photo}')">
                                     <img ondblclick="define_crop(this);"
                                         style="{cssimagescale resolution=67 photo=$photoData[n]}"
                                         src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoData[n]->path_file}{$photoData[n]->name}"
                                         class="draggable2" id="img{$photoData[n]->pk_photo}"  border="0"
                                         de:pk_photo="{$photoData[n]->pk_photo}"
                                         value="f{$indi}-{$photoData[n]->pk_photo}"
                                         name="{$photoData[n]->name}"
                                         de:mas="{$photoData[n]->name}"
                                         de:path="{$photoData[n]->path_file}"
                                         de:dimensions="{$photoData[n]->width} x {$photoData[n]->height} (px)"
                                         de:peso="{$photoData[n]->size}"
                                         de:created="{$photoData[n]->created}"
                                         de:description="{$photoData[n]->description|escape:"html"}"
                                         de:tags="{$photoData[n]->metadata}"
                                         de:footer="{$otherPhotos[n][2]|escape:"html"}" />
                                 </a>
                             {/if}
                         </li>
                     {assign var=indi value=$indi+1}
                     {/section}
                {/if}
            </ul>
        </div>
    </td>
</tr>

<tr>
    <td style="height:60px;" colspan="3">
        <div id="album_msg" style="display:none;"></div>
    </td>
</tr>

<tr>
    <td></td><td></td>
    <td  align="right" rowspan="2">
        <div style="cursor:pointer; border:1px double #ccc; background-color:#EEE; padding:7px;">
                <strong>{t}Available images{/t}</strong>
        </div>
        <div id="photos_container" class="photos" style="border:1px solid #ccc;  padding:7px;">
            <table>
                <tr>
                    <td align="left">
                        <div class="cajaBusqueda">
                            <input id="stringImageSearch" name="stringImageSearch" type="text"
                               onkeypress="onImageKeyEnter(event, $('category_imag').options[$('category_imag').selectedIndex].value,encodeURIComponent($('stringImageSearch').value),1);"
                               onclick="this.select();" value="{t}Search images by title...{/t}"/>
                        </div>
                    </td>
                    <td align="right">
                        <select id="category_imag" name="category_imag" class="required" onChange="getGalleryImages('list_by_category',this.options[this.selectedIndex].value,'',1);">
                            <option value="0">GLOBAL</option>
                                {section name=as loop=$allcategorys}
                                    <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                                    {section name=su loop=$subcat[as]}
                                            <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                    {/section}
                                {/section}
                        </select>
                    </td>
                </tr>
            </table>
            <div id="photos" class="photos" style="height:440px; border:0px double #333333; margin:5px; overflow:auto;"></div>
        </div>
    </td>
</tr>
<tr>
    <td colspan="2"  style="vertical-align:top;" >
        <div id="portada" style="display:block;">
            <table style="width:95%; display:block; border-bottom:1px solid #ccc; background:#eee; padding:10px;">
                <tr>
                    <td>
                        <h2>{t}Image for frontpage:{/t}</h2>
                        <td  align='center'>
                            <a style="cursor:pointer;"  onclick="javascript:recuperar_eliminar('img');">
                                <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png"
                                     id="remove_img" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" />
                            </a>
                        </td>
                    </td>
                </tr>
                <input type="hidden" id="img_des" value="" size="60">
                <tr>
                    <td align='center'>
                        <div id="droppable_div1">
                            <img src="{$params.IMAGE_DIR}default_img.jpg"
                                 id="image_view" name="imag_view" width="300" border="0" />
                        </div>
                    </td>
                    <td colspan="2" style="text-align:left;white-space:normal;">
                        <div id="informa" style="text-align:left;overflow:auto;width:260px;">
                            <b>{t}File:{/t} default_img.jpg</b> <br>
                            <b>{t}Dimensions:{/t}</b> 300 x 208 (px)<br>
                            <b>{t}Weight:{/t}</b> 4.48 Kb<br>
                            <b>{t}Created:{/t}</b> 11/06/2008<br>
                            <b>{t}Description:{/t}</b>  {t}Default image:{/t}<br>
                            <b>{t}Tags:{/t}</b> {t}Image{/t}<br>
                        </div>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <label for="title">{t}Footer image:{/t}</label>
                        <input type="text" id="img_footer" name="img_footer" title="Imagen" value="" size="50" />
                    </td>
                </tr>
            </table>   
        </div>
        <input type="hidden" id="ordenAlbum" name="ordenAlbum" value="" />
    </td>
</tr>

<tr>
    <td colspan="3">
        <label>{t}Cut the image that is in frontpage view. ({$crop_width}x{$crop_height} px){/t} </label>
        <div id="testWrap">
            <img src="{$params.IMAGE_DIR}default_img.jpg" alt={t}"test image"{/t} 
                 id="testImage"  width="300" />
        </div>
        <div id="previewArea">
            {if !empty($album->cover)}
                <img id="crop_img" src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$album->cover}"
                     alt={t}"Frontpage image"{/t}  style="maxWidth:600px;maxHeight:400px;" />
            {/if}
        </div>
        <div id="results">
            <input type="hidden" name="x1" id="x1" value=""/>          
            <input type="hidden" name="y1" id="y1" value=""/>

            <input type="hidden" name="width" id="width" value="" />
            <input type="hidden" name="height" id="height" value=""/>

            <input type="hidden" name="cropWidth" id="cropWidth" value="{$crop_width}" />
            <input type="hidden" name="cropHeight" id="cropHeight" value="{$crop_height}"/>

            <input type="hidden" name="path_img" id="path_img" value=""/>
            <input type="hidden" name="name_img" id="name_img" value=""/>
        </div>
    </td>
</tr>


<script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
<script type="text/javascript">

    document.observe('dom:loaded', function() {
        getGalleryImages('listByCategory','{$category}','','1');
    });
    tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
    OpenNeMas.tinyMceConfig.simple.elements = "summary";
    tinyMCE.init( OpenNeMas.tinyMceConfig.simple );

    album_make_mov();

    
</script>
 
