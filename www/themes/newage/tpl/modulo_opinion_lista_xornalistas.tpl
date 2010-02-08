<form method="post" action="/opinions/listar_autores/'.$('autores').options[this.selectedIndex].value/{$smarty.now}.html">        
    <select name="autores" id="autores" class="" onChange="{literal} if(this.options[this.selectedIndex].value!=-1){ window.location='/opinions/opinions_do_autor/'+this.options[this.selectedIndex].value+'/'+this.options[this.selectedIndex].getAttribute('name')+'.html';} {/literal}">
        <option name="" value="0">--Autor--</option>
        <option name="Editorial" value="1" {if $author_id eq 1} selected="selected" {/if}>Editorial</option>
        <option name="Director" value="2" {if $author_id eq 2} selected="selected" {/if} >Director</option>
        {section name=as loop=$todos_pag}
                <option name="{$todos_pag[as]->name}" value="{$todos_pag[as]->pk_author}" {if $todos_pag[as]->pk_author eq $author_id} selected="selected" {/if} >{$todos_pag[as]->name|truncate:24:"...":TRUE} </option>
        {/section}
    </select>
</form>