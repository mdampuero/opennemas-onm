 {*
    OpenNeMas project

    @theme      Lucidity
*}
<div class='widget-last-opinions-with-face span-8 last clearfix'>
    <div>
        {if $other_opinions}
            {if $opinion->type_opinion eq 1}
                <div class='title'>Otros art&iacute;culos de la Editorial</div>
            {elseif $opinion->type_opinion eq 2}
                <div class='title'>Otros art&iacute;culos del Director</div>
            {else}
                <div class='title'>Otros art&iacute;culos de {$opinion->name|clearslash}</div>
            {/if}
                
            <div class='content'>
                <ul>
                    {section name=a loop=$other_opinions}
                         <li><a href="{$smarty.const.SITE_URL}{generate_uri   content_type="opinion"
                                                            id=$other_opinions[a]->id
                                                            date=$other_opinions[a]->created
                                                            title=$other_opinions[a]->title
                                                            category_name=$other_opinions[a]->author_name_slug}">{$other_opinions[a]->title|clearslash}</a></li>
                    {/section}
                </ul>

            </div>
        {/if}
    </div>
</div>
