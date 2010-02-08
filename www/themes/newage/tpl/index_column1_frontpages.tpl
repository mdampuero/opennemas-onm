<div class="column1big" >
    {* renderwidgets placeholder='widgetholder_0_0' category=$section *}
    
    {* Check if 1st article is principal *}
    {if !empty($destaca)}
        {if ($destaca[0]->columns neq '2' && $category_name neq 'home') || ($destaca[0]->home_columns neq '2' && $category_name eq 'home')}    
            {* renderitems items=$destaca filter="\$i==0" tpl="container_article_destacado.tpl" *}
            {renderplaceholder video=$video_destacada items=$destaca  relationed=$relationed tpl='container_article_destacado_frontpages.tpl' placeholder="placeholder_0_0"}
        {/if}
    {/if}
    
    {* pintar as que quedan no placeholder de destacada *}
    {if !isset($smarty.request.page) || ($smarty.request.page == 0)}
        {renderplaceholder items=$articles_home tpl='container_article_col1_frontpages.tpl' placeholder="placeholder_0_0"}
    {/if}
    
    {* renderwidgets placeholder='widgetholder_0_1' category=$section *}
    
    {if isset($smarty.request.page) && $smarty.request.page>0}
        {* renderitems items=$column filter="\$i%2==0 && \$i<11" tpl="container_article_col1.tpl"}
        {renderitems items=$column filter="\$i>=12 " tpl="container_article_col1.tpl" *}
        
        {* renderitems items=$column filter="\$i%2==0 && \$i<11" tpl="container_article_col1_frontpages.tpl"}
        {renderitems items=$column filter="\$i>=12 " tpl="container_article_col1_frontpages.tpl" *}
        {renderitems items=$column filter="true" tpl="container_article_col1_frontpages.tpl"}
    {else}
        {renderplaceholder items=$column tpl='container_article_col1_frontpages.tpl' placeholder="placeholder_0_1"}
    {/if}

    {if !empty($paginacion)}
        <p align='center'>
        <a title="Portada" href="/seccion/{$actual_category}/"> Portada </a> |
    	{$paginacion}
        </p>
    {/if}
</div>