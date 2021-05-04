<contents>
    {foreach $articles as $article}
        <content id="{$article->id}">
            {$app.instance->getBaseUrl()}/ws/agency/newsml/{$article->id}.xml
        </content>
    {/foreach}
</contents>
