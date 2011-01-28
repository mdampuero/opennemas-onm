{if !empty($relationed)}
<div class="related-contents clearfix">
    <div class="title">Noticias relacionadas:</div>
        <ul>
        {section name=r loop=$relationed}
            {if $relationed[r]->pk_article neq  $article->pk_article}
            <li>{renderTypeRelated content=$relationed[r]}</li>
            {/if}
        {/section}
        </ul>                                                                          
</div>
{/if}