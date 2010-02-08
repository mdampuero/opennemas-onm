{* Botonera Plan Conecta Opinion -------------------------------------------- *}
{if preg_match('/pc_opinion\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list") }

	<div id="menu-acciones-admin">
            <ul>
                    <li>
                            <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
                                <img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
                            </a>
                    </li>
                    <li>
                            <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                                <img border="0" src="{php}echo($this->image_dir);{/php}publish_no.gif" title="Despublicar" alt="Despublicar"><br />Despublicar
                            </a>
                    </li>
                    <li>
                            <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
                                <img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="Publicar" alt="Publicar"><br />Publicar
                            </a>
                    </li>
                    <li>
                            <a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 1);" name="submit_mult" value="Archivar" title="Archivar">
                                    <img border="0" src="{php}echo($this->image_dir);{/php}archive.gif" title="Archivar" alt="Archivar" ><br />Archivar
                            </a>
                    </li>
                    <li>
                            <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button' );">
                                    <img id="select_button"  status="0"  class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo">
                            </button>
                    </li>
                    <li>
                            <a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva opinion');" accesskey="N" tabindex="1">
                                <img border="0" src="{php}echo($this->image_dir);{/php}pc_opinion.png" title="Nueva Opinion" alt="Nueva Opinion"><br />Nueva Opinion
                            </a>
                    </li>
            </ul>
	</div>
	
{elseif preg_match('/pc_opinion\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read")) }
	<div id="menu-acciones-admin">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'validate', '{$opinion->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{php}echo($this->image_dir);{/php}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick='pc_cancel( "pc_opinion", {php} echo '"'.$_SESSION['pc_from'].'", "'.$_GET['category'].'", "'.$_GET['page'].'"';{/php});' onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{php}echo($this->image_dir);{/php}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($opinion->id) }
			   <a href="#" onClick="javascript:enviar(this, '_self', 'update', '{$opinion->id}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:enviar(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Guardar" alt="Guardar"><br />Guardar
				</a>
			</li>
		</ul>
	</div>

{* Botonera Plan Conecta Cartas -------------------------------------------- *}
{elseif preg_match('/pc_letter\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list") }

	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
				    <img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
				    <img border="0" src="{php}echo($this->image_dir);{/php}publish_no.gif" title="Despublicar" alt="Despublicar"><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
				    <img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="Publicar" alt="Publicar"><br />Publicar
				</a>
			</li>
                         <li>
				<a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 1);" name="submit_mult" value="Archivar" title="Archivar">
					<img border="0" src="{php}echo($this->image_dir);{/php}archive.gif" title="Archivar" alt="Archivar" ><br />Archivar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2' );">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva carta');" accesskey="N" tabindex="1">
				    <img border="0" src="{php}echo($this->image_dir);{/php}pc_letter.png" title="Nueva Carta" alt="Nueva Carta"><br />Nueva Carta
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/pc_letter\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read")) }
	<div id="menu-acciones-admin">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$letter->pk_pc_letter}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{php}echo($this->image_dir);{/php}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick='pc_cancel( "pc_letter",{php} echo '"'.$_SESSION['pc_from'].'", "'.$_GET['category'].'", "'.$_GET['page'].'"';{/php});' onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{php}echo($this->image_dir);{/php}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($letter->pk_pc_letter) }
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$letter->pk_pc_letter}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Guardar" alt="Guardar"><br />Guardar
				</a>
			</li>
		</ul>
	</div>

{* Botonera Plan Conecta Fotos -------------------------------------------- *}
{elseif preg_match('/pc_photo\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list") }

	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
				    <img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
				    <img border="0" src="{php}echo($this->image_dir);{/php}publish_no.gif" title="Despublicar" alt="Despublicar"><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
				    <img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="Publicar" alt="Publicar"><br />Publicar
				</a>
			</li>
                        <li>
				<a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 1);" name="submit_mult" value="Archivar" title="Archivar">
					<img border="0" src="{php}echo($this->image_dir);{/php}archive.gif" title="Archivar" alt="Archivar" ><br />Archivar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2' );">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva Foto');" accesskey="N" tabindex="1">
				    <img border="0" src="{php}echo($this->image_dir);{/php}pc_photo.png" title="Nueva Foto" alt="Nueva Foto"><br />Nueva Foto
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/pc_photo\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read")) }
	<div id="menu-acciones-admin">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$photo->pk_pc_photo}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{php}echo($this->image_dir);{/php}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick='pc_cancel( "pc_photo", {php} echo '"'.$_SESSION['pc_from'].'", "'.$_GET['category'].'", "'.$_GET['page'].'"';{/php});' onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{php}echo($this->image_dir);{/php}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($photo->pk_pc_photo) }
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$photo->pk_pc_photo}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Guardar" alt="Guardar"><br />Guardar
				</a>
			</li>
		</ul>
	</div>

{* Botonera Plan Conecta Videos -------------------------------------------- *}
{elseif preg_match('/pc_video\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list") }

	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
				    <img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
				    <img border="0" src="{php}echo($this->image_dir);{/php}publish_no.gif" title="Despublicar" alt="Despublicar"><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
				    <img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="Publicar" alt="Publicar"><br />Publicar
				</a>
			</li>
                         <li>
				<a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 1);" name="submit_mult" value="Archivar" title="Archivar">
					<img border="0" src="{php}echo($this->image_dir);{/php}archive.gif" title="Archivar" alt="Archivar" ><br />Archivar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2' );">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva Foto');" accesskey="N" tabindex="1">
				    <img border="0" src="{php}echo($this->image_dir);{/php}pc_video.png" title="Nuevo Video" alt="Nuevo Video"><br />Nuevo Video
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/pc_video\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read")) }
	<div id="menu-acciones-admin">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$video->pk_pc_video}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{php}echo($this->image_dir);{/php}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick='pc_cancel( "pc_video",{php} echo '"'.$_SESSION['pc_from'].'", "'.$_GET['category'].'", "'.$_GET['page'].'"';{/php});' onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{php}echo($this->image_dir);{/php}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($video->pk_pc_video) }
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$video->pk_pc_video}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Guardar" alt="Guardar"><br />Guardar
				</a>
			</li>
		</ul>
	</div>
	
