<div class="homepage-opinion-editorial span-16 last">

    {if ($smarty.request.action eq "list_opinions") && (!empty($editorial))}

        <div class="opinion-listing-editorial span-16 last">
            <h3>Editorial</h3>
            <div>
                {section name="ac" loop=$editorial}
                    <a class="post-title" href="{$smarty.const.SITE_URL}{generate_uri   content_type="opinion"
                                                                    id=$editorial[ac]->id
                                                                    date=$editorial[ac]->created
                                                                    title=$editorial[ac]->title
                                                                    category_name='editorial'}" >{$editorial[ac]->title|clearslash}</a>
                    {$editorial[ac]->body|clearslash|strip_tags|truncate:500|purify_html}
                    <a class="opinion-see-more" href="{$smarty.const.SITE_URL}{generate_uri content_type="opinion_author_frontpage" title="editorial" id=1}">[Ver más...]</a>
                {/section}
            </div>
        </div>

    <hr>
    {/if}

    {if ($smarty.request.action eq "list_opinions") && (!empty($director))}
        <div class="opinion-listing-director span-16 last">
            <h3>Opinión del director</h3>
            <div>
                <a class="post-title" href="{$smarty.const.SITE_URL}{generate_uri   content_type="opinion"
                                                                    id=$director->id
                                                                    date=$director->created
                                                                    title=$director->title
                                                                    category_name='editorial'}" title="{$director->title|clearslash}">{$director->title|clearslash}</a>
                <div>
                    <strong>{$director->created|date_format:"%d de %B de %y"}</strong> -
                    {$director->body|clearslash|strip_tags|truncate:350|purify_html}
                    <a class="opinion-see-more" href="{$smarty.const.SITE_URL}{generate_uri content_type="opinion_author_frontpage" title="director" id=2}">[Ver más...]</a>
                </div>
            </div>
        </div>
        <hr>

    {/if}

    <div class="opinion-listing-colaborators last">
        <h3>Últimas opiniones </h3>
        <div class="span-16 last clearfix">
            {section name=ac loop=$opinions start=0}

            <div id="{$opinions[ac]->id}" style="padding:5px 10px;" class="opinion clearfix">

                {if $opinions[ac]->path_img}
                    <div class="avatar-author span-2">
                        <a href="{$smarty.const.SITE_URL}{generate_uri content_type="opinion_author_frontpage" title=$opinions[ac]->name id=$opinions[ac]->pk_author}">
                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$opinions[ac]->path_img}" alt="{$opinions[ac]->name}" height="67" />
                        </a>
                    </div>
                {/if}
                <div class="opinion-listing-colaborators-post {if $opinions[ac]->path_img}span-14{else}span-16{/if} last clearfix">
                    <h4 class="author-name"><a href="{$smarty.const.SITE_URL}{generate_uri content_type="opinion_author_frontpage" title=$opinions[ac]->name id=$opinions[ac]->fk_author}">{$opinions[ac]->name}</a></h4>
                    <span class="post-date">{$opinions[ac]->created|date_format:"%d.%m.%Y"}</span> | 
                    <strong><a class="post-name" href="{$smarty.const.SITE_URL}{generate_uri   content_type="opinion"
                                                                id=$opinions[ac]->id
                                                                date=$opinions[ac]->created
                                                                title=$opinions[ac]->title
                                                                category_name=$opinions[ac]->author_name_slug}">{$opinions[ac]->title|clearslash}</a></strong>
                    
                </div>
            </div>

            {/section}
            <div class="pagination center clearfix">{$pagination->links}</div>
            <br>
        </div>

    </div>
</div>
