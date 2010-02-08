{* Botonera articles -------------------------------------------- *}
{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
	
	<div style='float:left'><h2>&nbsp;{$datos_cat[0]->title}</h2></div>
	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
				</a>
			</li>
                        {if $category eq 'home'}
                                 <li>
                                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 0);" name="submit_mult" value="no Sugeridas" title="no Sugeridas">
                                            <img border="0" src="{php}echo($this->image_dir);{/php}home_no50.png" title="No sugeridas" alt="No sugeridas" ><br />No sugeridas
                                    </a>
                            </li>
                        {else}
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="noFrontpage" alt="noFrontpage" ><br />Publicar
				</a>
			</li>                     
                            <li>
                                    <a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 0);" name="submit_mult" value="Archivar" title="Archivar">
                                            <img border="0" src="{php}echo($this->image_dir);{/php}archive.gif" title="Archivar" alt="Archivar" ><br />Archivar
                                    </a>
                            </li>
                            <li>
                                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['no_selected_fld[]'],'select_button2');">
                                            <img id="select_button2"  status="0" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo">
                                    </button>
                            </li>
                        {/if}
                            <li>
                                    <a href="#" class="admin_add" onClick="javascript:savePos('{$category}');" title="Guardar Positions" alt="Guardar Cambios">
                                            <img border="0" src="{php}echo($this->image_dir);{/php}save.gif" title="Guardar Cambios" alt="Guardar Cambios" ><br />Guardar posiciones
                                    </a>
                            </li>
                        
		</ul>
	</div>

{* Botonera comentarios -------------------------------------------- *}
{elseif preg_match('/comment\.php/',$smarty.server.SCRIPT_NAME)}

	<div style='float:left'><h2>&nbsp;{$datos_cat[0]->title}</h2></div>
	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 2);" name="submit_mult" value="noFrontpage" title="Rechazar">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish_no.gif" title="Rechazar" alt="Rechazar" ><br />Rechazar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Publicar">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2');">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
		</ul>
	</div>

{* Botonera opinion -------------------------------------------- *}
{elseif preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}

	<div style='float:left'><h2>Artículos de Opinión</h2></div>
	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="Frontpage" alt="Frontpage" ><br />Publicar
				</a>
			</li>
			{if $type_opinion neq '-1' }			
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}gohome50.png" width="50" title="Frontpage" alt="Frontpage" ><br />En Home
				</a>
			</li>
			{/if}
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 0);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}home_no50.png" width="50" title="Frontpage" alt="Frontpage" ><br />No Home
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2');">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva opinion');" accesskey="N" tabindex="1">
					<img border="0" src="{php}echo($this->image_dir);{/php}opinion.png" title="Nuevo" alt="Nuevo"><br />Nuevo
				</a>
			</li>
			{if $type_opinion eq '-1' }
				<li>
					<a href="#" class="admin_add" onClick="javascript:savePositionsOpinion();" title="Guardar Positions" alt="Guardar Posiciones">
						<img border="0" src="{php}echo($this->image_dir);{/php}save.gif" title="Guardar Cambios" alt="Guardar Posiciones"><br />Guardar Posiciones
					</a>
				</li>
			{/if}
		</ul>
	</div>

{* Botonera advertisement -------------------------------------------- *}
{elseif preg_match('/advertisement\.php/',$smarty.server.SCRIPT_NAME)}

	<div style='float:left'><h2>&nbsp;{$datos_cat[0]->title} {if $category eq 0}GEN&Eacute;RICA{/if}</h2></div>
	<div id="menu-acciones-admin">
		<ul>
			
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="Frontpage" alt="Frontpage" ><br />Publicar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2');">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
			
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva opinion');" accesskey="N" tabindex="1">
					<img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Nuevo" alt="Nuevo"><br />Nuevo
				</a>
			</li>
		</ul>
	</div>

{* Botonera hemeroteca -------------------------------------------- *}
{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)  && ($smarty.request.action eq "list_hemeroteca")}

	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add"  onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mstatus', 1);" name="submit_mult" value="Archivar" title="Archivar">
				    <img border="0" src="{php}echo($this->image_dir);{/php}archive_no.png" title="Archivar" alt="Archivar"><br />Recuperar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2');">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
		</ul>
	</div>

{* Botonera Papelera -------------------------------------------- *}
{elseif preg_match('/litter\.php/',$smarty.server.SCRIPT_NAME)}
	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar3(this, '_self', 'mremove', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar3(this, '_self', 'm_no_in_litter', 0);" name="submit_mult" value="Recuperar" title="Recuperar">
				    <img border="0" src="{php}echo($this->image_dir);{/php}trash_no.png" title="Recuperar" alt="Recuperar"><br />Recuperar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
		</ul>
    </div>
{/if}