{* Botonera polls ---------------------------------------------------------------------------------- *}
{elseif preg_match('/pc_poll\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list") }
	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{php}echo($this->image_dir);{/php}publish.gif" title="Frontpage" alt="Frontpage" ><br />Publicar
				</a>
			</li>
                         <li>
				<a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 1);" name="submit_mult" value="Archivar" title="Archivar">
					<img border="0" src="{php}echo($this->image_dir);{/php}archive.gif" title="Archivar" alt="Archivar" ><br />Archivar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"> 
				</button>
			</li>
			<li>
				<a class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva opinion');" accesskey="N" tabindex="1">
					<img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Nuevo" alt="Nuevo"><br />Nuevo
				</a>
			</li>
		</ul>
	</div>
	
{elseif preg_match('/pc_poll\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read")) }

	<div id="menu-acciones-admin">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$poll->pk_pc_poll}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{php}echo($this->image_dir);{/php}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick='pc_cancel( "pc_poll",{php} echo '"'.$_SESSION['pc_from'].'", "'.$_GET['category'].'", "'.$_GET['page'].'"';{/php});' onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{php}echo($this->image_dir);{/php}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($poll->pk_pc_poll) }
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$poll->pk_pc_poll}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Guardar" alt="Guardar"><br />Guardar
				</a>
			</li>
		</ul>
	</div>
	
{* Botonera Plan Conecta Secciones -------------------------------------------- *}
{elseif preg_match('/pc_sections\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list") }

	<div id="menu-acciones-admin">
		<div style="float:left"><b></b></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva Foto');" accesskey="N" tabindex="1">
				    <img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Nueva Seccion" alt="Nueva Foto"><br />Nueva Seccion
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:savePriority();" title="Guardar Positions" alt="Guardar Cambios">
				   <img border="0" src="{php}echo($this->image_dir);{/php}save.gif" title="Save Positions" alt="Guardar Cambios" ><br />Guardar Cambios
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/pc_sections\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read")) }
	<div id="menu-acciones-admin">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$category->pk_content_category}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{php}echo($this->image_dir);{/php}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', '{$category->pk_content_category}');" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{php}echo($this->image_dir);{/php}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($category->pk_content_category) }
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$category->pk_content_category}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{php}echo($this->image_dir);{/php}advertisement.png" title="Guardar" alt="Guardar"><br />Guardar
				</a>
			</li>
		</ul>
	</div>

{* Botonera Plan Conecta Usuarios -------------------------------------------- *}
{elseif preg_match('/pc_user\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list") }
<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add mdelete" name="submit_mult" value="Eliminar" title="Eliminar">
				    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>						
            
            <li class="separator"></li>
            
			<li>
				<a href="#" class="admin_add unsubscribe" accesskey="U">
					<img class="icon" src="{$params.IMAGE_DIR}subscription_0.png"
                         title="Desuscribir seleccionados" alt="Desuscribir seleccionados" border="0" height="50" /><br />
                    Desuscribir
                </a>
			</li>
            
			<li>
				<a href="#" class="admin_add subscribe" accesskey="S">
					<img class="icon" src="{$params.IMAGE_DIR}subscription_1.png"
                         title="Suscribir seleccionados" alt="Suscribir seleccionados" border="0" height="50" /><br />
                    Suscribir
                </a>
			</li>
            
            <li class="separator"></li>
            
            <li>
				<a href="#" class="admin_add checkall" accesskey="E">
					<img class="icon" src="{$params.IMAGE_DIR}deselect.png"
                         title="Seleccionar Todo" alt="Seleccionar Todo" border="0" height="50" /><br />
                    <span>Seleccionar todo</span>
                </a>
			</li>
            
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" accesskey="N">
				    <img border="0" src="{$params.IMAGE_DIR}authors_add.png" title="Nuevo Usuario" alt="Nuevo Usuario"><br />
                        Nuevo Usuario
				</a>
			</li>            
		</ul>
	</div>

{elseif preg_match('/pc_user\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read")) }
	<div id="menu-acciones-admin">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', '{$user->id}');" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($user->id) }
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$user->id}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar" alt="Guardar"><br />Guardar
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/pc_litter\.php/',$smarty.server.SCRIPT_NAME)}
	<div id="menu-acciones-admin">
		<ul>
			 <li>
                <a href="#" class="admin_add" onClick="javascript:enviar3(this, '_self', 'mremove', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
                    <img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" alt="Eliminar todos"><br />Eliminar todos
                </a>
            </li>
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
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;"onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2' );">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo">
				</button>
			</li>
		</ul>
    </div>
    
    	
{elseif preg_match('/pc_hemeroteca\.php/',$smarty.server.SCRIPT_NAME)}
	<div id="menu-acciones-admin">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{php}echo($this->image_dir);{/php}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_restore', 0);" onmouseover="return escape('Recuperar');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{php}echo($this->image_dir);{/php}archive_no.png" alt="recuperar"><br />Recuperar
                </a>
            </li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; height: 70px;"onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button2' );">
					<img id="select_button2" class="icon" src="{php}echo($this->image_dir);{/php}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo">
				</button>
			</li>
		</ul>
    </div>
    				
{/if}
<div id="msg" style="color:#BB1313;font-size:16px;font-weight:bold;padding:8px;">
{$msg} {$smarty.get.msg}
</div>