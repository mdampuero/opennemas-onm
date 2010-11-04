{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if $smarty.request.action eq "list"}

{include file="botonera_up.tpl"}

<table class="adminheading">
	<tr>
		<th nowrap></th>
	</tr>	
</table>
<table border="0" cellpadding="4" cellspacing="0" class="adminlist">
<tr>
<th class="title" style="text-align:left; padding-left:10px">Group name </th>
<th class="title">Actions</th>
</tr>
{section name=c loop=$user_groups}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
	<td style="padding:10px;">
		{$user_groups[c]->name}
	</td>
	<td style="padding:10px;width:75px; text-align:center">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$user_groups[c]->id});" title="Edit this User group">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
            &nbsp;
		<a href="#" onClick="javascript:confirmar(this, {$user_groups[c]->id});" title="Delete this user group">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td align="center"><b>There isn't any group to list here.</b></td>
</tr>
{/section}
{if count($user_groups) gt 0}
<tr>
    <td colspan="3" align="center">{$paginacion->links}</td>
</tr>
{/if}
</table>
{/if}


{* FORMULARIO PARA ENGADIR UN USUARIO **************************************** *}
{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}

{include file="botonera_up.tpl"}

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
<tbody>
<!-- Id -->
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="id">{* Id: *}</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="hidden" id="idReadOnly" name="idReadOnly" title="Id"
			value="{$user_group->id}" readonly />
	</td>
</tr>
<!-- Nome -->
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="name">Name:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="name" name="name" title="Name for the user group"
			value="{$user_group->name}" class="required"
            {if $user_group->name eq $smarty.const.NAME_GROUP_ADMIN}disabled="disabled"{/if} />
	</td>
</tr>
<!-- Privileges -->
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="privileges">Grants:</label>
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
{/if}

{include file="footer.tpl"}