{include file="modulo_head.tpl"}
<body>
{insert name="intersticial" type="150"}
<div class="global_metacontainer">
    <div class="marco_metacontainer">
        <div class="metacontainer">	
            {include file="modulo_separadorbanners1.tpl"}
            {include file="modulo_header.tpl"}            
            <div class="container">
                <div class="containerColumnas12Noticia">                    
                    <div class="column1Noticia">                        
                        {assign var="beforeAdv" value='<div class="separadorHorizontal"></div><div class="textoBannerPublicidad">publicidad</div>'}
                        
                        {include file="article_cnoticia.tpl"}
                        
                        {* Publicidad: banner posición 101 "Banner noticia interior" *}
                        {*renderbanner banner=$banner101 photo=$photo101 cssclass="banner486x60"
                        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' *}
                        {insert name="renderbanner" type=101 cssclass="banner486x60"
                            beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'}
                        
                        {if $article->with_comment eq '1'}
                            {include file="modulo_copina.tpl"}
                        {/if}
                    </div>
                    {include file="article_column2.tpl"}
                </div>                
                {* <div class="separadorHorizontal"></div>                 
                {include file="modulo_separadorbanners3.tpl"} *}
                <div class="separadorHorizontal"></div>                
            </div>
            {include file="modulo_footer.tpl"}
        </div>
    </div>
</div>
{include file="modulo_analytics.tpl"}
</body>
</html>