{* Botonera articulos crear/editar -------------------------------------------- *}
{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "new")}
<div id="menu-acciones-admin" class="clearfix">
	<ul>
		<li>
			<a href="#" class="admin_add" onClick='cancel( {php} echo '"'.$_SESSION['desde'].'", "'.$_REQUEST['category'].'", "'.$_GET['page'].'"';{/php});' value="Cancelar" title="Cancelar">
				<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
			</a>
		</li>
	<li>
		<a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'validate', '{$article->id}', 'formulario');" value="Validar" title="Validar">
		<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
		</a>
	</li>
	<li>
		<a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'create', 0, 'formulario');" id="button_save">
		<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir" ><br />Guardar y salir
		</a>
	</li>

	<li>
		<a href="{$article->permalink}" target="_blank" accesskey="P" onmouseover="return escape('<u>P</u>revisualizar');" onclick="recolectar(); previewArticle('','formulario','create'); return false;" id="button_preview">
		<img border="0" src="{$params.IMAGE_DIR}preview.png" title="Previsualizar" alt="Previsualizar" /><br />Previsualizar</a>
		</li>
	</ul>
</div>

{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "read")}
	<div id="menu-acciones-admin" class="clearfix">
        {if  $article->content_status=='0' && $article->available=='1'}  <div style="float:left;"><h2>HEMEROTECA</h2> </div>  {/if}
        <ul>
            <li>
                {if $smarty.session._from eq 'search_advanced'}
                     <a href="#" class="admin_add"  onClick="window.location='search_advanced.php?action=search&stringSearch={$smarty.get.stringSearch}'">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                     </a>
                {else}
                    <a href="#" class="admin_add" onClick='cancel( {php} echo '"'.$_SESSION['desde'].'", "'.$_REQUEST['category'].'", "'.$_GET['page'].'"';{/php});' value="Cancelar" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                    </a>
                {/if}
            </li>
            {if ($article->content_status eq 0) && ($article->available eq 1)}
            <li>
                <a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'restore', '{$article->id}', 'formulario');" onmouseover="return escape('Recuperar');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}archive_no.png" alt="recuperar"><br />Recuperar
                </a>
            </li>
            {/if}

		    <li>
                <a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'validate', '{$article->id}', 'formulario');" value="Validar" title="Validar">
                    <img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
                </a>
		    </li>
            <li>
                <a href="#" class="admin_add" onClick="recolectar(); sendFormValidate(this, '_self', 'update', '{$article->id}', 'formulario');" id="button_save">
                    <img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir" ><br />Guardar y salir
                </a>
            </li>

            {if !$article->isClone()}
            <li>
                <a href="#" onclick="recolectar(); sendFormValidate(this, '_blank', 'clone', '{$article->id}', 'formulario');" id="button_clone">
                    <img border="0" src="{$params.IMAGE_DIR}clone.png" title="Clonar" /><br />Clonar
                </a>
            </li>
            {/if}

            <li>
                <a href="{$article->permalink}" target="_blank" accesskey="P" onmouseover="return escape('<u>P</u>revisualizar');" onclick="recolectar(); previewArticle('{$article->id}','formulario','update'); return false;" id="button_preview">
                    <img border="0" src="{$params.IMAGE_DIR}preview.png" title="Previsualizar" alt="Previsualizar" /><br />Previsualizar
                </a>
            </li>
		</ul>
	</div>

{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "only_read")}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="cancel( {php} echo '"'.$_SESSION['desde'].'", "'.$_GET['category'].'", "'.$_GET['page'].'"';{/php});'" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>

		</ul>
	</div>


