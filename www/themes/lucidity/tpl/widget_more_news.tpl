{*
    OpenNeMas project
    @theme      Lucidity
*}
    
<div class="more-news-section">
    <ul class="more-news-section-sectionlist clearfix">
        <li class="first"><strong><a href="/seccion/{$category_data.name}/" title="Sección:{$category_data.title}">{$category_data.title}</strong>:</a></li>
         {if !empty($category_data.subcategories)}
             {foreach name=s key=k item=sub from=$category_data.subcategories}
                {if $smarty.foreach.s.last}
                    <li class="last"><a href="/seccion/{$category_data.name}/{$k}/" title="Seccion:{$sub}">{$sub}</a></li>
                {else}
                    <li ><a href="/seccion/{$category_data.name}/{$k}/" title="Seccion:{$sub}">{$sub}</a></li>
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

 