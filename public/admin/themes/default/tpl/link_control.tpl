{include file="header.tpl"}
{literal}
<script type="text/javascript">
function confirmar(url) {
	if(confirm('¿Está seguro de querer eliminar este fichero?')) {
		location.href = url;
	}
}

</script>
{/literal}

{if isset($smarty.request.message) && strlen($smarty.request.message) > 0}
	<div class="message" id="console-info">{$smarty.request.message}</div>
	<script type="text/javascript">
		new Effect.Highlight('console-info', {ldelim}startcolor:'#ff99ff', endcolor:'#999999'{rdelim})
	</script>
{/if}
<div>
<ul class="tabs2">
		{section name=as loop=$allcategorys}
		    <li>
				 {assign var=ca value=$allcategorys[as]->pk_content_category}
				<a href="link_control.php?listmode={$listmode}&category={$ca}" {if $category==$ca} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} {/if} >{$allcategorys[as]->title}</a>
			</li>
			{/section}
			 <li>		
				<a href="link_control.php?listmode={$listmode}&category=3" {if $category==3} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>ALBUMS</a>
			</li> 
			 <li>		
				<a href="link_control.php?listmode={$listmode}&category=2" {if $category==2} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>PUBLICIDAD</a>
			</li>
	</ul>
		 <br>
	     <div style="clear:left;"> 
	     {section name=as loop=$allcategorys}			 
				  <div id="{$allcategorys[as]->name}" style="display:inline "> 
				   <ul class="tabs2"> 
				   	  {section name=su loop=$subcat[as]}
				   	    {if $allcategorys[as]->pk_content_category eq $category}
				   	      {assign var=subca value=$subcat[as][su]->pk_content_category}
				   	       <li>
				   	      	<a href="{$home}?action=list&category={$subcat[as][su]->pk_content_category}" {if $category==$subca} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} >
				   	      				<span style="color:#222 ;margin-left: 12px;margin-right: 12px;">{$subcat[as][su]->title}</span>
				   	      			</a> 
				   	      	 </li>
				   	     {else}
				   	      	 {if $subcat[as][su]->fk_content_category eq $datos_cat[0]->fk_content_category}
				   	     		{assign var=subca value=$subcat[as][su]->pk_content_category}
						   	       <li>
						   	      		<a href="{$home}?action=list&category={$subcat[as][su]->pk_content_category}" {if $category==$subca} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} >
						   	      				<span style="color:#222 ;margin-left: 12px;margin-right: 12px;">{$subcat[as][su]->title}</span>
						   	      			</a> 
						   	      	 </li>
				   	     		 {/if} 	
				   	     {/if} 	  			   	  
				   	  {/section}
				   	  </ul> 
				  </div>  
			{/section}
		</div>

<br><br>

{include file="botonera_up.tpl"}
</div>

<div id="{$category}" class="categ" style="width:100%; padding: 6px 2px;">

	{* Vista en miniatura de los ficheros ***************************************************************************** *}
	{if $listmode == 'weeks'}
	<br>
	<br />
	<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="99%">
	<tbody>
	<tr>
		<td width="15" height="100%" valign="top">
			<td valign="top">
			<!--fieldset>
				<legend>Ficheros</legend-->
				<form action="{$smarty.server.SCRIPT_NAME}">	
					<table class="adminlist" border=0>
					   <tr>
					   <th style="width:5%;"> </th>
					   <th style="width:20%;" align='left'>Nombre</th>
						<th style="width:40%;" align='left'>Ruta</th>
						<th style="width:10%;" >Tipo</th>
						<th  style="width:10%;">Tamaño</th>
						<th  style="width:10%;">Fecha</th>						
						<th  style="width:5%;">Eliminar</th>
					  </tr>
					  <tr >
						{section name=n loop=$photo}
						  {if $photo[n]->content_status eq 0}	
								<tr {cycle values="class=row0,class=row1"}> 
										<td style="text-align: left;font-size: 11px;"> 	
											<input type="checkbox" class="minput" id="selected_{$smarty.section.n.iteration}" name="selected_fld[]" value="{$photo[n]->pk_photo}"  >							
										 </td>
								    	<td style="text-align: left;font-size: 11px;"> 
											{$photo[n]->name}
										</td>
										<td style="text-align: left;font-size: 11px;"> 
									 	  {$photo[n]->path_file}{$photo[n]->name}
									 	</td>  
										<td style="text-align: center;font-size: 11px;"> 							
											{$photo[n]->type_img}
										</td>
										<td style="text-align: center;font-size: 11px;"> 
											 {$photo[n]->size}KB  - ({$photo[n]->width}x{$photo[n]->height}) &nbsp;
										 </td>
										 <td style="text-align: center;font-size: 11px;"> 							
											{$photo[n]->created}
										</td>
										 <td style="text-align: center;font-size: 11px;"> 
											<a href="#" onclick="javascript:confirmar('?action=delFile&amp;id={$photo[n]->pk_photo}&amp;listmode=weeks&amp;category={$category}&amp;page={$paginacion->_currentPage}');" title="Eliminar fichero">
											   <img src="{$params.IMAGE_DIR}iconos/eliminar.gif" border="0" align="absmiddle" /></a>&nbsp;
										</td>
									</tr>
								{/if}
						{/section}
	
						<br class="clearer" />
	
				
					<br class="clearer" />
					{if count($photo) gt 0}
						<br class="clearer" />
						<div align="center">{$paginacion->links}</div>
					{/if}
					</div>
	
					<input type="hidden" name="listmode" value="weeks" />
				</form>
			<!--/fieldset-->
		</td>
	</tr>	
	</tbody>
	</table>
	{/if}

</div>
{include file="footer.tpl"}