{* Botonera articles -------------------------------------------- *}
{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
	<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}::&nbsp;{$datos_cat[0]->title}{if $category eq 0}HOME{/if}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 0);" name="submit_mult" value="Archivar" title="Archivar">
					<img border="0" src="{$params.IMAGE_DIR}archive.gif" title="Archivar" alt="Archivar" ><br />Archivar
				</a>
			</li>
			{if $category!='home'}
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 2);" name="submit_mult" value="Frontpage" title="Sugerir Home">
					<img border="0" src="{$params.IMAGE_DIR}gosuggest50.png" title="Sugerir Home" alt="Sugerir Home" ><br />Sugerir Home
				</a>
			</li>
			{/if}
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 0);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}home_no50.png" title="Frontpage" alt="Frontpage" ><br />No Home
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" status="0" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
				</button>
			</li>
			<li style="margin-right: 20px;">
				<a href="#" class="admin_add" onClick="javascript:savePositions('{$category}');" title="Guardar Positions" alt="Guardar Cambios">
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar Cambios" alt="Guardar Cambios" ><br />Guardar posiciones
				</a>
			</li>
                        <li style="height:70px;">
				<a href="#" class="admin_add" onClick="javascript:previewFrontpage('{$category}');return false;" title="Previsualizar posiciones en portada">
					<img border="0" src="{$params.IMAGE_DIR}preview_layout.png" title="Previsualizar" alt="Previsualizar" ><br />Previsualizar
				</a>
			</li>
            <li style="height:70px;">
                 <a href="#" onclick="clearcache('{$category}'); return false;" id="button_clearcache">
                     <img border="0" src="{$params.IMAGE_DIR}clearcache.png" title="Limpiar caché" alt="" /><br />Limpiar caché
                 </a>
            </li>
		</ul>
	</div>

