{* Botonera articulos crear/editar -------------------------------------------- *}
{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "new")}
<div id="menu-acciones-admin" class="clearfix">
	<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Article manager :: Creating new article{/t}</h2></div>
	<ul>
		<li>
			<a href="{$smarty.server.PHP_SELF}?action={if isset($_SESSION['desde'])}{$_SESSION['desde']}{else}list_pendientes{/if}&category={$_REQUEST['category']}&page={$_GET['page']}" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
				<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="{t}Preview{/t}" alt="{t}Cancel{/t}" ><br />{t}Cancel{/t}
			</a>
		</li>
	<li>
		<a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'validate', '{$article->id}', 'formulario');" value="{t}Save and continue{/t}" title="{t}Save and continue{/t}">
		<img border="0" src="{$params.IMAGE_DIR}validate.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
		</a>
	</li>
	<li>
		<a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'create', 0, 'formulario');" id="button_save">
		<img border="0" src="{$params.IMAGE_DIR}save.gif" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}" ><br />{t}Save and exit{/t}
		</a>
	</li>

	<li>
		<a href="{$article->permalink}" target="_blank" accesskey="P" onclick="recolectar(); previewArticle('','formulario','create'); return false;" id="button_preview">
		<img border="0" src="{$params.IMAGE_DIR}preview.png" title="{t}Preview{/t}" alt="{t}Preview{/t}" /><br />{t}Preview{/t}</a>
		</li>
	</ul>
</div>

{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "read")}
	<div id="menu-acciones-admin" class="clearfix">
        {if  $article->content_status=='0' && $article->available=='1'}  <div style="float:left;"><h2>{t}Library{/t}</h2> </div>  {/if}
        <ul>
            <li>
                {if $smarty.session._from eq 'search_advanced'}
                     <a href="#" class="admin_add"  onClick="window.location='search_advanced.php?action=search&stringSearch={$smarty.get.stringSearch}'">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" ><br />{t}Cancel{/t}
                     </a>
                {else}
                    <a href="#" class="admin_add" onClick='cancel( {php} echo '"'.$_SESSION['desde'].'", "'.$_REQUEST['category'].'", "'.$_GET['page'].'"';{/php});' value="{t}Cancel{/t}" title="{t}Cancel{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" ><br />{t}Cancel{/t}
                    </a>
                {/if}
            </li>
            {if ($article->content_status eq 0) && ($article->available eq 1)}
            <li>
                <a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'restore', '{$article->id}', 'formulario');" onmouseover="return escape('Recuperar');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}archive_no.png" alt="{t}Restore{/t}"><br />{t}Restore{/t}
                </a>
            </li>
            {/if}

		    <li>
                <a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'validate', '{$article->id}', 'formulario');" value="{t}Save and continue{/t}" title="{t}Save and continue{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}validate.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                </a>
		    </li>
            <li>
                <a href="#" class="admin_add" onClick="recolectar(); sendFormValidate(this, '_self', 'update', '{$article->id}', 'formulario');" id="button_save">
                    <img border="0" src="{$params.IMAGE_DIR}save.gif" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}" ><br />{t}Save and exit{/t}
                </a>
            </li>

            {if !$article->isClone()}
            <li>
                <a href="#" onclick="recolectar(); sendFormValidate(this, '_blank', 'clone', '{$article->id}', 'formulario');" id="button_clone">
                    <img border="0" src="{$params.IMAGE_DIR}clone.png" title="{t}Clone{/t}" /><br />{t}Clone{/t}
                </a>
            </li>
            {/if}

            <li>
                <a href="{$article->permalink}" target="_blank" accesskey="P" onmouseover="return escape('<u>P</u>revisualizar');" onclick="recolectar(); previewArticle('{$article->id}','formulario','update'); return false;" id="button_preview">
                    <img border="0" src="{$params.IMAGE_DIR}preview.png" title="Previsualizar" alt="{t}Preview{/t}" /><br />{t}Preview{/t}
                </a>
            </li>
		</ul>
	</div>

{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "only_read")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Library{/t} :: {t}Seeing article{/t}</h2></div>
		<ul>
			<li>
				<a href="{$smarty.server.PHP_SELF}?action={$smarty.session.desde}&category={$smarty.get.category}&page={$_GET['page']}" title="{t}Go back{/t}">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
				</a>
			</li>

		</ul>
	</div>

