{* Botonera articulos crear/editar -------------------------------------------- *}
{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "new")}
<div class="top-action-bar">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Article manager{/t} :: {t}Creating new article{/t}</h2></div>
		<ul class="old-button">
            {acl isAllowed="ARTICLE_UPDATE"}
			<li>
				<a href="#" class="admin_add" id="validate-button" onClick="save_related_contents();sendFormValidate(this, '_self', 'validate', '{$article->id|default:""}', 'formulario');" title="{t}Save and continue{/t}">
				<img src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
				</a>
			</li>
            {/acl}
            {acl isAllowed="ARTICLE_CREATE"}
			<li>
				<a href="#" class="admin_add" id="save-button" onClick="save_related_contents();sendFormValidate(this, '_self', 'create', 0, 'formulario');" >
				<img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}" ><br />{t}Save and exit{/t}
				</a>
			</li>
             {/acl}
            {acl isAllowed="ARTICLE_UPDATE"}
			<li>
				<a href="#"  accesskey="P" id="button_preview">
				    <img src="{$params.IMAGE_DIR}preview.png" title="{t}Preview{/t}" alt="{t}Preview{/t}" /><br />{t}Preview{/t}
                </a>
			</li>
			<li class="separator"></li>
			<li>
				<a href="{if isset($smarty.session.desde) && ($smarty.session.desde neq 'list')}{$smarty.server.PHP_SELF}?action={$smarty.session.desde|default:"list_pendientes"}{else}controllers/frontpagemanager/frontpagemanager.php?action=list{/if}&amp;category={$smarty.request.category|default:"todas"}&amp;page={$smarty.get.page|default:0}" title="{t}Cancel{/t}">
					<img src="{$params.IMAGE_DIR}previous.png" title="{t}Preview{/t}" alt="{t}Cancel{/t}" ><br />{t}Go back{/t}
				</a>
			</li>
            {/acl}
		</ul>
	</div>
</div>

{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "read")}
<div class="top-action-bar">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Article manager{/t} :: {t}Editing article{/t}</h2></div>
		<ul class="old-button">
			{if ($article->content_status eq 0) && ($article->available eq 1)}
            <li>
                <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'restore', '{$article->id|default:""}', 'formulario');" onmouseover="return escape('Recuperar');" name="submit_mult" value="noFrontpage">
                    <img src="{$params.IMAGE_DIR}archive_no.png" alt="{t}Restore{/t}"><br />{t}Restore{/t}
                </a>
            </li>
            {/if}

            {acl isAllowed="ARTICLE_UPDATE"}
		    <li>
                <a href="#" class="admin_add" id="save-button" onClick="save_related_contents();sendFormValidate(this, '_self', 'validate', '{$article->id|default:""}', 'formulario');" title="{t}Save and continue{/t}">
                    <img src="{$params.IMAGE_DIR}save_and_continue.png" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                </a>
		    </li>
             {/acl}
            {acl isAllowed="ARTICLE_UPDATE"}
            <li>
                <a href="#" class="admin_add" id="validate-button" onClick="save_related_contents();sendFormValidate(this, '_self', 'update', '{$article->id|default:""}', 'formulario');" id="button_save">
                    <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save and exit{/t}" ><br />{t}Save and exit{/t}
                </a>
            </li>
            {/acl}

            <li>
                <a href="#" accesskey="P" id="button_preview">
                    <img src="{$params.IMAGE_DIR}preview.png" alt="{t}Preview{/t}" /><br />{t}Preview{/t}
                </a>
            </li>
			<li class="separator"></li>
			<li>
                {if $smarty.session.desde eq 'search_advanced'  && isset($smarty.get.stringSearch)}
                     <a href="#" class="admin_add"  onClick="window.location='controllers/search_advanced/search_advanced.php?action=search&amp;stringSearch={$smarty.get.stringSearch}';">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Cancel{/t}" ><br />{t}Go back{/t}
					</a>
                {else}
                    {if $smarty.session.desde eq 'europa_press_import'}
                        <a href="/admin/controllers/agency_importer/europapress.php" title="{t}Cancel{/t}">
                            <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Cancel{/t}" ><br />{t}Go back{/t}
                        </a>
                    {else}
                        {if $smarty.session.desde eq 'efe_press_import'}
                            <a href="/admin/controllers/agency_importer/efe.php" title="{t}Cancel{/t}">
                                <img src="{$params.IMAGE_DIR}previous.png"  alt="{t}Cancel{/t}" ><br />{t}Go back{/t}
                            </a>
                        {else}
                             {if $smarty.session.desde eq 'list'}
                                 <a href="controllers/frontpagemanager/frontpagemanager.php?action={$smarty.session.desde|default:"list_pendientes"}&amp;category={$smarty.request.category|default:""}&amp;page={$smarty.get.page|default:""}" title="{t}Cancel{/t}">
                                    <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Cancel{/t}" ><br />{t}Go back{/t}
                                </a>
                             {else}
                                <a href="{$smarty.server.PHP_SELF}?action={$smarty.session.desde|default:"list_pendientes"}&amp;category={$smarty.request.category|default:""}&amp;page={$smarty.get.page|default:""}" title="{t}Cancel{/t}">
                                    <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Cancel{/t}" ><br />{t}Go back{/t}
                                </a>
                             {/if}
                        {/if}
                    {/if}
                {/if}
            </li>
		</ul>
	</div>
</div>

{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "only_read")}
	<div class="top-action-bar clearfix">
		<div class="title"><h2>{t}Library{/t} :: {t}Seeing article{/t}</h2></div>
		<ul class="old-button">
			<li>
				<a href="{$smarty.server.PHP_SELF}?action={$smarty.session.desde}&amp;category={$smarty.get.category}&amp;page={$_GET['page']}" title="{t}Go back{/t}">
					<img src="{$params.IMAGE_DIR}cancel.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
				</a>
			</li>

		</ul>
	</div>

{* agencias *}
{elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "list_agency")}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}:: {$datos_cat[0]->title}{if empty($datos_cat[0]->title)} {$category|upper} {/if}</h2></div>
		<ul>

            {acl isAllowed="ARTICLE_DELETE"}
			 <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  onmouseover="return escape('<u>E</u>liminar todos');">
                    <img src="{$params.IMAGE_DIR}trash.png" alt="Eliminar todos"><br />Eliminar todos
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');">
                    <img src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />Eliminar
                </a>
            </li>
             {/acl}
            {acl isAllowed="ARTICLE_AVAILABLE"}
            {if $category!=20}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" onmouseover="return escape('<u>P</u>ublicar');">
                    <img src="{$params.IMAGE_DIR}publish.gif" alt="noFrontpage"><br />Publicar
                </a>
            </li>
            {/if}
            {/acl}
            <li>
                <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                </button>
            </li>
            {acl isAllowed="IMPORT_ADMIN"}
            <li>
                <a href="importAgencyXML.php" class="admin_add"  onmouseover="return escape('<u>I</u>mportar XML');" name="submit_mult" value="Importar">
                    <img src="{$params.IMAGE_DIR}xml.png" alt="Importar"><br />Importar Agencia XML
                </a>
            </li>
            {/acl}
		</ul>
	</div>
{/if}
