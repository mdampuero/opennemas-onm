<div class="in-big-title">
                    
    <h1>
    {if $author_id eq 1}
        Opiniones de <a class="CNombreAuthorLink" href="/opinions_autor/1/Editorial.html">Editorial</a>
    {elseif $author_id eq 2}
        Opiniones del <a class="CNombreAuthorLink" href="/opinions_autor/2/Director.html">Director</a>
    {else}
        Opiniones de <a class="CNombreAuthorLink" href="/opinions_autor/{$opinions[0].pk_author}/{$opinions[0].name|clearslash}.html">{$opinions[0].name}</a>
    {/if}
    </h1>
    <p class="in-subtitle"></p>
   
</div><!-- fin lastest-news -->
                
<div class="opinion-listing-for-author">
    {section name=ac loop=$opinions}
        <div class="ListadoTitlesAuthor">
            <h3 class='title-opinion-on-list'><a href="{$opinions[ac].permalink}">{$opinions[ac].title|clearslash}</a></h3>
            <div class="date-opinion-on-list">
                 {articledate updated=$opinions[ac].changed}
            </div>
            <div class="CtextoAuthorlist">
                {$opinions[ac].body|clearslash|truncate:250|strip_tags}
                <p class='moretoread'><a href="{$opinions[ac].permalink}"> Sigue leyendo &raquo; </a></p>
            </div>
        </div>

    {/section}
    <p align="center">{$pagination_list->links}</p>
</div>
