{extends file="base/admin.tpl"}

{block name="footer-js"}
    <script type="text/javascript">
        document.observe('dom:loaded', function(){
            onChangeGroup( document.formulario.id_user_group, new Array('comboAccessCategory','labelAccessCategory') );

            // Refrescar los elementos seleccionados
            $('ids_category').select('option').each(function(item){
                if( item.getAttribute('selected') ) {
                    item.selected=true;
                    item.setAttribute('selected', 'selected');
                }
            });

            new SpinnerControl('sessionexpire', 'up', 'dn', { interval: 5,  min: 15, max: 250 });
        });
    </script>
{/block}


{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{$titulo_barra}</h2></div>
			<ul class="old-button">
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
						<img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
					</a>
				</li>
				<li>
					<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
						<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todos" status="0">
					</button>
				</li>
				<li>
					<a href="{$smarty.server.PHP_SELF}?action=new&id=0" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}user_add.png" title="Nuevo" alt="Nuevo"><br />Nuevo Usuario
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="wrapper-content">

		{* Filters: filter[name], filter[login], filter[group]  *}
		<table class="adminheading">
			<tr>
				<th align="right">

					<label for="username">{t}Filter by name{/t}</label>
					<input id="username" name="filter[name]" onchange="$('action').value='list';this.form.submit();" value="{$smarty.request.filter.name|default:""}" />

					<label for="userlogin">{t}or username:{/t}</label>
					<input id="userlogin" name="filter[login]" onchange="$('action').value='list';this.form.submit();" value="{$smarty.request.filter.login|default:""}" />

					<label for="usergroup">{t}and group:{/t}</label>
					<select id="usergroup" name="filter[group]" onchange="$('action').value='list';this.form.submit();">
						{if isset($smarty.request.filter) && isset($smarty.request.filter.group)}
							{assign var=filter_selected value=$smarty.request.filter.group}
						{/if}
						{html_options options=$groupsOptions selected=$filter_selected|default:""}
					</select>

					<input type="hidden" name="page" value="{$smarty.request.page|default:""}" />
					<input type="submit" value="{t}Search{/t}">
				</th>
			</tr>
		</table>

		<table border="0" cellpadding="4" cellspacing="0" class="adminlist">
			{if count($users) gt 0}
			<thead>
				<tr>
					<th class="title" align="left" style="width:40%;padding:4px;">{t}Name Surname{/t}</th>
					<th class="title" style="padding:4px;">{t}Username{/t}</th>
					<th class="title" style="padding:4px;">{t}Group{/t}</th>
					<th class="title" style="width:50px;padding:4px;" align="right">{t}Actions{/t}</th>
				</tr>
			</thead>
			{/if}

			<tbody>
				{section name=c loop=$users}
				<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">

					<td style="padding:4px;">
								<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$users[c]->id}"  style="cursor:pointer;" ">
						<a href="?action=read&id={$users[c]->id}" title="{t}Edit user{/t}">
							{$users[c]->name}&nbsp;{$users[c]->firstname}&nbsp;{$users[c]->lastname}</a>
					</td>
					<td style="padding:4px;">
						{$users[c]->login}
					</td>
					<td style="padding:4px;">
						{section name=u loop=$user_groups}
							{if $user_groups[u]->id == $users[c]->fk_user_group}
									{$user_groups[u]->name}
							{/if}
						{/section}
					</td>
					<td style="padding:4px;" align="right">
						<ul class="action-buttons">
							<li>
								<a href="{$smarty.server.PHP_SELF}?action=read&id={$users[c]->id}&page={$page|default:0}" title="{t}Edit user{/t}">
									<img src="{$params.IMAGE_DIR}edit.png" border="0" alt="{t}Edit user{/t}"/>
								</a>
							</li>
							<li>
								<a href="#" onClick="javascript:confirmar(this, {$users[c]->id});" title="{t}Delete user{/t}">
									<img src="{$params.IMAGE_DIR}trash.png" border="0" alt="{t}Delete user{/t}"/>
								</a>
							</li>
						</ul>

					</td>
				</tr>
				{sectionelse}
				<tr >
					<td align="center" colspan="4"><h2>{t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}</h2><br>
					</td>
				</tr>
				{/section}
			</tbody>

			<tfoot>
				<tr>
					<td colspan="5" align="center">{if count($users) gt 0}{$paginacion->links}{/if}</td>
				</tr>
			</tfoot>

		</table>

		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$id|default:""}" />

	</div>
</form>
{/block}
