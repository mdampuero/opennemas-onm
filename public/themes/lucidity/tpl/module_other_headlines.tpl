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
                {assign var=iteration value='0'}
                {foreach key=k item=v from=$categories_data name="other_headlines"} 
                    {if !empty($titulares_cat[$k])}
                        {assign var=is_last value=$iteration%2}
                        {assign var=last_category value=$smarty.foreach.other_headlines.last}
                        {include file="widget_more_news.tpl" category_hdata="$v" index="$k" last=$is_last last_category=$last_category}
                        {assign var=iteration value=$iteration+1}
                    {/if}
                {/foreach}

        </div>
    </div>
    <div class="span-8 last more-promotions-from-diary">
            {include file="widget_other_info.tpl"}
            <hr class="new-separator"/>
            {include file="ads/widget_ad_lateral.tpl"}
    </div>
</div>

