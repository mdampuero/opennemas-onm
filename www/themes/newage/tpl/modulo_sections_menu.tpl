<div class="zonaSecciones">      
    <div class="menuCabeceraTexto">
      <ul>
      {foreach key=k item=v from=$categories}
        {if $category_name eq $v.name}
            <li class="menuselec"><a href="/seccion/{$v.name}/">{$v.title}</a></li>
	    {else}
            <li class="opcion"><a href="/seccion/{$v.name}/">{$v.title}</a></li>
	    {/if}
	  {/foreach}
      </ul>
    </div>    
</div>