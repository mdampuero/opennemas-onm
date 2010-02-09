{include file="mobile/header.tpl"}

    <div class="noticia principal">
        {if isset($photo)}
            <img src="/media/images/{$photo}" alt="" {imageattrs image=$photos}/>
        {/if}
        
        <div class="titular">
            <div class="fecha">{humandate article=$article created=$article->created updated=$article->changed}</div>
            
            <div class="article-title">
                {$article->title|clearslash}                
                <span class="article-seccion">[{$ccm->get_title($section)}]</span>
            </div>
        </div>
        
        <div class="entradilla">
            {$article->summary|clearslash|striphtml:'<p><br><strong><em><b><i>'}
        </div>
        
        <div class="article-body">
            {$article->body|clearslash|striphtml:'<p><br><strong><em><b><i>'}
        </div>
    </div>	

{include file="mobile/footer.tpl"}