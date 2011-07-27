{* Botonera articulos crear/editar -------------------------------------------- *}
{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "new")}
<div class="top-action-bar">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Article manager :: Creating new article{/t}</h2></div>
		<ul class="old-button">
			<li>
				<a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'validate', '{$article->id}', 'formulario');" value="{t}Save and continue{/t}" title="{t}Save and continue{/t}">
				<img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="recolectar();sendFormValidate(this, '_self', 'create', 0, 'formulario');" id="button_save">
				<img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}" ><br />{t}Save and exit{/t}
				</a>
			</li>

			<li>
				<a href="{$article->permalink}" target="_blank" accesskey="P" onclick="recolectar(); previewArticle('','formulario','create'); return false;" id="button_preview">
				<img border="0" src="{$params.IMAGE_DIR}preview.png" title="{t}Preview{/t}" alt="{t}Preview{/t}" /><br />{t}Preview{/t}</a>
			</li>
			<li class="separator"></li>
			<li>
				<a href="{$smarty.server.PHP_SELF}?action={if isset($_SESSION['desde'])}{$_SESSION['desde']}{else}list_pendientes{/if}&category={$_REQUEST['category']}&page={$_GET['page']}" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
					<img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Preview{/t}" alt="{t}Cancel{/t}" ><br />{t}Go back{/t}
				</a>
			</li>

		</ul>
	</div>
</div>

{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "read")}
<div class="top-action-bar">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Article manager :: Editing article{/t}</h2></div>
		<ul class="old-button">
			<li>
                {if $smarty.session.desde eq 'search_advanced'  && isset($smarty.get.stringSearch)}
                     <a href="#" class="admin_add"  onClick="window.location='controllers/search_advanced/search_advanced.php?action=search&stringSearch={$smarty.get.stringSearch}';">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" ><br />{t}Cancel{/t}
                     </a>
                {else}
                    <a href="#" class="admin_add" onClick="cancel('{$smarty.session.desde}','{$smarty.request.category}','{$smarty.get.page}');" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
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
                    <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                </a>
		    </li>
            <li>
                <a href="#" class="admin_add" onClick="recolectar(); sendFormValidate(this, '_self', 'update', '{$article->id}', 'formulario');" id="button_save">
                    <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}" ><br />{t}Save and exit{/t}
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

{* agencias *}
{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list_agency")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}:: {$datos_cat[0]->title}{if empty($datos_cat[0]->title)} {$category|upper} {/if}</h2></div>
		<ul>
			 <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar todos"><br />Eliminar todos
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />Eliminar
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
{/if}
