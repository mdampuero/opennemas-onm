{extends file="base/admin.tpl"}

{block name="admin_menu"}
	<div id="menu-acciones-admin" class="clearfix">
        <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
		<ul>
			 <li>
                            <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelFiles', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
                                <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar todos"><br />Eliminar todos
                            </a>
                        </li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelFiles', 0);"  onmouseover="return escape('<u>E</u>liminar seleccionados');" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
				</button>
			</li>
		</ul>
    </div>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}
	 style="max-width:70% !important; margin: 0 auto; display:block;">

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
						  <span style="color:#222 ;margin-left: 12px;margin-right: 12px;">{$subcat[as][su]->title}</span></a>
				</li>
			{else}
				{if $subcat[as][su]->fk_content_category eq $datos_cat[0]->fk_content_category}
					{assign var=subca value=$subcat[as][su]->pk_content_category}
					<li>
						<a href="{$home}?action=list&category={$subcat[as][su]->pk_content_category}" {if $category==$subca} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} >
							<span style="color:#222 ;margin-left: 12px;margin-right: 12px;">{$subcat[as][su]->title}</span></a>
					</li>
				{/if}
			{/if}
			{/section}
			</ul>
		</div>
	{/section}
</div>

<br><br>

{block name="admin_menu"}{/block}
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

<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}


{block name="footer-js"}
{$smarty.block.parent}
    <script type="text/javascript">
        function confirmar(url) {
            if(confirm('¿Está seguro de querer eliminar este fichero?')) {
                location.href = url;
            }
        }
    </script>
{/block}
