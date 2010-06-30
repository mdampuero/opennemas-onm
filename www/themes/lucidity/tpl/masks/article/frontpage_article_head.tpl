{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="nw-big">
    {if !empty($content->img1_path)}
         <img  class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$content->img1_path}" alt="{$content->img_footer}" title="{$content->img_footer}" />
    {/if}

    {if $category_name eq 'home'}
        <div class="nw-category-name {$content->category_name}">{$content->category_title|upper} <span>&nbsp;</span></div>
    {/if}
    <h3 class="nw-title-head"><a href="{* TODO implement getPermalink *}" title="{$content->title}">{$content->title}</a></h3>
    <div class="nw-subtitle">{$content->summary}</div>
</div>

 