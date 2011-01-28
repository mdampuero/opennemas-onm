{*
    OpenNeMas project
    @theme      Lucidity
*}

<hr class="new-separator" />
<div class="span-24">
    <div class="layout-column span-16">
        <div class="more-news">
            <h4>Más noticias</h4>
            <hr class="more-news-separator" />
                {assign var=iteration value='0'}
                {foreach key=k item=v from=$categories_data name="other_headlines"} 
                    {if !empty($titulares_cat[$k])}
                        {assign var=is_last value=$iteration%2}
                        {assign var=last_category value=$smarty.foreach.other_headlines.last}
                        {include file="internal_widgets/partials/_more-news-for-section.tpl" category_hdata="$v" index="$k" last=$is_last last_category=$last_category}
                        {assign var=iteration value=$iteration+1}
                    {/if}
                {/foreach}

        </div>
    </div>
    <div class="span-8 last more-promotions-from-diary">
            espacio por definir
            <hr class="new-separator"/>
            {include file="widgets/facebook_stream_box.tpl"}
            <hr class="new-separator" />
            {include file="widgets/last_day-last_3days-last_week.tpl"}
    </div>
</div>