{* Botonera articles -------------------------------------------- *}
{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Frontpage Manager{/t} :: {if $category eq 0}{t}HOME{/t}{else}{$datos_cat[0]->title}{/if}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="{t}Delete{/t}" title="{t}Delete{/t}">
					<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
					<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="noFrontpage" ><br />{t}Unpublish{/t}
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:confirmar_hemeroteca(this,{$category}, 0);" name="submit_mult" value="Archivar" title="Archivar">
					<img border="0" src="{$params.IMAGE_DIR}archive.gif" title="{t}Arquive{/t}" alt="{t}Arquive{/t}" ><br />{t}Arquive{/t}
				</a>
			</li>
			{if $category!='home'}
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 2);" name="submit_mult" value="Frontpage" title="Sugerir Home">
					<img border="0" src="{$params.IMAGE_DIR}gosuggest50.png" title="{t}Suggest to home{/t}" alt="{t}Suggest to home{/t}" ><br />{t}Suggest to home{/t}
				</a>
			</li>
			{/if}
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 0);" name="submit_mult" value="Frontpage" title="Frontpage">
					<img border="0" src="{$params.IMAGE_DIR}home_no50.png" title="{t}No home{/t}" alt="Frontpage" ><br />{t}No home{/t}
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" status="0" src="{$params.IMAGE_DIR}select_button.png" title="{t}Select all{/t}" alt="{t}Select all{/t}"  status="0">
				</button>
			</li>
			<li style="margin-right: 20px;">
				<a href="#" class="admin_add" onClick="javascript:savePositions('{$category}');" title="Guardar Positions" alt="Guardar Cambios">
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="{t}Save changes{/t}" alt="{t}Save changes{/t}" ><br />{t}Save changes{/t}
				</a>
			</li>
                        <li style="height:70px;">
				<a href="#" class="admin_add" onClick="javascript:previewFrontpage('{$category}');return false;" title="Previsualizar posiciones en portada">
					<img border="0" src="{$params.IMAGE_DIR}preview_layout.png" title="{t}Preview{/t}" alt="{t}Preview{/t}" ><br />{t}Preview{/t}
				</a>
			</li>
            <li style="height:70px;">
                 <a href="#" onclick="clearcache('{$category}'); return false;" id="button_clearcache">
                     <img border="0" src="{$params.IMAGE_DIR}clearcache.png" title="{t}Clean cache{/t}" alt="" /><br />{t}Clean cache{/t}
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
                    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar todos"><br />{t}Delete all{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar"><br />{t}Delete{/t}
                </a>
            </li>
            {if $category!=20}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" onmouseover="return escape('<u>P</u>ublicar');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish.gif" alt="noFrontpage"><br />{t}Publish{/t}
                </a>
            </li>
             <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdirectly_frontpage', 1);" onmouseover="return escape('<u>P</u>ublicar directamente en portada');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish_direct.gif" alt="publicar en portada directamente"><br />{t}Publish to frontpage{/t}
                </a>
            </li>
            {/if}
            <li>
                <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                </button>
            </li>
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add">
                    <img border="0" src="{$params.IMAGE_DIR}/article_add.gif" alt="Nuevo"><br />{t}New article{/t}
                </a>
            </li>
            <li>
                <a href="importXML.php" class="admin_add"  onmouseover="return escape('<u>I</u>mportar XML');" name="submit_mult" value="Importar">
                    <img border="0" src="{$params.IMAGE_DIR}xml.png" alt="Importar"><br />{t}Import XML{/t}
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
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}::&nbsp;{if $category eq 'todos'} {$category|upper} {else}{$datos_cat[0]->title} {/if}</h2></div>
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
            <li>
                <a title="Advanced Search" tabindex="1" accesskey="N" class="admin_add" href="{$smarty.const.SITE_URL}admin/search_advanced.php">
                <img border="0" alt="Advanced Search" title="Advanced Search" src="{$smarty.const.SITE_URL}/admin/themes/default/images/search.png"><br>Search
                </a>
            </li>
		</ul>
	</div>
{/if}
