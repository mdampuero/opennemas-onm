{extends file="base/admin.tpl"}

{block name="content"}
<div class="wrapper-content">

	<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">

		{* LISTADO ******************************************************************* *}
		{if !isset($smarty.get.action) || $smarty.get.action eq "list"}
		<div id="menu-acciones-admin">
			<div style='float:left;margin-left:10px;margin-top:10px;'><h2>Keyword Manager :: Listing keywords</h2></div>
			<ul>
				<li>
					<a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add" title="Nueva palabra clave">
						<img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva palabra clave" alt="" ><br />{t}New{/t}
					</a>
				</li>
			</ul>
		</div>
		<br>


		<table class="adminheading">
			<tr>
				<td align="right">
					{t}Search keyworks containing {/t}
					{if isset($smarty.request.filter)
						&& isset({$smarty.request.filter.pclave})}
						{assign var=filterPClave value=$smarty.request.filter.pclave}
					{/if}
					<input type="text" name="filter[pclave]" style="margin-top:-2px;" value="{$filterPClave|default:""}" />
					<button type="submit" onclick="javascript:$('action').value='list';">Filtrer</button>
				</td>
			</tr>
		</table>
		<table border="0" cellpadding="4" cellspacing="0" class="adminlist">
			<thead>
				<tr>
					<th class="title">{t}Type{/t}</th>
					<th class="title">{t}Keyword{/t}</th>
					<th class="title">{t}Replacement value{/t}</th>
					<th>{t}Actions{/t}</th>
				</tr>
			</thead>

			<tbody>
				{section name=k loop=$pclaves|default:array()}
				<tr style="background:{cycle values="#eeeeee,#ffffff"} !important">

					<td align="center" weight="15">
						<img src="{$params.IMAGE_DIR}iconos/{$pclaves[k]->tipo}.gif" border="0" alt="{$pclaves[k]->tipo}" />
					</td>
					<td>
						{$pclaves[k]->pclave}
					</td>
					<td>
						{$pclaves[k]->value|default:"-"}
					</td>

					<td width="44">
						<a href="{$smarty.server.PHP_SELF}?action=read&id={$pclaves[k]->id}" title="{t}Modify{/t}">
							<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
						&nbsp;
						<a href="#" onClick="javascript:confirmar(this, {$pclaves[k]->id});" title="{t}Delete{/t}">
							<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
					</td>
				</tr>
				{sectionelse}
				<tr>
					<td align="center"><b>Ninguna palabra guardada.</b></td>
				</tr>
				{/section}
			</tbody>

			<tfoot class="pagination">
				<tr>
					<td colspan="5" align="center">
						{$pager->links}
					</td>
				</tr>
			</tfoot>
		</table>
		{/if}


		{* FORMULARIO PARA ENGADIR OU MODIFICAR  ************************************** *}
		{if isset($smarty.get.action) && (($smarty.get.action eq "new") || ($smarty.get.action eq "read"))}
		<div id="menu-acciones-admin">
			<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Keyword Manager :: Editing keyword information{/t}</h2></div>
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
		<br>

		<table class="adminheading">
			<tr>
				<td>{t}Keyword information{/t}</td>
			</tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="0" class="adminlist">
		<tbody>
		<tr>
			<td valign="top" align="right" style="padding:4px;" width="40%">
				<label for="name">Palabra clave:</label>
			</td>
			<td style="padding:4px;" nowrap="nowrap" width="60%">
				<input type="text" id="pclave" name="pclave" title="Palabra clave" value="{$pclave->pclave|default:""}"
					   class="required" size="30" maxlength="60" />
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" style="padding:4px;" width="40%">
				<label for="tipo">Tipo:</label>
			</td>
			<td style="padding:4px;" nowrap="nowrap" width="60%">
				<select name="tipo" id="tipo">
					{html_options options=$tipos selected=$pclave->tipo|default:""}
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" style="padding:4px;" width="40%">
				<label for="value">Valor:</label>
			</td>
			<td style="padding:4px;" nowrap="nowrap" width="60%">
				<input type="text" id="value" name="value" title="Valor" value="{$pclave->value|default:""}"
					   size="50" maxlength="240" />
			</td>
		</tr>

		<tr>
			<td valign="top" align="right" style="padding:4px;">
				<label>Existing similar elements yet: </label>
			</td>
			<td valign="top" style="padding:4px;">
				<div id="similarKeywords" style="border: 1px solid #CCC; padding: 10px; width: 400px; background-color: #EEE;"></div>
			</td>
		</tr>

		</tbody>
		<tfoot>
			<tr>
				<td colspan=2></td>
			</tr>
		</tfoot>
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
		<input type="hidden" name="id" id="id" value="{$id|default:""}" />
	</form>

</div>
{/block}
