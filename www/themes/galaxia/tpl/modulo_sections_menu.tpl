{*<!-- ****************************** MENU DE SECCIONES ******************************* 
# index.php => /seccion/2008/09/29/galicia/ (categoria), pagina principal de la seccion
# index.php => /seccion/2008/09/29/galicia/ourense/ (categoria y subcategoria)
#-->*}
<div class="zonaSecciones">      
    <div class="menuCabeceraTexto">
      <ul>
      {foreach key=k item=v from=$categories}
	    {*How to put the triangle figure under the section*}
        {if $category_name eq $v.name}
            {* <li id="menu_opt{$k}" class="menuselec" ><a href="/seccion/{$v.name}/">{$v.title}</a></li> *}
            <li class="menuselec"><a href="/seccion/{$v.name}/">{$v.title}</a></li>
	    {else}
            {* <li id="menu_opt{$k}" class="opcion"><a href="/seccion/{$v.name}/">{$v.title}</a></li> *}
            <li class="opcion"><a href="/seccion/{$v.name}/">{$v.title}</a></li>
	    {/if}
	    {*How to put the triangle figure under the section*}
	    <li class="CContenedorSeparadorMenu"><div class="CSeparadorMenu"></div></li> 
	  {/foreach}
      </ul>
    </div>    
</div>
{*<!-- ****************************** MENU DE SECCIONES ******************************* -->*}