{extends file="base/admin.tpl"}

{block name="action_buttons"}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
		<ul>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva privilegios');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}privilege_add.png" title="{t}New Privilege{/t}" alt="{t}New Privilege{/t}"><br />{t}New Privilege{/t}
				</a>
			</li>
		</ul>
	</div>
{/block}

{block name="content"}
	<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}
		  style="max-width:70% !important; margin: 0 auto; display:block;">

	{* LIST ******************************************************************* *}
	{if $smarty.request.action eq "list"}

	{block name="action_buttons"}{/block}

	<table class="adminheading">
		<tr>
			<th nowrap></th>
		</tr>
	</table>
	<table border="0" cellpadding="4" cellspacing="0" class="adminlist">
	<tr>
	<th class="title" style="text-align:left; padding-left:10px">{t}Group name{/t}</th>
	<th class="title">{t}Actions{/t}</th>
	</tr>
	{section name=c loop=$user_groups}
	<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
		<td style="padding:10px;">
			{$user_groups[c]->name}
		</td>
		<td style="padding:10px;width:75px; text-align:center">
			<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$user_groups[c]->id});" title="{t}Edit group{/t}">
				<img src="{$params.IMAGE_DIR}edit.png" alt="{t}Edit group{/t}" border="0" /></a>
				&nbsp;
			<a href="#" onClick="javascript:confirmar(this, {$user_groups[c]->id});" title="{t}Delete group{/t}">
				<img src="{$params.IMAGE_DIR}trash.png" alt="{t}Delete group{/t}" border="0" /></a>
		</td>
	</tr>
	{sectionelse}
	<tr>
		<td align="center"><b>{t}There is no groups created yet.{/t}</b></td>
	</tr>
	{/section}
	{if count($user_groups) gt 0}
	<tr>
		<td colspan="3" align="center">{$paginacion->links}</td>
	</tr>
	{/if}
	</table>
	{/if}


	{* FROM FOR ADDING A GROUP **************************************** *}
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
			<input type="hidden" id="idReadOnly" name="idReadOnly" title="{t}Id{/t}"
				value="{$user_group->id}" readonly />
		</td>
	</tr>
	<!-- Nome -->
	<tr>
		<td valign="top" align="right" style="padding:4px;" width="30%">
			<label for="name">{t}Name:{/t}</label>
		</td>
		<td style="padding:4px;" nowrap="nowrap" width="70%">
			<input type="text" id="name" name="name" title="{t}Group name{/t}"
				value="{$user_group->name}" class="required"
				{if $user_group->name eq $smarty.const.NAME_GROUP_ADMIN}disabled="disabled"{/if} />
		</td>
	</tr>
	<!-- Privileges -->
	<tr>
		<td valign="top" align="right" style="padding:4px;" width="30%">
			<label for="privileges">{t}Grants:{/t}</label>
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

	<input type="hidden" id="action" name="action" value="" /><input type="hidden" name="id" id="id" value="{$id}" />
	</form>
{/block}