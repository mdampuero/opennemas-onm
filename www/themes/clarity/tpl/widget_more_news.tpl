{*
    OpenNeMas project
    @theme      Clarity
*}
    
<div class="more-news-section">
    <ul class="more-news-section-sectionlist clearfix">
        <li class="first"><a href="/seccion/{$category_hdata.name}/" title="Sección:{$category_hdata.title}"><strong>{$category_hdata.title|clearslash}</strong>:</a></li>
         {if !empty($category_hdata.subcategories)}
             {foreach name=s key=c item=subcat from=$category_hdata.subcategories}
                {if $smarty.foreach.s.last}
                    <li class="last"><a href="/seccion/{$category_hdata.name}/{$c}/" title="Seccion:{$subcat}">{$subcat|clearslash}</a></li>
                {else}
                    <li ><a href="/seccion/{$category_hdata.name}/{$c}/" title="Seccion:{$subcat}">{$subcat|clearslash}</a></li>
                {/if}
             {/foreach}
        {/if}
    </ul>
    <ul class="more-news-section-links">
        {foreach name=t key=c item=sub from=$titulares_cat[$index]}
            {if $smarty.foreach.t.first}
                <li class="first"><a href="{$sub.permalink}" title="{$sub.title|clearslash|escape:"html"}">{$sub.title|clearslash}</a></li>
            {elseif $smarty.foreach.t.last}
                <li class="last"><a href="{$sub.permalink}" title="{$sub.title|clearslash|escape:"html"}">{$sub.title|clearslash}</a></li>
            {else}
                <li><a href="/seccion/{$sub.permalink}" title="{$sub.title|clearslash|escape:"html"}">{$sub.title|clearslash}</a></li>
            {/if}
        {/foreach}
    </ul>
</div>

 