{*
    OpenNeMas project

    @theme      Clarity

*}

<div class="nw-big">
    <div class="content-new">
        {if !empty($item->img1_path)}
           <img  class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer|clearslash}" title="{$item->img_footer|clearslash}" />
        {/if}
        <h3 class="nw-title-big"><a href="{$item->permalink|clearslash}" title="{$item->title|clearslash}">
            {$item->title|clearslash}</a>
        </h3>
        <p class="nw-subtitle">{$item->summary|clearslash} </p>
    </div>
</div>
 