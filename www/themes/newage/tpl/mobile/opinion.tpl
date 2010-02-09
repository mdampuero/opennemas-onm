{include file="mobile/header.tpl"}

<div id="opinion">
    <div class="opinion principal">                
        <div class="titular">
            <a href="/mobile{$opinion->permalink}">{$opinion->title|clearslash}</a>                        
        </div>
        <br class="clearer" />        
        
        <div class="info">
            <div class="fecha">{humandate article=$opinion created=$opinion->created updated=$opinion->updated}</div>
        </div>                
        
         <div class="entradilla">
            {if $opinion->type_opinion != 1}
            <div class="author">
                <div>
                    <img src="/media/images/{$photo->path_img}" alt="{$author_name}" height="100"/>
                </div>
                
                <div>
                    {$author_name}
                </div>
                
                <div>
                    <span class="condition">{$condition}</span>
                </div>
            </div>
            {/if}
            
            <p>
                {$opinion->body|clearslash|striphtml:'<p><br><strong><em><b><i>'}
            </p>
        </div>
         
        <br class="clearer" />
    </div>
</div>

{include file="mobile/footer.tpl"}