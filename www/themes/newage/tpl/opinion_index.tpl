{include file="modulo_head.tpl"}
<body>

{insert name="intersticial" type="50"}    
    
<div class="global_metacontainer">
    <div class="marco_metacontainer">
        <div class="metacontainer">
            {include file="modulo_separadorbanners1.tpl"}
            {include file="modulo_header.tpl"}
            <div class="container">
                <div class="containerNoticias">
                    <div class="column12">
                        <div class="containerCol12 fondoContainerActualidad">
                             <form method="post" action="/opinions/listar_autores/'.$('autores').options[this.selectedIndex].value/{$smarty.now}.html">
                                 Seleccione autor:
                                 <select name="autores" id="autores" class="" onChange=" {literal} if(this.options[this.selectedIndex].value){ window.location='/opinions/opinions_do_autor/'+this.options[this.selectedIndex].value+'/'+this.options[this.selectedIndex].getAttribute('name')+'.html';} {/literal}">
                                    <option name=" " value="0" selected="selected">--Autor--</option>
                                    <option name="Editorial" value="1" {if $author_id eq 1} selected="selected" {/if} >Editorial</option>
                                    <option name="Director" value="2" {if $author_id eq 2} selected="selected" {/if} >Director</option>
                                    {section name=as loop=$autores}
                                         <option name="{$autores[as]->name}" value="{$autores[as]->pk_author}" {if $autores[as]->pk_author eq $author_id} selected="selected" {/if} >{$autores[as]->name}</option>
                                    {/section}
                                 </select>
                             </form>
                        </div>
                        <div class="containerCol12 fondoContainerActualidad">
                            {if $author_id>0}
                                {include file="opinion_author_opinions.tpl"}
                            {else}
                                {include file="opinion_index_content.tpl"}
                            {/if}
                        </div>
                        
                    </div>
                    {include file="opinion_index_column3.tpl"}
                </div>
                
                <div class="separadorHorizontal"></div>
                {include file="modulo_separadorbanners3.tpl"}
                {* <div class="separadorHorizontal"></div> *}
             </div>
        {include file="modulo_footer.tpl"}
        </div>
    </div>
</div>
{include file="modulo_analytics.tpl"}
</body>
</html>