{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if $smarty.request.action eq "list"}

{*php}
	if($_GET['resp']=="SI"){
			echo ' <script> alert(" Se ha eliminado correctamente."); </script> '; 
			
		} else {
		    if($_GET['resp']=="NO"){
				echo ' <script> alert(" No se ha eliminado, la Seccion no está vacia.");</script> ';
			} else {
			    if($_GET['resp']=="ya"){
					echo ' <h1>PROBLEMA: ya existe. </h1> ';
				}
			}
		}
	
{/php*}
    <div id="media_msg" style="float:right;width:300px;display:none;">   </div>
                    {assign var=msg value=''}
                    {if $smarty.request.resp eq 'SI'}
                        {assign var=msg value='Se ha eliminado correctamente.'}
                      {elseif $smarty.request.resp eq 'NO'}
                        {assign var=msg value='No se ha eliminado, la seccion no esta vacia.'}
                       {elseif $smarty.request.resp eq 'ya'}
                         {assign var=msg value='No se ha podido crear, la seccion ya existe.'}
                    {/if}
                    {if !empty($msg)}
                        <script type="text/javascript">
                            {literal}
                             showMsg({'warn':['{/literal}{$msg}. {literal}<br />  ']},'inline');
                            {/literal}
                        </script>
                 {/if}
<div>
<ul id="tabs">
	<li>
		<a href="category.php#listado">Listar secciones</a>
	</li>
	<li>
		<a href="#ordenar">Ordenar Secciones</a>
	</li>
</ul>

<div class="panel" id="listado" style="width:95%">

{include file="botonera_up.tpl" type="list"}

<table class="adminlist" id="tabla"  width="100%">
	<tr>
<!--<th>ID</th>-->
		<th width="30%" class="title">T&iacute;tulo</th>		
		<th width="10%" align="center">Nº Art&iacute;culos</th>
		<th width="10%" align="center">Nº Fotos</th>
		<th width="10%" align="center">Nº Publicidades</th>
		<th align="center" width="20%">Ver En menu</th>
		<th align="center">Modificar</th>
		<th align="center" >Eliminar</th> 
	  </tr>
	  <tr><td colspan="7"> 	{assign var=containers value=1}												
	 <div id="ctes" class="seon" style="float:left;width:100%;border:1px solid gray;"> <br>
		{section name=c loop=$categorys}
		{if $containers neq $categorys[c]->inmenu} <hr> {/if}	
		  <table width="100%" cellpadding=0 cellspacing=0  id="{$categorys[c]->pk_content_category}">
			<tr {cycle values="class=row0,class=row1"}>
				<td style="padding: 0px 10px; height: 40px;font-size: 11px;width:30%;">
					 <b> {$categorys[c]->title|clearslash|escape:"html"}</b>
				</td>
				<td style="padding: 0px 10px; height: 40px;font-size: 11px;width:10%;" align="center">
					{$num_contents[c].articles|default:0}</a>
				</td>
				<td style="padding: 0px 10px; height: 40px;font-size: 11px;width:10%;" align="center">
					{$num_contents[c].photos|default:0}</a>
				</td>
				<td style="padding: 0px 10px; height: 40px;font-size: 11px;width:10%;" align="center">
					{$num_contents[c].advertisements|default:0}</a>
				</td>
				<td style="padding:10px;font-size: 11px;width:20%;" align="center">							
					{if $categorys[c]->inmenu==1} 					
						<a href="?id={$categorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
							<img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
					{else}
						<a href="?id={$categorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
							<img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
					{/if}
					{assign var=containers value=$categorys[c]->inmenu}
				</td>
				<td style="padding: 0px 10px; height: 40px;width:10%;" align="center">
					<a href="#" title="Modificar">
							<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$categorys[c]->pk_content_category});" title="Modificar">
						<img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
				</td>
				<td style="padding: 0px 10px; height: 40px;width:10%;" align="center">
					<a href="#" title="Eliminar">
						<a href="#" onClick="javascript:confirmar(this, {$categorys[c]->pk_content_category});" title="Eliminar">
						<img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
				</td>				
			</tr>
					<tr><td colspan=7>							 	  
							   	  {section name=su loop=$subcategorys[c]}									   	   						  		   	  
							   	      <table width="100%" cellpadding=0 cellspacing=0 id="{$subcategorys[c][su]->pk_content_category}" class="tabla">
										<tr {cycle values="class=row0,class=row1"}> 											
											<td style="padding: 0px 10px 0px 40px; height: 30px; font-size: 11px;width:30%;">
												 <b>{$subcategorys[c][su]->title} </b>
											</td>
											<td align="center" style="padding: 0px 10px; height: 30px;font-size: 11px;width:10%;">
												{$num_sub_contents[c][su].articles|default:0}</a>
											</td>
										    <td align="center" style="padding: 0px 10px; height: 30px;font-size: 11px;width:10%;">
												{$num_sub_contents[c][su].photos|default:0}</a>
											</td>
											<td align="center" style="padding: 0px 10px; height: 30px;font-size: 11px;width:10%;">
												{$num_sub_contents[c][su].advertisements|default:0}</a>
											</td>
											<td align="center" style="padding: 0px 10px; height: 30px;font-size: 11px;width:20%;">
												{if $subcategorys[c][su]->inmenu==1} 					
													<a href="?id={$subcategorys[c][su]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
														<img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
												{else}
													<a href="?id={$subcategorys[c][su]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
														<img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
												{/if}
												{assign var=containers2 value=$subcategorys[c][su]->inmenu}												
											</td>
											<td style="padding: 0px 10px; height: 30px;width:75px;width:10%;" align="center">
												<a href="#" title="Modificar">
														<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$subcategorys[c][su]->pk_content_category});" title="Modificar">
													<img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
											</td>
											<td style="padding: 0px 10px; height: 30px;width:75px;width:10%;" align="center">
												<a href="#" title="Eliminar">
													<a href="#" onClick="javascript:confirmar(this, {$subcategorys[c][su]->pk_content_category});" title="Eliminar">
													<img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
											</td>								
										</tr>						
									  </table>	 	   
							   	  {/section}						   
						   	 	</td>								
							</tr>			
							
		  </table>
		{sectionelse}
		<tr>
			<td align="center" colspan=5><br><br><p><h2><b>Ning&uacute;na secci&oacute;n guardada</b></h2></p><br><br></td>
		</tr>
		{/section}		
  </div>
  </td></tr>
  <tr>