{* pendientes *}
{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list_pendientes")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}:: {$datos_cat[0]->title}{if empty($datos_cat[0]->title)} {$category|upper} {/if}</h2></div>
		<ul>
			 <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
                    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar todos"><br />Eliminar todos
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar"><br />Eliminar
                </a>
            </li>
            {if $category!=20}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" onmouseover="return escape('<u>P</u>ublicar');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish.gif" alt="noFrontpage"><br />Publicar
                </a>
            </li>
             <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdirectly_frontpage', 1);" onmouseover="return escape('<u>P</u>ublicar directamente en portada');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish_direct.gif" alt="publicar en portada directamente"><br />Publicar a portada
                </a>
            </li>
            {/if}
            <li>
                <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                </button>
            </li>
            <li>
                <a  onclick="enviar(this, '_self', 'new', 0);" class="admin_add" onmouseover="return escape('<u>N</u>ueva noticia');">
                    <img border="0" src="{$params.IMAGE_DIR}/article_add.gif" alt="Nuevo"><br />Nueva noticia
                </a>
            </li>
            <li>
                <a href="importXML.php" class="admin_add"  onmouseover="return escape('<u>I</u>mportar XML');" name="submit_mult" value="Importar">
                    <img border="0" src="{$params.IMAGE_DIR}xml.png" alt="Importar"><br />Importar XML
                </a>
            </li>
		</ul>
	</div>


{* agencias *}
{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list_agency")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}:: {$datos_cat[0]->title}{if empty($datos_cat[0]->title)} {$category|upper} {/if}</h2></div>
		<ul>
			 <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
                    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar todos"><br />Eliminar todos
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar"><br />Eliminar
                </a>
            </li>
            {if $category!=20}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" onmouseover="return escape('<u>P</u>ublicar');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish.gif" alt="noFrontpage"><br />Publicar
                </a>
            </li>

            {/if}
            <li>
                <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                </button>
            </li>

            <li>
                <a href="importAgencyXML.php" class="admin_add"  onmouseover="return escape('<u>I</u>mportar XML');" name="submit_mult" value="Importar">
                    <img border="0" src="{$params.IMAGE_DIR}xml.png" alt="Importar"><br />Importar Agencia XML
                </a>
            </li>
		</ul>
	</div>

{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list_hemeroteca")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}::&nbsp;{$datos_cat[0]->title}{if empty($datos_cat[0]->title)} {$category|upper} {/if}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
				</a>
			</li>
            <li>
                <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                </button>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_restore', 1);" onmouseover="return escape('Recuperar');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}archive_no.png" alt="recuperar"><br />Recuperar
                </a>
            </li>
		</ul>
	</div>

{* Botonera comentarios ---------------------------------------------------------------------------------- *}
{elseif preg_match('/comment\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}::&nbsp;{$datos_cat[0]->title}{if empty($datos_cat[0]->title)} {if $category==4} OPINION {else} {$category|upper}{/if}{/if}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 2);" name="submit_mult" value="noFrontpage" title="Rechazar">
					<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="Rechazar" alt="Rechazar" ><br />Rechazar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Publicar">
					<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
				</button>
			</li>
		</ul>
	</div>

{elseif preg_match('/comment\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "read")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left'><h2>&nbsp;{$datos_cat[0]->title}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'update', '{$comment->id}');">
					<img border="0" src="{$params.IMAGE_DIR}save.gif" ="Guardar y salir" alt="Guardar y salir" ><br />Guardar y salir
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="confirmar(this, '{$comment->id}');">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
				</a>
			</li>
			<li>
				{if $comment->content_status == 1}
					<a href="?id={$comment->id}&amp;action=change_status&amp;status=0&amp;category={$comment->category}" title="Publicar">
						<img src="{$params.IMAGE_DIR}publish_no.gif" border="0" alt="Publicado" /><br />Despublicar
					</a>
				{else}
					<a href="?id={$comment->id}&amp;action=change_status&amp;status=1&amp;category={$comment->category}" title="Despublicar">
						<img src="{$params.IMAGE_DIR}publish.gif" border="0" alt="Pendiente" /><br />Publicar
					</a>
				{/if}
			</li>
			 <li>
                <a href="#" class="admin_add" rel="iframe" onmouseover="return escape('<u>V</u>er Noticia');" onclick="preview(this, '{$article->category}','{$article->subcategory}','{$article->id}');">
                    <img border="0" src="{$params.IMAGE_DIR}preview.png" title="Ver Noticia" alt="Ver Noticia" ><br />Ver noticia
                </a>
			</li>
		</ul>
	</div>

{* Botonera opinion -------------------------------------------- *}
{elseif preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}:: {$accion}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);return false;" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);return false;" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Frontpage" alt="Frontpage" ><br />Publicar
				</a>
			</li>
			 {if $type_opinion neq '-1'}
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 1);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}gohome50.png"  title="Frontpage" alt="Frontpage" ><br />En Home
				</a>
			</li>
			{/if}
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 0);return false;" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}home_no50.png"  title="Frontpage" alt="Frontpage" ><br />No Home
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
				</button>
			</li>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva opinion');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}opinion.png" title="Nuevo" alt="Nuevo"><br />Nuevo
				</a>
			</li>
			 {if $type_opinion eq '-1'}
				<li>
					<a href="#" class="admin_add" onClick="javascript:savePositionsOpinion();" title="Guardar Positions" alt="Guardar Posiciones">
						<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar Cambios" alt="Guardar Posiciones"><br />Guardar Posiciones
					</a>
				</li>
			{/if}
             <li class="separator"> </li>
            <li>
				<a href="author.php?action=new&desde=opinion" class="admin_add" name="submit_mult" value="Nuevo Autor" title="Nuevo Autor">
					<img border="0" src="{$params.IMAGE_DIR}authors_add.png" title="Nuevo Autor" alt="Nuevo Autor"><br />Nuevo Autor
				</a>
			</li>

            <li >
				<a href="author.php?action=list&desde=opinion" class="admin_add" name="submit_mult" value="Listado Autores" title="Listado Autores">
					<img border="0" src="{$params.IMAGE_DIR}authors.png" title="Listado Autores" alt="Listado Autores"><br />Ver Autores
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div style="float:left"></div>
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
			<li>
                        {if $smarty.session._from eq 'search_advanced'}
                             <a href="#" class="admin_add"  onClick="window.location='search_advanced.php?action=search&stringSearch={$smarty.get.stringSearch}'">
                                <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                             </a>
                        {else}
				<a href="#" class="admin_add" onClick='cancel( {php} echo '"'.$_SESSION['desde'].'", "'.$_REQUEST['category'].'", "'.$_GET['page'].'"';{/php});' value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
                        {/if}
		    </li>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$opinion->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
                {if isset($opinion->id)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$opinion->id}', 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
                {/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar y salir</a>
			</li>
		</ul>
	</div>
{* Botonera advertisement -------------------------------------------- *}
{elseif preg_match('/advertisement\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
   <div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}::&nbsp;{$datos_cat[0]->title} {if $category eq 0}HOME{/if}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Frontpage" alt="Frontpage" ><br />Publicar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
				</button>
			</li>

			<li>
				<a class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva opinion');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}advertisement.png" title="Nuevo" alt="Nuevo"><br />Nuevo
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/advertisement\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
			 <li>
				<a href="#" class="admin_add" onClick='cancel( {php} echo '"'.$_SESSION['desde'].'", "'.$_REQUEST['category'].'", "'.$_GET['page'].'"';{/php});' value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
		    </li>
			<li>
			{if isset($advertisement->id)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$advertisement->id}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar y salir
				</a>
			</li>
		</ul>
	</div>

{* Botonera category -------------------------------------------- *}
{elseif preg_match('/category\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	{if $type eq "list"}
		<div id="menu-acciones-admin" class="clearfix">
			<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
			<ul>
				<li>
					<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<b>N</b>ueva Seccion');" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}advertisement.png" title="Nueva" alt="Nueva"><br />Nueva Sección
					</a>
				</li>
			</ul>
		</div>
	{elseif $type eq "order"}
		<div id="menu-acciones-admin" class="clearfix">
			<div style="float:left"><b><em>DRAG &amp; DROP: pinche y arrastre las filas para determinar el orden en el menú de las secciones</em></b></div>
			<ul>
				<li>
					<a href="#" class="admin_add" onClick="javascript:savePriority();" title="Guardar Positions" alt="Guardar Cambios">
						<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar Cambios" alt="Guardar Cambios"><br />Guardar Cambios
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva sección');" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}advertisement.png" title="Nueva Sección" alt="Nueva Sección"><br />Nueva Sección
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
				<a href="#" class="admin_add" onClick="enviar(this, '_self', '{$_SESSION['desde']}', 0);" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
		</ul>
	</div>

{* Botonera autores -------------------------------------------- *}
{elseif preg_match('/author\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</div>
		<ul>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva publicidad');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}authors_add.png" title="Nuevo" alt="Nuevo"><br />Nuevo Autor
				</a>
			</li>
             <li class="separator"> </li>
            <li>
				<a href="opinion.php?action=new&desde=author" class="admin_add" name="submit_mult" value="Nueva Opinion" title="Nueva Opinion">
					<img border="0" src="{$params.IMAGE_DIR}opinion.png" title="Nueva Opinion" alt="Nueva Opinion"><br />Nueva Opinion
				</a>
			</li>

            <li >
				<a href="opinion.php?action=list&desde=author" class="admin_add" name="submit_mult" value="Listado Opiniones" title="Listado Opiniones">
					<img border="0" src="{$params.IMAGE_DIR}opinion.png" title="Listado Opiniones" alt="Listado Opiniones"><br />Ver Opiniones
				</a>
			</li>
		</ul>

	</div>

{elseif preg_match('/author\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$author->pk_author}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}user_validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				{if $smarty.session._from eq 'opinion.php'}
						<a href="#" class="admin_add"  onClick="window.location='opinion.php'">
						<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
					</a>
				{else}
					<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
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

{* Botonera usuarios -------------------------------------------- *}
{elseif preg_match('/user\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
		<ul>
            <li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todos" status="0">
				</button>
			</li>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>uevo usuario');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}user_add.png" title="Nuevo" alt="Nuevo"><br />Nuevo Usuario
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/user\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}user_validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Save and continue
				</a>
		    </li>

			<li>
			{if isset($user->id)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$user->id}, 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0, 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Save
				</a>
			</li>
            <li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancel
				</a>
			</li>

		</ul>
	</div>

{* Botonera grupos de usuarios -------------------------------------------- *}
{elseif preg_match('/user_groups\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>uevo grupo de usuarios');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}group_add.png" title="Nuevo" alt="Nuevo"><br />Nuevo grupo de Usuarios
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/user_groups\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user_group->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}user_validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($user_group->id)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$user_group->id}, 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0,'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/privileges\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$privilege->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($privilege->id)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$privilege->id}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
		</ul>
	</div>



{* Botonera Search_avanced ----------------------------------------------- *}
 {elseif preg_match('/search_advanced\.php/',$smarty.server.SCRIPT_NAME) && ((!isset($smarty.request.action)) || ($smarty.request.action neq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'search', 0);" onmouseover="return escape('<u>S</u>earch');" accesskey="N" tabindex="1" title="Search">
					<img border="0" src="{$params.IMAGE_DIR}search.png" title="Search" alt="Search"><br />Search
				</a>
			</li>
		</ul>
	</div>

 {elseif preg_match('/search_advanced\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "read")}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'search',0);" onmouseover="return escape('<u>S</u>earch');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancel" alt="Search"><br />Cancelar
				</a>
			</li>
		</ul>
	</div>


{* Botonera album -------------------------------------------- *}
{elseif preg_match('/album\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
    <div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todos" status="0">
				</button>
			</li>
			<li>
				<a href="#" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>uevo Album');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}/album.png" title="Nuevo Album" alt="Nuevo Album"><br />Nuevo Album
				</a>
			</li>
		</ul>
	</div>

 {elseif preg_match('/album\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
		    <li>
				<a class="admin_add" onClick="album_get_order(); if(check_crop()) enviar(this, '_self', 'validate', '{$album->id}');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
			{if isset($album->id)}
			   <a onClick="javascript:album_get_order(); if(check_crop()) enviar(this, '_self', 'update', '{$album->id}');">
			{else}
			   <a onClick="javascript: album_get_order(); if(check_crop()) enviar(this, '_self', 'create', '0');">
			{/if}
                                <img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
                            </a>
			</li>
		</ul>
	</div>
 {* Botonera video -------------------------------------------- *}
{elseif preg_match('/video\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
        <ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
				</button>
			</li>
			<li>
				<a href="#" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva carta');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}/video.png" title="Nuevo Video" alt="Nuevo Video"><br />Nuevo Video
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/video\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div style="float:left"></div>
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$video->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($video->id)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$video->id}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
		</ul>
	</div>
 {* Botonera polls -------------------------------------------- *}
{elseif preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
        <ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
				</button>
			</li>
			<li>
				<a href="#" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva carta');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}/polls.png" title="Nueva encuesta" alt="Nuevo Encuesta"><br />Nueva Encuesta
				</a>
			</li>
		</ul>
	</div>

{elseif preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
	<div id="menu-acciones-admin" class="clearfix">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$poll->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($poll->id)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$poll->id}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
		</ul>
	</div>


{* Botonera category -------------------------------------------- *}
{elseif preg_match('/kiosko\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}

    <div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
		<ul>
            <li>
                <a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva portada');" accesskey="N" tabindex="1">
                    <img border="0" src="{$params.IMAGE_DIR}add_kiosko.gif" title="Nueva" alt="Nueva"><br />Nueva Portada
                </a>
            </li>
        </ul>
    </div>
{elseif preg_match('/kiosko\.php/',$smarty.server.SCRIPT_NAME) && (($smarty.request.action eq "new")||($smarty.request.action eq "read"))}
    <div id="menu-acciones-admin" class="clearfix">
        <ul>
            <li>
                <a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
                    <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                </a>
            </li>
            <li>
            {if isset($kiosko->id)}
                <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$kiosko->id}', 'formulario');">
            {else}
                <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
            {/if}
                    <img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
                </a>
            </li>
        </ul>
    </div>
{/if}
