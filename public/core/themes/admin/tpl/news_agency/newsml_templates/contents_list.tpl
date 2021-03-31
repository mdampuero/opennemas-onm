<contents>
    {foreach $articles as $article}
        <content id="{$article->id}">
            {$smarty.const.SITE_URL}/ws/agency/newsml/{$article->id}.xml
        </content>
    {/foreach}
</contents>
