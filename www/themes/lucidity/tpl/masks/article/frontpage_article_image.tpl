{*
    OpenNeMas project
    @theme      Lucidity
*}

     <div class="nw-just-image  span-4">
        <div class="nw-title  span-4"><a href="{$item->permalink|escape:'html'}" title="{$item->title|escape:'html'}">{$item->title}</a></div>
        <div class="nw-subtitle  span-4"><div>{$item->subtitle}</div></div>
        <img class="nw-image span-4" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer|escape:'html'}" title="{$item->img_footer|escape:'html'}" />
    </div>
 