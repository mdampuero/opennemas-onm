{if (!$last)}
<div class="span-16"><!-- inicio headlines -->
{/if}

<div class="more-news-section span-8 {if ($last)}last{/if}">
{$last}    <ul class="more-news-section-sectionlist clearfix">
        <li class="first"><a href="/seccion/{$category_hdata.name}/" title="Sección:{$category_hdata.title|clean_for_html_attributes}"><strong>{$category_hdata.title|clearslash}</strong>:</a></li>
         {if !empty($category_hdata.subcategories)}
             {foreach name=s key=c item=subcat from=$category_hdata.subcategories}
             {if  $subcat.internal_category eq 1}
                {if $smarty.foreach.s.last}
                    <li class="last"><a href="/seccion/{$category_hdata.name}/{$c}/" title="Seccion:{$subcat.title|clearslash|clean_for_html_attributes}">{$subcat.title|clearslash}</a></li>
                {else}
                    <li><a href="/seccion/{$category_hdata.name}/{$c}/" title="Seccion:{$subcat.title|clearslash|clean_for_html_attributes}">{$subcat.title|clearslash}</a></li>
                {/if}
             {/if}
             {/foreach}
        {/if}
    </ul>
    <ul class="more-news-section-links">
        {foreach name=t key=c item=sub from=$titulares_cat[$index]}

            {if $smarty.foreach.t.first}
                <li class="first">
                    <a href="{$smarty.const.SITE_URL}{generate_uri
                                                                content_type='article'
                                                                id=$sub.id
                                                                date=$sub.created
                                                                category_name=$category_hdata.name
                                                                title=$sub.title}" title="{$sub.title|clearslash|escape:"html"|clean_for_html_attributes}">
                                    {$sub.title|clearslash}</a></li>
            {elseif $smarty.foreach.t.last}
                <li class="last"><a href="{$smarty.const.SITE_URL}{generate_uri
                                                                content_type='article'
                                                                id=$sub.id
                                                                date=$sub.created
                                                                category_name=$category_hdata.name
                                                                title=$sub.title}" title="{$sub.title|clearslash|escape:"html"|clean_for_html_attributes}">{$sub.title|clearslash}</a></li>
            {else}
                <li>
                    <a href="{$smarty.const.SITE_URL}{generate_uri
                                                        content_type='article' id=$sub.id date=$sub.created category_name=$category_hdata.name title=$sub.title}"
                       title="{$sub.title|clearslash|escape:"html"|clean_for_html_attributes}">{$sub.title|clearslash}</a>
                </li>
            {/if}
        {/foreach}
    </ul>
</div>
{if ($last || $last_category || $smarty.foreach.other_headlines.last)}
</div><!-- fin headlines -->
{/if}