</tr>
{if count($categorys) gt 0}
<tr>
<td colspan="5" style="padding:10px;font-size: 12px;" align="center"><br>{$paginacion->links}<br></td>
</tr>
{/if}

</table>
</div>

{* FORMULARIO PARA ORDENAR NOTICIAS ************************************** *}

<div class="panel" id="ordenar" style="width:95%">

		{include file="botonera_up.tpl" type="order"}
		
		<table class="adminlist" id="tabla"  width="99%" cellpadding=0 cellspacing=0 >
				<tr>
			<!--<th>ID</th>-->
					<th width="30%" class="title">T&iacute;tulo</th>
					<th width="30%" align="center">Nombre interno</th>
					<th align="center" width="10%">Ver En menu</th>
					<th align="center" width="15%">Modificar</th>
					<th align="center" width="25%">Eliminar</th>
				
				  </tr>
				  <tr><td colspan="5">
				 <div id="cates" class="seccion" style="float:left;width:100%;border:1px solid gray;"> <br>
					{section name=c loop=$ordercategorys}
					   {if $ordercategorys[c]->pk_content_category neq "20"}
						  <table width="100%"  id="{$ordercategorys[c]->pk_content_category}" class="tabla" cellpadding=0 cellspacing=0 >
							<tr {cycle values="class=row0,class=row1"} style="cursor:pointer;border:0px; padding:0px;margin:0px;">
								<td style="padding:10px;font-size: 11px;width:30%;">
									  {$ordercategorys[c]->title}
								</td>
								<td align="center" style="padding:10px;font-size: 11px;width:30%;">
									{$ordercategorys[c]->name|clearslash}</a>
								</td>
							
								<td align="center" style="padding:10px;font-size: 11px;width:10%;">
									{if $ordercategorys[c]->inmenu==1} 					
										<a href="?id={$ordercategorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
											<img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
									{else}
										<a href="?id={$ordercategorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
											<img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
									{/if}
								</td>
								<td style="padding: 0px 10px; height: 40px;width:15%;" align="center">
                                                                    {if $ordercategorys[c]->internal_category==1}
                                                                        <a href="#" title="Modificar">
											<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$ordercategorys[c]->pk_content_category});" title="Modificar">
										<img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
                                                                    {/if}
                                                                </td>
								<td style="padding: 0px 10px; height: 40px;width:15%;" align="center">
								{if $ordercategorys[c]->internal_category==1}
									<a href="#" title="Eliminar">
										<a href="#" onClick="javascript:confirmar(this, {$ordercategorys[c]->pk_content_category});" title="Eliminar">
										<img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
								{else} &nbsp;
								{/if}
								</td>								
							</tr>	
							</table>					
						{/if}
					{sectionelse}
					<tr>
						<td align="center" colspan=5><br><br><p><h2><b>Ning&uacute;na secci&oacute;n guardada</b></h2></p><br><br></td>
					</tr>
					{/section}		
			  </div>
			  </td></tr>
		</table>			
		{literal}
		<script type="text/javascript">
		 // <![CDATA[		 
		  Sortable.create('cates',{
		   tag:'table',
		   dropOnEmpty: true, 
		    containment:["cates"],
		   constraint:false});
		
		 // ]]>
		</script>
		{/literal}
