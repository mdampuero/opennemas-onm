{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.post.action) || $smarty.post.action eq "list"}
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
{section name=c loop=$user_groups}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
	<td style="padding:10px;">
		{$user_groups[c]->name}
	</td>
	<td style="padding:10px;width:75px;">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$user_groups[c]->id});" title="Modificar">
			<img src="{php}echo($this->image_dir);{/php}btn_modificar.gif" border="0" /></a>
	</td>
	<td style="padding:10px;width:75px;">
		<a href="#" onClick="javascript:confirmar(this, {$user_groups[c]->id});" title="Eliminar">
			<img src="{php}echo($this->image_dir);{/php}btn_eliminar.gif" border="0" /></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td align="center"><b>Ningún grupo de usuarios guardado.</b></td>
</tr>
{/section}
{if count($user_groups) gt 0}
<tr>
    <td colspan="3" align="center">{$paginacion->links}</td>
</tr>
{/if}
</table>
{/if}


{* FORMULARIO PARA ENGADIR UN CONTENIDO ************************************** *}
{if isset($smarty.post.action) && (($smarty.post.action eq "new") || ($smarty.post.action eq "read"))}

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
<tbody>
<!-- Id -->
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="id">Id:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="idReadOnly" name="idReadOnly" title="Id"
			value="{$user_group->id}" readonly />
	</td>
</tr>
<!-- Nome -->
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="name">Nombre:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="name" name="name" title="Nombre del grupo de usuarios"
			value="{$user_group->name}" class="required" />
	</td>
</tr>
<!-- Privileges -->
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="privileges">Permisos activados:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
		<tbody>
		{section name=privilege loop=$privileges}
			<tr>
			<td style="padding:4px;" nowrap="nowrap" width="5%">

		{if $user_group->contains_privilege($privileges[privilege]->id)}
					<input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}" checked>
  		{else}
					<input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}">
		{/if}
			</td>
			<td valign="top" align="left" style="padding:4px;" width="95%">
				{$privileges[privilege]->description}
			</td>
			</tr>
		{/section}
		</tbody>
		</table>
	</td>
</tr>
</tbody>
</table>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
<tbody>
<tr>
	<td colspan="2" align="right">
		{if isset($user_group->id) }
		   <a href="#" onClick="javascript:enviar(this, '_self', 'update', {$user_group->id});">
		{else}
		   <a href="#" onClick="javascript:enviar(this, '_self', 'create', 0);">
		{/if}
			<img src="{php}echo($this->image_dir);{/php}btn_guardar.gif" border="0" /></a>&nbsp;&nbsp;
	</td>
</tr>
</tbody>
</table>
{literal}
<script type="text/javascript" language="javascript">
tinyMCE.init({
	mode : "exact",
	elements : "body"
});
</script>
{/literal}
{/if}

{include file="footer.tpl"}