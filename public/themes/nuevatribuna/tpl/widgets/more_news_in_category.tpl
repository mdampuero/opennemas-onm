{if !empty($other_news)}
<div class="morenews-in-category">
    <div class="clearfix">
        <h3 class="title">Más noticias en {$actual_category_title|default:'Portada'}</h3>
        {section name="morenews" loop=3}
            <div class="onm-new-little {$cssclass}">
                    <h3 class="onm-new-title"><a href="{$other_news[$smarty.section.morenews.iteration]->permalink|clearslash}" title="{$other_news[$smarty.section.morenews.iteration]->title|clearslash|clean_for_html_attributes}">{$other_news[$smarty.section.morenews.iteration]->title|clearslash|escape}</a></h3>
            </div>
         {/section}
    </div>
</div>
{/if}