</div>


{/if}
	
<div>


{* FORMULARIO PARA ENGADIR ************************************** *}

{if isset($smarty.request.action) && $smarty.request.action eq "new"}

{include file="botonera_up.tpl"}

<div id="warnings-validation"></div>

<!-- <div class="panel" id="edicion-contenido" style="width:720px">-->
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="700">
<tbody>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título"
			value="" class="required" size="100" />
		
	</td>
</tr>

<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Subsecci&oacute;n de:</label>
	</td>
<td style="padding:4px;" nowrap="nowrap" width="70%">
			<select name="subcategory" class="required" size="12">
				<option value="0" selected > </option>
				{section name=as loop=$allcategorys}
					<option value="{$allcategorys[as]->pk_content_category}">{$allcategorys[as]->title}</option>
		   		 {/section}
	    </select>
	</td>
</tr>

<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
	<label for="summary">Ver en menú:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="checkbox" id="inmenu" name="inmenu" value="1" checked="checked" >
		* Activado, ver en el menú de portada.
	</td>
</tr>

	
</tbody>
</table>
<!-- </div> -->

{/if}


{* FORMULARIO PARA ACTUALIZAR *********************************** *}
{if isset($smarty.request.action) && $smarty.request.action eq "read"}

{include file="botonera_up.tpl"}

<div id="warnings-validation"></div>

<!-- <div class="panel" id="edicion-contenido" style="width:720px"> -->
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="700">
<tbody>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título de la categoria"
			value="{$category->title|clearslash}" class="required" size="100" />		
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Nombre interno:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
	<input type="text" id="name" name="name" title="carpeta categoria" readonly
			value="{$category->name|clearslash}" class="required" size="100" />		
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Subsecci&oacute;n de:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
			<select name="subcategory" class="required" size="12">
				<option value="0" {if $category->fk_content_category eq '0'}selected{/if}> </option>
				{section name=as loop=$allcategorys}
					<option value="{$allcategorys[as]->pk_content_category}" {if $category->fk_content_category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->name}</option>
		   		 {/section}
	    </select>
	</td>
</tr>

<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
	<label for="summary">Ver en menú:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="checkbox" id="inmenu" name="inmenu" value="1" {if $category->inmenu eq 1}checked{/if}>
		* Activado, ver en el menú de portada.
	</td>
</tr>

{if $subcategorys}
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Subsecciones:</label>
	</td>
    <td style="padding:4px;" nowrap="nowrap" width="70%"> <br>
	<table border="0" class="adminlist" id="cates">	
		<tr>
			<!--<th>ID</th>-->
					<th class="title"  width="25%">T&iacute;tulo</th>
					<th  width="25%">Nombre interno</th>
					<th align="center">En menu</th>
					<th align="center" style="padding:10px;width:20%;">Modificar</th>
					<th align="center" style="padding:10px;width:20%;">Eliminar</th> 
				  </tr>
				  <tr><td colspan="5">
				 <div id="subcates" class="seccion" style="float:left;width:100%;border:1px solid gray;"> <br>				
					{section name=s loop=$subcategorys}		
					 	  <table width="100%" class="tabla" style="cursor:pointer;" id="{$subcategorys[s]->pk_content_category}">	
							 <tr {cycle values="class=row0,class=row1"}>
								<td style="padding:10px;font-size: 11px;width:25%;">
									 {$subcategorys[s]->title}</a>
								</td>
								<td style="padding:10px;font-size: 11px;width:25%;">
									 {$subcategorys[s]->name}</a>
								</td>	
								
								<td style="padding:10px;font-size: 11px;width:10%;"  align="center">
									{if $subcategorys[s]->inmenu==1} Si {else}No {/if}
									</a>
								</td>		
								<td style="padding:10px;width:20%;" align="center">
									<a href="#" title="Modificar">
											<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$subcategorys[s]->pk_content_category});" title="Modificar">
										<img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
								</td>
								<td style="padding:10px;width:20%;" align="center">
									<a href="#" title="Eliminar">
										<a href="#" onClick="javascript:confirmar(this, {$subcategorys[s]->pk_content_category});" title="Eliminar">
										<img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>				</td>
								
							</tr>			
					 	  </table>
					{/section}	
				</div>
		</td></tr>
	</table>		
</td>
</tr>		
{literal}
			<script type="text/javascript">
		 // <![CDATA[		 
		  Sortable.create('subcates',{
		    tag:'table',
		    dropOnEmpty: true, 
		    containment:["subcates"],
		   constraint:false});
		
		 // ]]>
		</script>
		{/literal}
{/if}

</tbody>
</table>
<!-- </div> -->

{/if}

{include file="footer.tpl"}