<?xml version="1.0" encoding="utf-8"?>
<contents>
    {foreach $articles as $article}
        <content id="{$article->id}">
            <text>
                {$smarty.const.SITE_URL}ws/articles/newsml/{$article->id}.xml
            </text>
        </content>
    {/foreach}
</contents>