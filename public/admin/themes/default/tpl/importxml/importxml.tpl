{extends file="base/admin.tpl"}

{block name="content"}
<!--form id="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=addFile" method="POST" enctype="multipart/form-data"-->
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">

	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Import articles from paper{/t}</h2></div>
			<ul class="old-button">
				<li>
					<a href="article.php?action=list_pendientes&category=todos" value="Cancelar" title="Cancelar">
						<img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
					</a>
				</li>
				<li>
					<a href="#" class="admin_add"  onclick="enviar(this, '_self', 'check', 0);" onmouseover="return escape('<u>C</u>heck');" name="check" value="check">
						<img border="0" src="{$params.IMAGE_DIR}checkout.png" alt="Importar"><br />{t}Check{/t}
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onclick="enviar(this, '_self', 'import', 0);" onmouseover="return escape('<u>I</u>mportar XML');" name="import" value="import">
						<img border="0" src="{$params.IMAGE_DIR}checkout.png" alt="Importar"><br />{t}Import{/t}
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onclick="delFile()" onmouseover="return escape('<u>R</u>emove File');" name="remove" value="remove">
						<img border="0" src="{$params.IMAGE_DIR}list-remove.png" alt="Remove"><br />{t}Remove File{/t}
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onclick="addFile();" onmouseover="return escape('<u>A</u>dd File');" name="add" value="add">
						<img border="0" src="{$params.IMAGE_DIR}list-add.png" alt="Add"><br />{t}Add File{/t}
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="wrapper-content">
	<div>
		<table class="adminheading">
			<tr>
				<th nowrap>{t}Files to import{/t}</th>
			</tr>
		</table>
		<table class="adminlist">
			<tr><td colspan="2"><br />
				<div id="FileContainer" style="margin:0 auto; display:block; float:none;">
					<h3>Select a XML or a set of XML Files to import</h3>
					<div class="marcoFoto" id="File0">
						<p style="font-weight: bold;">
							<input style="border:none;" type="file" name="file[0]" id="fFile0" class="required" size="50" onChange="ckeckName(this,'fileCat[0]');"/>
							<span style="width:240px; vertical-align:middle;" title="Si activa esto las noticias serán importadas en la cola de pendientes. Por el contrario se importarán automáticamente y estarán disponibles."> <input type="checkbox"  id="check_pendientes[0]" checked="checked" name="check_pendientes[0]" value="1"  style="cursor:pointer; vertical-align:middle;">Importar a pendientes</span>
							<div id="fileCat[0]" name="fileCat[0]" style="display:none;">
								<table border='0' bgcolor='red'   cellpadding='4'>
									<tr><td>El nombre del ficheiro es incorrecto. No puede contener espacios en blanco o caracteres especiales (*, /, ~, etc.).</td></tr>
								</table>
							</div>
						</p>
					</div>
			  </div>
			  <p>&nbsp;</p>
			  <div id="fotosContenedor"></div>
			</td></tr>
		</table><br />
		{if isset($dataXML) && !empty($dataXML)}
			{if isset($action) && $action eq 'check'}
				<h2>Checking XML files ...</h2>
			{else}
				<h2>Importing XML files ...</h2>
			{/if}
			<br />
		<pre style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0;margin:0.5em 1em;overflow:auto;">
			<div style="float:right;width:200px">
				<span><b>Número total de ficheros: {$total_num}</b></span>
				<table style="width: 100px;" align="center">
				{foreach from=$numCategories key=k item=i}
					{if ($i != '0')}<tr><td><b>{$k}: </b></td><td>{$i}</td></tr>{/if}
				{/foreach}
				</table>
			</div>

			{foreach from=$dataXML item=article name=articl}
			<table>
				<tr>
					<td colspan="2">
						<h3>{$XMLFile[$smarty.foreach.articl.index]}</h3>
					</td>
				</tr>
				{if !empty($article->autor_nombre)}<tr><td><b>Autor: </b></td><td>{$article->autor_nombre}</td></tr>{/if}
				{if !empty($article->titulo)}<tr style="color:blue;font-size:16px;font-weight:700"><td>Titulo: </td><td>{$article->titulo|truncate:50}</td></tr>{/if}
				{if !empty($article->entradilla)}<tr><td><b>Entradilla: </b></td><td>{$article->entradilla|truncate:100}</td></tr>{/if}
				{if !empty($article->textoArticulo)}<tr><td><b>Cuerpo: </b></td><td>{$article->textoArticulo|truncate:200}</td></tr>{/if}
				{if !empty($article->fecha)}<tr><td><b>Fecha: </b></td><td>{$article->fecha}</td></tr>{/if}
				{if !empty($article->seccion)}<tr><td><b>Seccion: </b></td><td>{$article->seccion}  {if !empty($article->page)} <b>Pagina: </b> {$article->page} - Artículo: {$article->num_article}{/if}</td></tr>{/if}
				{if !empty($article->agency)}<tr><td><b>Agency: </b></td><td>{$article->agency}</td></tr>{/if}
			</table>
			{/foreach}
		</pre>
		{/if}

		</div>
	</div>
	<input type="hidden" id="action" name="action" value="" />
	<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
