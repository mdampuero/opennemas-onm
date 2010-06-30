{*
    OpenNeMas project
    @theme      Lucidity
*}

     <div class="nw-just-image  span-4">
        <div class="nw-title  span-4"><a href="{$content->permalink|escape:'html'}" title="{$content->title|escape:'html'}">{$content->title}</a></div>
        <div class="nw-subtitle  span-4"><div>{$content->subtitle}</div></div>
        <img class="nw-image span-4" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$content->img1_path}" alt="{$content->img_footer|escape:'html'}" title="{$content->img_footer|escape:'html'}" />
    </div>
 