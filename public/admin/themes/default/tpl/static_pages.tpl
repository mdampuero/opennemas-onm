{extends file="base/admin.tpl"}


{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
<div id="menu-acciones-admin">
<ul>
    <li>
        <a href="#" class="admin_add" onClick="enviar(this, '_self', 'new', -1);"
           title="Nueva Página">
            <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva Página" alt="" /><br />Nueva
        </a>
    </li>
</ul>
</div>

<div style="padding: 5px 0;">
	<label>Título: <input type="text" name="filter[title]" value="{$smarty.request.filter.title}" /></label>
	<button type="submit" onclick="javascript:$('action').value='list';">Filtrar</button>
</div>

<style>
{literal}
table.adminlist td {
	padding: 4px;
}
{/literal}
</style>


<table border="0" class="adminlist">
{if count($pages)>0}
<thead>
<tr>
    <th>Título</th>
	<th>URL</th>
	<th class="title">Visitas</th>
	<th class="title">Disponible</th>
    <th>&nbsp;</th>
</tr>
</thead>
{/if}

<tbody id="gridPages">
{section name=k loop=$pages}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
	<td>
		{$pages[k]->title}
	</td>
	<td>
		&raquo;
		<a href="{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}{$pages[k]->slug}.html" target="_blank" title="Abrir en nueva ventana">
			{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}{$pages[k]->slug}.html</a>
	</td>

	<td width="44" align="right">
		{$pages[k]->views}
		&nbsp;&nbsp;
	</td>

	<td width="44" align="center">
		<a href="?action=chg_status&id={$pages[k]->id}" class="available">
			{if $pages[k]->available == 1}
				<img src="{$params.IMAGE_DIR}publish_g.png" border="0" title="Publicado" />
			{else}
				<img src="{$params.IMAGE_DIR}publish_r.png" border="0" title="Pendiente" />
			{/if}
		</a>
	</td>

	<td width="64" align="center">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$pages[k]->id}');" title="Modificar">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
		&nbsp;&nbsp;
		<a href="#" onClick="javascript:confirmar(this, '{$pages[k]->id}');" title="Eliminar">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td align="center"><h2>Ninguna página guardada.</h2></td>
</tr>
{/section}
</tbody>

<tfoot>
    <tr>
        <td colspan="5" align="center">
            {$pager->links}
        </td>
    </tr>
</tfoot>
</table>

<script type="text/javascript" language="javascript" src="{$params.JS_DIR}switcher_flag.js"></script>
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
{literal}
$('gridPages').select('a.available').each(function(item){
	new SwitcherFlag(item);
});
{/literal}
</script>
{/if}


{* FORMULARIO PARA ENGADIR OU MODIFICAR  ************************************** *}
{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}
<div id="menu-acciones-admin">
<ul>
    <li>
        <a href="#" class="admin_add" onClick="javascript:enviar(this, '_self', 'save', $('id').value);"
           value="Guardar" title="Guardar">
            <img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar" alt="Guardar" /><br />Guardar
        </a>
    </li>
    <li>
        <a href="?action=list" class="admin_add" value="Cancelar" title="Cancelar">
            <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" /><br />Cancelar
        </a>
    </li>
</ul>
</div>

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="20%">
        <label for="name">Título:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="60%">
        <input type="text" id="title" name="title" title="Título de la página" value="{$page->title}"
               class="required" size="60" maxlength="120" tabindex="1" />
    </td>

	<td valign="top" align="right" style="padding:4px;" rowspan="3" width="20%">
        <table style="background-color:#F5F5F5; padding:18px; width:99%;">
			<tr>
				<td valign="middle" align="right">
					<label for="available">Disponible:</label>
				</td>
				<td>
					<select name="available" id="available" class="required" tabindex="4">
						<option value="1"{if $page->available eq 1} selected="selected"{/if}>Si</option>
						<option value="0"{if isset($page->available) && $page->available eq 0} selected="selected"{/if}>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left" colspan="2">
					<label for="available">Descripción:</label> <br />
					<textarea name="description" id="description" rows="4" cols="30" tabindex="5">{$page->description}</textarea>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left" colspan="2">
					<label for="available">Metadatos:</label>
					<sub>(separados por coma)</sub> <br />
					<input type="text" name="metadata" id="metadata" size="40" tabindex="6" value="{$page->metadata}" />
				</td>
			</tr>
		</table>
	</td>

</tr>

<tr>
    <td valign="top" align="right" style="padding:4px 0 4px 4px;" width="40%">
		<label for="name">{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}</label>
	</td>
    <td style="padding:4px;" nowrap="nowrap" width="60%">
        <input type="text" id="slug" name="slug" title="Palabra clave" value="{$page->slug}"
               class="required" size="56" maxlength="120" tabindex="2" /><label>.html</label>
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;" colspan="2">
        <textarea name="body" id="body" tabindex="3" title="Contenido de la página" style="width:100%; height:20em;">{$page->body}</textarea>
    </td>
</tr>

</tbody>
</table>

<script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );

OpenNeMas.tinyMceConfig.advanced.elements = "body";
tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
{literal}
var previous = null;
var updateSlug = function() {
	var slugy = $('slug').value.strip();
	if(previous!=slugy) {

		new Ajax.Request('?action=build_slug', {
			method: 'post',
			postBody: 'slug=' + slugy + '&id=' + $('id').value + '&title=' + $('title').value,
			onSuccess: function(transport) {
				$('slug').value = transport.responseText;
				previous = $('slug').value;
			}
		});
	}
};

document.observe('dom:loaded', function() {
	$('title').observe('blur', function() {
		var slugy = $('slug').value.strip();
		if(slugy.length <= 0) {
			updateSlug();
		}
	});

	$('slug').observe('blur', updateSlug);

	$('metadata').observe('blur', function() {
		new Ajax.Request('?action=clean_metadata', {
			method: 'post',
			postBody: 'metadata=' + $('metadata').value,
			onSuccess: function(transport) {
				$('metadata').value = transport.responseText;
			}
		});
	});
});
{/literal}
/* ]]> */
</script>

<input type="hidden" name="filter[title]" value="{$smarty.request.filter.title}" />
{/if}

<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
