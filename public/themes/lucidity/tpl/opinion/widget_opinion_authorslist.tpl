<div class='widget-all-authors span-8 last'>
    <div>
            
        <div class='title'>Otros columnistas</div>
       
       <div class='content'>
            <form method="post" action="/opinions/listar_autores/'.$('autores').options[this.selectedIndex].value/{$smarty.now}.html">        
                <select name="autores" id="autores" class="" onChange="{literal} if(this.options[this.selectedIndex].value!=-1){ window.location='/opinions_autor/'+this.options[this.selectedIndex].value+'/'+this.options[this.selectedIndex].getAttribute('name')+'.html';} {/literal}">
                    <option name="" value="0"> -- Seleccione su autor --</option>
                    <option name="Editorial" value="1" {if $author_id eq 1} selected="selected" {/if}>Editorial</option>
                    <option name="Director" value="2" {if $author_id eq 2} selected="selected" {/if} >Director</option>
                    {section name=as loop=$list_all_authors}
                            <option name="{$list_all_authors[as]->name}" value="{$list_all_authors[as]->pk_author}" {if $list_all_authors[as]->pk_author eq $author_id} selected="selected" {/if} >{$list_all_authors[as]->name|truncate:24:"...":'TRUE'} </option>
                    {/section}
                </select>
            </form>
       </div>

    </div>
    
</div>

