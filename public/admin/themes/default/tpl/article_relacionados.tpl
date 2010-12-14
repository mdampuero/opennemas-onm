
<table border="0" cellpadding="6" cellspacing="4" class="fuente_cuerpo" width="99%">
<tbody>
<tr valign='top'>
	<td width="400">
	{* onClick="{section start=$aux loop=$todos2 step=1 name=foo}Effect.Fade('interior#up{$smarty.section.foo.index}');{/section}Effect.Appear('imgint');return false;" *}
				
				<a onclick="search_related(0,$('metadata').value,1); divs_hide('search-noticias');" style="cursor:pointer;"><b>Noticias Sugeridas:</b></a><hr>
				<a onclick="get_div_contents(0,'noticias',' {$article->category}',1); divs_hide('noticias_div');" style="cursor:pointer;"><b>Noticias por Secciones:</b></a><hr>
                <a onclick="get_div_contents(0,'hemeroteca',' {$article->category}',1); divs_hide('hemeroteca_div');" style="cursor:pointer;"><b>Noticias de Hemeroteca:</b></a><hr>
                <a onclick="get_div_contents(0,'pendientes',' {$article->category}',1); divs_hide('pendientes_div');" style="cursor:pointer;"><b>Noticias Pendientes:</b></a><hr>
				<a onclick="get_div_contents(0,'opinions',0,1);  divs_hide('opinions_div');" style="cursor:pointer;"><b>Opiniones:</b></a><hr>
				<a onclick="get_div_contents(0,'albums',3,1); divs_hide('albums_div');" style="cursor:pointer;"><b>Galerias:</b></a><hr>
				<a onclick="get_div_contents(0,'videos',0,1);  divs_hide('videos_div');"  style="cursor:pointer;"><b>Videos:</b></a><hr>
				<a onclick="get_div_contents(0,'adjuntos', '{$article->category}',1); divs_hide('adjuntos_div'); " style="cursor:pointer;"><b>Ficheros relacionados:</b></a><hr>
			
	
	</td>
	<td>
		<div id='search-noticias' class='div_lists'   style="display:none;width:95%;">
		
			<br/><h2>NOTICIAS SUGERIDAS:</h2>
		</div>	

	 	<div id="noticias_div" class='div_lists' style="display:none;width:95%;"><br />     
	 				{include file="menu_categorys.tpl" home=""}	 	   
	 		<h2>NOTICIAS:</h2>
        	  
		</div>
                 <div id="hemeroteca_div" class='div_lists' style="display:none;width:95%;"><br />
	 				{include file="menu_categorys.tpl" home=""}
	 		<h2>HEMEROTECA</h2>

		</div>
                <div id="pendientes_div" class='div_lists' style="display:none;width:95%;"><br />
	 				{include file="menu_categorys.tpl" home=""}
	 		<h2>PENDIENTES</h2>

		</div>
		
		<div id="opinions_div" class='div_lists' style="display:none;width:95%;">			
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
				<tbody><tr>
				<td colspan="2">				 	
				 		<h2>OPINIONES:</h2>												
				</td>
				</tr>
				</tbody>
			</table>
		</div>
		

		<div  id="albums_div"  class='div_lists' style="width:95%;display:none;">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
				<tbody><tr>
				<td colspan="2">			
					{include file="menu_categorys.tpl" home=""}	 			 
					 	 <h2>FOTOGALERIAS:</h2>													 
				</td>
				</tr>
				</tbody>
			</table>
		</div>

 		<div id='videos_div'  class='div_lists' style="display:none;"><br/>	
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
				<tbody><tr>
				<td colspan="2">
                        {include file="menu_categorys.tpl" home=""}	
					 	<h2>VIDEOS:</h2>													 
				</td>
				</tr>
				</tbody>
			</table>
		</div>
					  
					  
		<div id="adjuntos_div"  class='div_lists' style="display:none; width:95%;">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
			<tbody>	
			<tr>
				<td>
					{include file="menu_categorys.tpl" home=""}	 	
			        <h2>ARCHIVOS ADJUNTOS:</h2> 
				 </td>
			</tr>
			</tbody>
			</table>
		</div>


		<div id="search_div"  class='div_lists' style="display:none; width:95%;">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
			<tbody>	
				<tr>
					<td>
						 Buscar: <input type="text" id="stringSearch" name="stringSearch" title="stringSearch"
				                        class="required" size="80" onkeypress=" "/>
				               
				     
					 </td>
				</tr>
			</tbody>
			</table>
		</div>

</td>
</tr>
</tbody>
</table>