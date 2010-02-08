<div class="CFooterArticle">
    <div class="CFooterArticleCel1">
        {include file="modulo_bookmarks.tpl"}
    </div>
    <div class="CFooterArticleCel2">
        {if ($smarty.request.category_name eq "opinion")}
            {insert name="rating" id=$opinion->id page="article" type="vote"}
        {else}
            {insert name="rating" id=$article->id page="article" type="vote"}
        {/if}
    </div>
<div class="CTextoNotaEnviarA">
    Nota: es posible que tengas que estar registrado y autentificado en estos servicios para poder anotar el contenido correctamente.
</div>
</div>