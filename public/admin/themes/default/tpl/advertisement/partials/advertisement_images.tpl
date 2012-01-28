<h2>{t}Multimedia for this ad:{/t}</h2>
<table id="advertisement-images" style="{if isset($advertisement) && $advertisement->with_script == 1} display:none;{else}display:block;{/if}">
	<tr>
		<td style="width:590px;">
            <div id="related-images" class="resource-container">
                <div class="droppable-image-position droppable-position">
                    <div>
                        <a class="delete-button" onclick="javascript:recuperar_eliminar('img1');">
                            <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_img1" alt="Eliminar" title="Eliminar" />
                        </a>
                        <div class="clearfix">
                            <div class="thumbnail article-resource-image">
                                {if isset($photo1) && $photo1->name}
                                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}" id="frontpage_image" name="{$article->img1}" />
                                {elseif isset($photo1) && strtolower($photo1->type_img)=='swf'}
                                    <object id="change1"  name="{$advertisement->img|default:""}" >
                                        <param name="movie" value="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}"></param>
                                        <embed src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name|default:""}" width="300" ></embed>
                                    </object>
                                {else}
                                    <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}" name="{$advertisement->img|default:""}" id="change1" width="300" />
                                {/if}
                            </div>
                            <div id="image-information" class="article-resource-image-info">
                                <div><label>{t}File name{/t}</label>     <span class="filename">{$photo1->name|default:'default_img.jpg'}</span></div>
                                <div><label>{t}Image size{/t}</label>    <span class="image_size">{$photo1->width|default:0} x {$photo1->height|default:0}</span> (px)</div>
                                <div><label>{t}File size{/t}</label>     <span class="file_size">{$photo1->size|default:0}</span> Kb</div>
                                <div><label>{t}Creation date{/t}</label> <span class="created_time">{$photo1->created|default:""}</span></div>
                                <div><label>{t}Description{/t}</label>   <span class="description">{$photo1->description|escape:'html'}</span></div>
                                <div><label>{t}Tags{/t}</label>          <span class="tags">{$photo1->metadata|default:""}</span></div>
                            </div>
                        </div><!-- / -->
                        <div id="footer_img_portada" class="article-resource-footer">
                            <input type="hidden" name="img" value="{$advertisement->img|default:""}" />
                        </div>
                    </div><!-- / -->
                </div><!-- /frontpage-image -->
            </div><!-- /related-images -->
		</td>
		<td id="photos_container">
			<div style="cursor:pointer; border:1px double #ccc; background-color:#EEE; padding:7px;">
					<strong>{t}Available multimedia for ads{/t}</strong>
			</div>
			<div id="photos" class="photos clearfix"
                 style="border:1px solid #ccc;
                 {if isset($advertisement) && $advertisement->with_script == 1}
                     display:none;
                 {else}
                     display:block;
                 {/if}" >
			</div>
		</td>
	</tr>
</table>
<style>
    #related-images.resource-container { border:1px solid #ccc; padding:10px; }
</style>
<script type="text/javascript">
document.observe('dom:loaded', function() {
    getGalleryImages('listByCategory','2','','1','photos');
});
</script>