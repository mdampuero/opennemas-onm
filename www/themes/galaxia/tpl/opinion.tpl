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
                    {if $action eq 'authors'}                     
                  	   {include file="opinion_index_content.tpl"}
                       
                        {* Publicidad: banner posición 101 "Banner noticia interior" *}
                        {assign var="beforeAdv" value='<div class="separadorHorizontal"></div><div class="textoBannerPublicidad">publicidad</div>'}
                        {* renderbanner banner=$banner101 photo=$photo101 cssclass="banner486x60" beforeHTML=$beforeAdv *}
                        {insert name="renderbanner" type=101 cssclass="banner486x60" beforeHTML=$beforeAdv}
                       
                    {else}
	                    {include file="opinion_cnoticia.tpl"}
                        
                        {* Publicidad: banner posición 101 "Banner noticia interior" *}
                        {assign var="beforeAdv" value='<div class="textoBannerPublicidad">publicidad</div>'}
                        {* renderbanner banner=$banner101 photo=$photo101 cssclass="banner486x60" beforeHTML=$beforeAdv *}
                        {insert name="renderbanner" type=101 cssclass="banner486x60" beforeHTML=$beforeAdv}
                        

                        {if $opinion->with_comment eq '1'}
                            {include file="modulo_copina.tpl"}
                        {/if}
                    {/if}
                 </div>                       
                 {include file="opinion_column2.tpl"}                           
                 </div>
                 <div class="separadorHorizontal"></div>
                {include file="modulo_separadorbanners3.tpl"}
            </div>
        {include file="modulo_footer.tpl"}
        </div>
    </div>
</div>
{include file="modulo_analytics.tpl"}
</body>
</html>