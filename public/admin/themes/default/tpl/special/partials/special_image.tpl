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

<table style="width:100%" border="1" >
<tr>
    <td style="vertical-align:top;" >
        <div id="portada" style="display:block;">
            <table style="width:100%; display:block; border-bottom:1px solid #ccc; background:#eee; padding:10px 0;">
                <tr>
                    <td>
                        <h2>{t}Image for Special:{/t}</h2>
                    </td>
                    <td  align='center'>
                        <a style="cursor:pointer;"  onclick="javascript:recuperar_eliminar('img');">
                            <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png"
                                 id="remove_img" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" />
                        </a>
                    </td>
                </tr>
                <input type="hidden" id="img_des" value="" size="60">
                <input type="hidden" id="input_img" value="" size="60">
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
    </td>
     <td  align="right"  >
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
</table>
