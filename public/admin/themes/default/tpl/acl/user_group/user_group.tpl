{extends file="base/admin.tpl"}

{block name="action_buttons"}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
		<ul>
			<li>
				<a href="{$smarty.server.PHP_SELF}?action=new">
					<img border="0" src="{$params.IMAGE_DIR}privilege_add.png" title="{t}New User Group{/t}" alt="{t}New User Group{/t}"><br />{t}New User Group{/t}
				</a>
			</li>
		</ul>
	</div>
{/block}

{block name="content"}
<div class="wrapper-content">
	<form action="#" method="post" name="formulario" id="formulario" {$formAttrs} />

	{* LIST ******************************************************************* *}
	{if $smarty.request.action eq "list"}

	{block name="action_buttons"}{/block}
	<br>

	<table class="adminheading">
		<tr>
			<th nowrap></th>
		</tr>
	</table>
	<table class="adminlist">
		<thead>
			<tr>
			<th class="title" style="text-align:left; padding-left:10px">{t}Group name{/t}</th>
			<th class="title">{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
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
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" align="center">{$paginacion->links}</td>
			</tr>
		</tfoot>
	</table>
	{/if}


	{* FROM TO ADD A GROUP **************************************** *}
	{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}

	<div id="menu-acciones-admin" class="clearfix">
		<ul>
		    <li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user_group->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}user_validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
		    </li>
			<li>
				<a href="{$smaty.server.PHP_SELF}?action=list" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
			<li>
			{if isset($user_group->id)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$user_group->id}, 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0,'formulario');">
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
		</ul>
	</div>

	<table class="adminheading">
		<tr>
			<th nowrap></th>
		</tr>
	</table>
	<table class="adminlist">
		<tbody>
		<!-- Id -->
		<tr>
			<td align="right" style="padding:4px;" width="20%">
				<label for="id">{* Id: *}</label>
			</td>
			<td style="padding:4px;" nowrap="nowrap" width="80%">
				<input type="hidden" id="idReadOnly" name="idReadOnly" title="{t}Id{/t}"
					value="{$user_group->id}" readonly />
			</td>
		</tr>
		<!-- Nome -->
		<tr>
			<td valign="top" align="right" style="padding:4px;" width="20%">
				<label for="name">{t}Name:{/t}</label>
			</td>
			<td style="padding:4px;" nowrap="nowrap" width="80%">
				<input type="text" id="name" name="name" title="{t}Group name{/t}"
					value="{$user_group->name}" class="required"
					{if $user_group->name eq $smarty.const.NAME_GROUP_ADMIN}disabled="disabled"{/if} />
			</td>
		</tr>
		<!-- Privileges -->
		<tr>
			<td valign="top" align="right" style="padding:4px;" width="20%">
				<label for="privileges">{t}Grants:{/t}</label>
			</td>

			<td style="padding:4px;" nowrap="nowrap" width="80%">
				 <div  style="float:left;width:420px;">
                {foreach item=privileges from=$modules key=mod name=priv}
                    {assign var=middle value=($smarty.foreach.priv.total/2)}
                      {if $smarty.foreach.priv.index gt $middle &&   $smarty.foreach.priv.index lt $middle+1}</div><div style="float:left;width:420px;"> {/if}
                        <div style=" width:400px;background-color: #EEE;">
                        <a style="cursor:pointer;" onClick="Element.toggle('{$mod}');"> 
                            <h3 style="padding:4px;"> {t}{$mod}{/t} </h3>
                        </a>
                        <br>  
                        <table border="0" cellpadding="0" cellspacing="0" id="{$mod}" class="fuente_cuerpo" width="100%" style="display:none;">
                            <tbody>
                            {section name=privilege loop=$privileges}
                                    <tr>
                                    <td style="padding:4px;" nowrap="nowrap" width="5%">

                                    {if $user_group->contains_privilege($privileges[privilege]->id)}
                                       <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}" checked>
                                       <script  type="text/javascript">
                                            $('{$mod}').setStyle('display:block');
                                       </script>

                                    {else}
                                       <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}">
                                    {/if}
                                    </td>
                                    <td valign="top" align="left" style="padding:4px;" width="95%">
                                            {t}{$privileges[privilege]->description}{/t}
                                    </td>
                                    </tr>
                            {/section}
                        </tbody>
                        </table>
                     </div>
                {/foreach}
	      </div>
			</td>
		</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" align="center">{$paginacion->links}</td>
			</tr>
		</tfoot>
	</table>
	</div>
	{/if}

	<input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
	</form>
</div>
{/block}
