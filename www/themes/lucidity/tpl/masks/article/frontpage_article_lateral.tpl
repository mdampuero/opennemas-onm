{*
    OpenNeMas project
    @theme      Lucidity
*}
<div class="nw-big-img-lateral">
 {if !empty($content->img1_path)}
    <img class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$content->img1_path}" alt="{$content->img_footer|escape:'html'}" title="{$content->img_footer|escape:'html'}" />
 {/if}
    <div class="nw-category-name {$content->category_name}">{$content->category_title|upper} <span>&nbsp;</span> </div>
   <h3 class="nw-title"><a href="{* TODO: implement $content->getPermalink() method *}" title="{$content->title|escape:'html'}">{$content->title}</a></h3>
   <p class="nw-subtitle">{$content->subtitle}</p>

</div>