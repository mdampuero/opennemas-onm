<contents>
    {foreach $articles as $article}
        <content id="{$article->id}" published="{format_date date=$article->starttime type='custom' format="yyyy-MM-dd HH:mm:ss"}">
            {$app.instance->getBaseUrl(true)}/ws/agency/newsml/{$article->id}.xml
        </content>
    {/foreach}
</contents>
