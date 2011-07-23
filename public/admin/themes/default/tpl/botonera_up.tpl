{* Botonera opinion -------------------------------------------- *}
{if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div class="top-action-bar">
		<div class="wrapper-content">
			<div class="title"><h2>{$titulo_barra} :: {$accion}</h2></div>
			<ul class="old-button">
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);return false;" name="submit_mult" value="Eliminar" title="Eliminar">
						<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar"><br />{t}Delete{/t}
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);return false;" name="submit_mult" value="noFrontpage" title="noFrontpage">
						<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />{t}Unpublish{/t}
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
						<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Frontpage" alt="Frontpage" ><br />{t}Publish{/t}
					</a>
				</li>
				 {if $type_opinion neq '-1'}
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 1);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
						<img border="0" src="{$params.IMAGE_DIR}gohome50.png"  title="Frontpage" alt="Frontpage" ><br />{t}Put in home{/t}
					</a>
				</li>
				{/if}
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 0);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
						<img border="0" src="{$params.IMAGE_DIR}home_no50.png"  title="Frontpage" alt="Frontpage" ><br />{t escape="off"}Delete from home{/t}
					</a>
				</li>
				<li>
					<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
						<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
					</button>
				</li>
				<li>
					<a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}opinion.png" title="Nuevo" alt="Nuevo"><br />{t escape="off"}New opinion{/t}
					</a>
				</li>
				 {if $type_opinion eq '-1'}
					<li>
						<a href="#" class="admin_add" onClick="javascript:savePositionsOpinion();" title="Guardar Positions" alt="Guardar Posiciones">
							<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar Cambios" alt="Guardar Posiciones"><br />{t}Save positions{/t}
						</a>
					</li>
				{/if}
				 <li class="separator"> </li>
				<li>
					<a href="author.php?action=new&desde=opinion" class="admin_add" name="submit_mult" value="Nuevo Autor" title="Nuevo Autor">
						<img border="0" src="{$params.IMAGE_DIR}authors_add.png" title="Nuevo Autor" alt="Nuevo Autor"><br />{t escape="off"}Create author{/t}
					</a>
				</li>

				<li >
					<a href="author.php?action=list&desde=opinion" class="admin_add" name="submit_mult" value="Listado Autores" title="Listado Autores">
						<img border="0" src="{$params.IMAGE_DIR}authors.png" title="Listado Autores" alt="Listado Autores"><br />Ver Autores
					</a>
				</li>
			</ul>
		</div>
	</div>



{* Botonera category -------------------------------------------- *}
{elseif preg_match('/category\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	{if $type eq "list"}

	{elseif $type eq "order"}
		<div id="menu-acciones-admin" class="clearfix">
			<div style="float:left"><b><em>DRAG &amp; DROP: pinche y arrastre las filas para determinar el orden en el menú de las secciones</em></b></div>
			<ul>
				<li>
					<a href="#" class="admin_add" onClick="javascript:savePriority();" title="Guardar Positions" alt="Guardar Cambios">
						<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar Cambios" alt="Guardar Cambios"><br />{t}Save changes{/t}
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
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="javascript:savePriority();sendFormValidate(this, '_self', 'validate', '{$category->pk_content_category}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
			{if isset($category->pk_content_category)}
               <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$category->pk_content_category}, 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0, 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
			<li>
				<a href="{$_SERVER['PHP_SELF']}?desde={$_SESSION['desde']}" class="admin_add" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
		</ul>
	</div>

{* Botonera autores -------------------------------------------- *}
{elseif preg_match('/author\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Opinion Manager :: Author list{/t}</div>
		<ul>
			<li>
				<a href="{$_SERVER['PHP_SELF']}?action=new&page=0" class="admin_add"  accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}authors_add.png" title="{t}New author{/t}" alt="{t}New author{/t}"><br />{t}New author{/t}
				</a>
			</li>
            <li class="separator"></li>
            <li>
				<a href="opinion.php?action=new&desde=author" class="admin_add" name="submit_mult" value="{t}New opinion{/t}" title="{t}New opinion{/t}">
					<img border="0" src="{$params.IMAGE_DIR}opinion.png" title="{t}New opinion{/t}" alt="{t}New opinion{/t}"><br />{t}New opinion{/t}
				</a>
			</li>

            <li >
				<a href="opinion.php?action=list&desde=author" class="admin_add" name="submit_mult" value="Listado Opiniones" title="Listado Opiniones">
					<img border="0" src="{$params.IMAGE_DIR}opinion.png" title="Listado Opiniones" alt="Listado Opiniones"><br />{t escape="off"}Go to <br/>opinion manager{/t}
				</a>
			</li>
		</ul>

	</div>

{elseif preg_match('/author\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{if $smarty.request.action eq "new"}{t}Opinion Manager :: New author{/t}{else}{t}Opinion Manager :: Edit author{/t}{/if}</div>
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$author->pk_author}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}user_validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				{if $smarty.session._from eq 'opinion.php'}
						<a href="opinion.php" class="admin_add">
						<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
					</a>
				{else}
					<a href="{$_SERVER['PHP_SELF']}?action=list&page=0" class="admin_add" title="Cancelar">
						<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
					</a>
				{/if}
			</li>
			<li>
			{if isset($author->pk_author)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$author->pk_author}, 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0, 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
		</ul>
	</div>
{/if}
