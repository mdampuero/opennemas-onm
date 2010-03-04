{*
    OpenNeMas project
    @theme      Lucidity
*}

<hr class="new-separator" />
<div class="span-24">
    <div class="layout-column first-column span-16">
        <div class="more-news">
            <h4>Más noticias</h4>
            <hr class="more-news-separator" />
            
                {foreach key=k item=v from=$categories_data} 
                     {if !empty($titulares_cat[$k])}
                        {include file="widget_more_news.tpl" category_data="$v" index="$k"}
                        <hr class="more-news-inner-separator" />
                    {/if}
                {/foreach}

        </div>
    </div>
    <div class="span-7 more-promotions-from-diary">
            {include file="widget_other_info.tpl"}
    </div>
</div>

