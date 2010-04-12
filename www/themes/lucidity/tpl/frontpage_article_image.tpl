{*
    OpenNeMas project
    @theme      Lucidity
*}

     <div class="nw-just-image  span-4">
        <div class="nw-title  span-4"><a href="{$item->permalink|clearslash|escape:'html'}" title="{$item->title|clearslash|escape:'html'}">{$item->title|clearslash}</a></div>
        <div class="nw-subtitle  span-4"><div>{$item->category_title|clearslash} </div></div>
        <img style="width:155px;" class="nw-image span-4" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer|clearslash|escape:'html'}" title="{$item->img_footer|clearslash|escape:'html'}" />
    </div>
 