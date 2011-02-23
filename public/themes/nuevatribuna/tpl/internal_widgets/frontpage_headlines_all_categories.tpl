{*
    OpenNeMas project
    @theme      Lucidity
*}

<hr class="new-separator" />
<div class="span-24 last">

    <div class="span-16 clearfix">
        <div class="more-news span-16">
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

    <div class="more-promotions-from-diary span-8 last">
            ESPACIO POR DEFINIR
            <hr class="new-separator"/>
            {include file="widgets/facebook_stream_box.tpl"}

    </div>

</div>
