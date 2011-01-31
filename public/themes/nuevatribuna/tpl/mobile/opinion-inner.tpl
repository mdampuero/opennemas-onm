{extends file="mobile/mobile_layout.tpl"}


{block sname="content"}
    <div id="content">
        <div id="contentwrap">
            <div id="infoblock">
                <h2>{$opinion->title|clearslash}</h2>
                <p class="subtitle">
                    {if isset($photos)}
                    <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/media/images/{$photo->path_img}" alt="{$author_name}" height="100" align="left"/>
                    {/if}
                    <strong>{$author_name} (<span class="condition">{$condition}</span>)</strong>
                    | {humandate article=$opinion created=$opinion->created updated=$opinion->updated}
                </p>
            </div>
            <div class="post">
                {$opinion->body|clearslash}
            </div>
        </div>
    </div>
{/block}
