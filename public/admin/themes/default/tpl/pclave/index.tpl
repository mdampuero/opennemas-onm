{extends file="base/admin.tpl"}

{block name="content"}
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST"
	  style="max-width:70% !important; margin: 0 auto; display:block;">

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
<div id="menu-acciones-admin">
<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
<ul>
    <li>
        <a href="#" class="admin_add" onClick="enviar(this, '_self', 'new', -1);"
           title="Nueva palabra clave">
            <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva palabra clave" alt="" ><br />Nueva
        </a>
    </li>
</ul>
</div>


<table class="adminheading">
	<tr>
		<td>
			Keyworks containing <label><input type="text" name="filter[pclave]" value="{$smarty.request.filter.pclave}" /></label>
			<button type="submit" onclick="javascript:$('action').value='list';">Filtrer</button>
		</td>
	</tr>
</table>
<table border="0" cellpadding="4" cellspacing="0" class="adminlist">
<thead>
<tr>
    <th class="title">Type</th>
    <th class="title">Keyword</th>
    <th class="title">Replace value</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>
{section name=k loop=$pclaves}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">

	<td align="center">
        <img src="{$params.IMAGE_DIR}iconos/{$pclaves[k]->tipo}.gif" border="0" alt="{$pclaves[k]->tipo}" />
	</td>
	<td>
		{$pclaves[k]->pclave}
	</td>
	<td>
		{$pclaves[k]->value|default:"-"}
	</td>

	<td width="44">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$pclaves[k]->id});" title="Modificar">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
		&nbsp;
		<a href="#" onClick="javascript:confirmar(this, {$pclaves[k]->id});" title="Eliminar">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td align="center"><b>Ninguna palabra guardada.</b></td>
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
            <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
        </a>
    </li>
</ul>
</div>

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="40%">
        <label for="name">Palabra clave:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="60%">
        <input type="text" id="pclave" name="pclave" title="Palabra clave" value="{$pclave->pclave}"
               class="required" size="30" maxlength="60" />
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="40%">
        <label for="tipo">Tipo:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="60%">
        <select name="tipo" id="tipo">
            {html_options options=$tipos selected=$pclave->tipo}
        </select>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="40%">
        <label for="value">Valor:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="60%">
        <input type="text" id="value" name="value" title="Valor" value="{$pclave->value}"
               size="50" maxlength="240" />
    </td>
</tr>

<tr>
    <td colspan="2"><hr /></td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label>Elementos similares ya existentes: </label>
    </td>
    <td valign="top" style="padding:4px;">
        <div id="similarKeywords" style="border: 1px solid #CCC; padding: 10px; width: 400px; background-color: #EEE;"></div>
    </td>
</tr>

</tbody>
</table>

<script type="text/javascript">
var searching = false;

new Form.Element.Observer(
	'pclave',
	0.4,
	function() {
		var valor = $('pclave').value;
		if((valor.length >= 4) && (!searching)) {
			searching = true;
			new Ajax.Updater('similarKeywords', '?action=search&q='+valor+'&id='+$('id').value, {
				onSuccess: function() {
					searching = false;
				}
			});
		}
	}
);

</script>
{/if}

<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>

{/block}
