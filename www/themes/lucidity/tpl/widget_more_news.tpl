{*
    OpenNeMas project
    @theme      Lucidity
*}
    
<div class="more-news-section">
    <ul class="more-news-section-sectionlist clearfix">
        <li class="first"><strong><a href="/seccion/{$category_hdata.name}/" title="Sección:{$category_hdata.title}">{$category_hdata.title}</strong>:</a></li>
         {if !empty($category_hdata.subcategories)}
             {foreach name=s key=c item=subcat from=$category_hdata.subcategories}
                {if $smarty.foreach.s.last}
                    <li class="last"><a href="/seccion/{$category_hdata.name}/{$c}/" title="Seccion:{$subcat}">{$subcat}</a></li>
                {else}
                    <li ><a href="/seccion/{$category_hdata.name}/{$c}/" title="Seccion:{$subcat}">{$subcat}</a></li>
                {/if}
             {/foreach}
        {/if}
    </ul>
    <ul class="more-news-section-links">
        {foreach name=t key=c item=sub from=$titulares_cat[$index] max=6}
            {if $smarty.foreach.t.first}
                <li class="first"><a href="{$sub.permalink}" title="{$sub.title}">{$sub.title}</a></li>
            {elseif $smarty.foreach.t.last}
                <li class="last"><a href="{$sub.permalink}" title="{$sub.title}">{$sub.title}</a></li>
            {else}
                <li><a href="/seccion/{$sub.permalink}" title="{$sub.title}">{$sub.title}</a></li>
            {/if}
        {/foreach}
    </ul>
</div>

 