{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="nw-big">
    {if !empty($item->img1_path)}
         <img  class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer}" title="{$item->img_footer}" />
    {/if}

    {if $category_name eq 'home'}
        <div class="nw-category-name {$item->category_name}">{$item->category_title|upper} <span>&nbsp;</span></div>
    {/if}
    <h3 class="nw-title-head"><a href="{$item->permalink}" title="{$item->title}">{$item->title}</a></h3>
    <div class="nw-subtitle">{$item->summary}</div>

    {if !empty($item->related_contents)}
        {assign var='relacionadas' value=$item->related_contents}
        <div class="more-resources">
            <ul>
                {section name=r loop=$relacionadas}
                    {if $relacionadas[r]->pk_article neq  $item->pk_article}
                       {renderTypeRelated content=$relacionadas[r]}
                    {/if}
                {/section}
            </ul>
        </div>
    {/if}
</div>

 