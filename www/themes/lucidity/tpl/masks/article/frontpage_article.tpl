{*
    OpenNeMas project

    @theme      Lucidity
*}


<div class="nw-big">
     {if !empty($content->img1_path)}
         <img class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$content->img1_path}" alt="{$content->img_footer}" title="{$content->img_footer}"/>
     {/if}
     {if $category_name eq 'home'}
        <div class="nw-category-name {$content->category_name}">{$content->category_title|upper} <span>&nbsp;</span></div>
     {/if}
    <h3 class="nw-title"><a href="{$content->permalink}" title="{$content->title}">{$content->title}</a></h3>
    {*if !empty($content->agency)}<h5>{$content->agency}</h5>{/if*}
    <div class="nw-subtitle">{$content->summary} en LUCIDITY</div>

    {if !empty($content->related_contents)}
        {assign var='relacionadas' value=$content->related_contents}
        <div class="more-resources">
            <ul>
                {section name=r loop=$relacionadas}
                    {if $relacionadas[r]->pk_article neq  $content->pk_article}
                       {renderTypeRelated content=$relacionadas[r]}
                    {/if}
                {/section}
            </ul>
        </div>
    {/if}
</div>

<hr class="new-separator"/>

