{* Botonera opinion -------------------------------------------- *}

{* Botonera category -------------------------------------------- *}
{if preg_match('/category\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	{if $type eq "list"}

	{elseif $type eq "order"}
		<div class="top-action-bar clearfix">
			<div style="title"><strong><em>DRAG &amp; DROP: pinche y arrastre las filas para determinar el orden en el menú de las secciones</em></strong></div>
			<ul class="old-button">
				<li>
					<a href="#" class="admin_add" onClick="javascript:savePriority();" title="Guardar Positions" alt="Guardar Cambios">
						<img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar Cambios" alt="Guardar Cambios"><br />{t}Save changes{/t}
					</a>
				</li>
				<li>
					<a href="{$_SERVER['PHP_SELF']}?action=new" class="admin_add"  accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}advertisement.png" title="Nueva Sección" alt="Nueva Sección"><br />{t}New section{/t}
					</a>
				</li>
			</ul>
		</div>
	{/if}

{* Botonera category -------------------------------------------- *}
{elseif preg_match('/category\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "read") || ($smarty.request.action eq "new"))}
	<div class="top-action-bar clearfix">
		<ul class="old-button">
		    <li>
				<a href="#" class="admin_add" onClick="javascript:savePriority();sendFormValidate(this, '_self', 'validate', '{$category->pk_content_category}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
			{if isset($category->pk_content_category)}
               <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$category->pk_content_category}, 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0, 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
			<li>
				<a href="{$_SERVER['PHP_SELF']}?desde={$_SESSION['desde']}" class="admin_add" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
		</ul>
	</div>
{/if}
