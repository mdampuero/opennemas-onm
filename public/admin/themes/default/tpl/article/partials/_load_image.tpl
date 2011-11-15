<div id="img_portada" style=" display:block; border-bottom:1px solid #ccc; background:#eee; padding:10px">
    <table style="width:100%;">
        <tr>
            <td>
                <label>{t}Image for frontpage:{/t}</label>
                <input type="hidden" id="input_video" name="fk_video" value="" size="70">
            <td  align='right'>
                <a style="cursor:pointer;"  onclick="javascript:recuperar_eliminar('img1');">
                    <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_img1" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" />
                </a>
            </td>
        </tr>
        <input type="hidden" id="input_img1" name="img1" title="Imagen" value="{$article->img1|default:""}" size="70"/>
        <tr>
            <td align='center'>
                <div id="droppable_div1">
                    {if isset($photo1) && $photo1->name}
                        {if strtolower($photo1->type_img)=='swf'}
                            <object id="change1"  name="{$article->img1}" >
                                <param name="movie" value="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}"></param>
                                <embed src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}" width="300" ></embed>
                            </object>
                        {else}
                            <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}" id="change1" name="{$article->img1}" border="0" width="300px" />
                        {/if}
                    {else}
                        <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_img.jpg" id="change1" name="default_img" border="0" width="300px" />
                    {/if}
                </div>
            </td>
            <td colspan="1" style="text-align:left;white-space:normal;">
                <div id="informa" style="text-align:left; width:260px;overflow:auto;">
                    <p><strong>{t}File name:{/t}</strong><br/> {$photo1->name|default:'default_img.jpg'}</p>
                    <p><strong>{t}Size:{/t}</strong><br/> {$photo1->width|default:0} x {$photo1->height|default:0} (px)</p>
                    <p><strong>{t}File size:{/t}</strong><br/> {$photo1->size|default:0} Kb</p>
                    <p><strong>{t}File creation date{/t}:</strong><br/> {$photo1->created|default:""}</p>
                    <p><strong>{t}Description:{/t}</strong><br/> {$photo1->description|default:""|clearslash|escape:'html'}</p>
                    <p><strong>Tags:</strong><br/> {$photo1->metadata|default:""}</p>
                </div>
                <div id="noimag" style="display: inline; width:100%; height:30px;"></div>
                <div id="noinfor" style="display: none; width:100%;  height:30px;"></div>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div id="footer_img_portada">
                    <label for="title">{t}Footer text for frontpage image:{/t}</label>
                    <input type="text" id="img1_footer" name="img1_footer" title="Imagen" value="{$article->img1_footer|clearslash|escape:'html'}" size="50" />
                </div>
            </td>
        </tr>
    </table>
</div>
<br/>