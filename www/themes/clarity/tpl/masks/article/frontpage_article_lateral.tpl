{*
    OpenNeMas project
    @theme      Lucidity
*}
<div class="nw-big-img-lateral">
 {if !empty($item->img1_path)}
    <img class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer|escape:'html'}" title="{$item->img_footer|escape:'html'}" />
 {/if}
    <div class="nw-category-name {$item->category_name}">{$item->category_title|upper} <span>&nbsp;</span> </div>
   <h3 class="nw-title"><a href="{$item->permalink|escape:'html'}" title="{$item->title|escape:'html'}">{$item->title}</a></h3>
   <p class="nw-subtitle">{$item->subtitle}</p>

</div>