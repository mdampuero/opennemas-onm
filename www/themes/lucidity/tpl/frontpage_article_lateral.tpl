{*
    OpenNeMas project
    @theme      Lucidity
*}
<div class="nw-big-img-lateral">
 {if !empty($item->img1_path)}
    <img class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer|clearslash|escape:'html'}" title="{$item->img_footer|clearslash|escape:'html'}" />
 {/if}
    <div class="nw-category-name {$item->category_name}">{$item->category_title|upper|clearslash}  </div>
   <h3 class="nw-title"><a href="{$item->permalink|clearslash|escape:'html'}" title="{$item->title|clearslash|escape:'html'}">{$item->title|clearslash}</a></h3>
   <p class="nw-subtitle">{$item->subtitle|clearslash}</p>

</div>