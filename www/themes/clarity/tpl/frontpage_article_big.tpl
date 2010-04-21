{*
    OpenNeMas project

    @theme      Clarity

*}
<div class="nw-big">
    <div class="nw-category-name science"><span class="spacer">&nbsp;</span>Arquitectura</div>
    <div class="content-new">
        {if !empty($item->img1_path)}
            <img class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer|clearslash}" title="{$item->img_footer|clearslash}"/>
        {/if}
        <h3 class="nw-title"><a href="{$item->permalink|clearslash}" title="{$item->title|clearslash}">{$item->title|clearslash}</a></h3>
        <p class="nw-subtitle"> {$item->summary|clearslash} </p>
        {if !empty($item->related_contents)}
              {assign var='relacionadas' value=$item->related_contents}
              <div class="more-resources">
                    {section name=r loop=$relacionadas}
                        {if $relacionadas[r]->pk_article neq  $item->pk_article}
                           <li>{renderTypeRelated content=$relacionadas[r]}</li>
                        {/if}
                    {/section}
              </div>
        {/if}
    </div>
</div>
 