<div class="homepage-opinion-editorial span-16 last">
	
    {if ($smarty.request.action eq "list_opinions") && (!empty($editorial))}
    
        <div class="opinion-listing-editorial span-16 last">
            <h3>Editoriales</h3>
            <div>
                {section name="ac" loop=$editorial}
                    <a class="post-title" href="{$editorial[ac]->permalink|default:"#"}" >{$editorial[ac]->title|clearslash}</a>
                    {$editorial[ac]->body|clearslash|strip_tags|truncate:500|purify_html}
                    <a class="opinion-see-more" href="/opinions_autor/2/Editorial.html">[Ver más...]</a>
                {/section}
            </div>
        </div>
    
    <hr>
    {/if}

    {if ($smarty.request.action eq "list_opinions") && (!empty($director))}
        <div class="opinion-listing-director span-16 last">
            <h3>Opinión del director</h3>
            <div>
                <a class="post-title" href="{$director->permalink}" title="{$director->title|clearslash}">{$director->title|clearslash}</a>
                <div>
                    <strong>{$director->created|date_format:"%d de %B de %y"}</strong> -
                    {$director->body|clearslash|strip_tags|truncate:350|purify_html}
                    <a class="opinion-see-more" href="/opinions_autor/2/Director.html">[Ver más...]</a>
                </div>
            </div>
        </div>
        <hr>
    
    {/if}
	
    <div class="opinion-listing-colaborators  span-16 last">
        <h3>Colaboradores</h3>
        <div class="span-16 clearfix">
            {section name=ac loop=$opinions start=0}

                {if $smarty.section.ac.index % 2 == 0}
                    <div class="span-8">
                {else}
                    <div class="span-8 last">
                {/if}

                <div class="avatar-author span-2">
                    {if $opinions[ac].path_img}
                        <a href="/opinions_autor/{$opinions[ac].pk_author}/{$opinions[ac].name|clearslash}.html">
                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$opinions[ac].path_img}" alt="{$opinions[ac].name}" height="67" />
                        </a>
                    {else}
                        <img src="{$params.IMAGE_DIR}opinion/editorial.jpg" alt="{$opinions[ac].name}" height="67" />
                    {/if}
                </div>

                <div class="opinion-listing-colaborators-post span-5 last">
                    <h4 class="author-name"><a href="/opinions_autor/{$opinions[ac].pk_author}/{$opinions[ac].name|clearslash}.html">{$opinions[ac].name}</a></h4>
                    {*<span class="post-date">{$opinions[ac].created|date_format:"%d/%m/%y"}</span>*}
                    <a class="post-name" href="{$opinions[ac].permalink}">{$opinions[ac].title|clearslash}</a>
                </div>

                    {*if $smarty.section.ac.index % 2 == 0}

                    {/if}
                    {if $smarty.section.ac.index % 2 != 0 || $smarty.section.ac.last}

                    {/if}

                    {if $smarty.section.ac.last}

                    {/if*}
                </div>

            {/section}
        </div>

    </div>
    {if count($opinions) gt 0}			  
        <p align="center">{$pagination}</p>        
    {/if}
</div>