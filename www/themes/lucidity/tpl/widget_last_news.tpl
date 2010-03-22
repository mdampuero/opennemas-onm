{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="more-tabbed">
    <h4>Más noticias</h4>
    <hr class="more-news-little-separator" />
    <ul class="more-news-little-section-links">
        {section name=exp loop=$articles_home_express}
            {if $smarty.section.i.first}
                <li class="first">
                    <a href="{$articles_home_express[exp]->permalink}">{$articles_home_express[exp]->created|date_format:"%H:%M"}
                    {$articles_home_express[exp]->title|clearslash}</a>
                </li>
            {elseif $smarty.section.i.last}
                <li class="last">
                    <a href="{$articles_home_express[exp]->permalink}">{$articles_home_express[exp]->created|date_format:"%H:%M"}
                    {$articles_home_express[exp]->title|clearslash}</a>
                </li>
            {else}
                <li>
                     <a href="{$articles_home_express[exp]->permalink}">{$articles_home_express[exp]->created|date_format:"%H:%M"}
                    {$articles_home_express[exp]->title|clearslash}</a>
                </li>
            {/if}
        {/section}
    </ul>
</div>