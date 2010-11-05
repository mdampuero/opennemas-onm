{*
    OpenNeMas project
    @theme      Lucidity
*}

<div class="nw-just-image  span-4">
   <div class="nw-title  span-4"><div><a href="{$item->permalink|clearslash|escape:'html'}" title="{$item->title|clearslash|escape:'html'}">{$item->title|clearslash}</a></div> </div>
   <div class="nw-subtitle  span-4"><div>{$item->category_title|clearslash} </div></div>
   <a href="{$item->permalink|clearslash|escape:'html'}" title="{$item->title|clearslash|escape:'html'}"><img class="nw-image span-4" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer|clearslash|escape:'html'}" title="{$item->img_footer|clearslash|escape:'html'}" /></a>
</div>
