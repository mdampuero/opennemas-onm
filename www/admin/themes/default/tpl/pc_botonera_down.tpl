{* Botonera Plan Conecta Secciones -------------------------------------------- *}
{if preg_match('/pc_sections\.php/',$smarty.server.SCRIPT_NAME)}

	<div id="menu-acciones-admin">
		<div style="float:left"><b><em>DRAG & DROP: pinche y arrastre las filas para determinar el orden de las categorias</em></b></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:savePriority();" title="Guardar Positions" alt="Guardar Cambios">
				   <img border="0" src="{php}echo($this->image_dir);{/php}save.gif" title="Save Positions" alt="Guardar Cambios" ><br />Guardar Cambios
				</a>
			</li>
		</ul>
	</div>

{/if}