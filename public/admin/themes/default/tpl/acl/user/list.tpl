{extends file="base/admin.tpl"}

{block name="footer-js" append}
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
			<div class="title"><h2>{t}User Manager{/t}</h2></div>
			<ul class="old-button">
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
						<img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
					</a>
				</li>
				<li class="separator"></li>
				<li>
					<a href="{$smarty.server.PHP_SELF}?action=new&id=0" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}user_add.png" title="Nuevo" alt="Nuevo"><br />{t}New user{/t}
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

					<label for="username">{t}Filter by name{/t}
						<input id="username" name="filter[name]" onchange="$('action').value='list';this.form.submit();" value="{$smarty.request.filter.name|default:""}" />
					</label>

					<label for="userlogin">{t}or username:{/t}
						<input id="userlogin" name="filter[login]" onchange="$('action').value='list';this.form.submit();" value="{$smarty.request.filter.login|default:""}" />
					</label>

					<label for="usergroup">{t}and group:{/t}
						<select id="usergroup" name="filter[group]" onchange="$('action').value='list';this.form.submit();">
							{if isset($smarty.request.filter) && isset($smarty.request.filter.group)}
								{assign var=filter_selected value=$smarty.request.filter.group}
							{/if}
							{html_options options=$groupsOptions selected=$filter_selected|default:""}
						</select>
					</label>
					<input type="hidden" name="page" value="{$smarty.request.page}" />
					<button type="submit">{t}Search{/t}</button>
				</th>
			</tr>
		</table>

		<table class="listing-table">
			{if count($users) gt 0}
			<thead>
				<tr>
					<th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
					<th class="left">{t}Full name{/t}</th>
					<th class="center">{t}Username{/t}</th>
					<th class="center">{t}Group{/t}</th>
					<th class="right">{t}Actions{/t}</th>
				</tr>
			</thead>
			{/if}
			<tbody>
			{foreach from=$users item=user name=user_listing}
				<tr>
					<td>
						<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$user->id}"  style="cursor:pointer;" ">
					</td>
					<td class="left">
						<a href="?action=read&id={$user->id}" title="{t}Edit user{/t}">
							{$user->name}&nbsp;{$user->firstname}&nbsp;{$user->lastname}</a>
					</td>
					<td class="center">
						{$user->login}
					</td>
					<td class="center">
						{section name=u loop=$user_groups}
							{if $user_groups[u]->id == $user->fk_user_group}
								{$user_groups[u]->name}
							{/if}
						{/section}
					</td>
					<td class="right">
						<ul class="action-buttons">
							<li>
								<a href="{$smarty.server.PHP_SELF}?action=read&id={$user->id}&page={$page|default:0}" title="{t}Edit user{/t}">
									<img src="{$params.IMAGE_DIR}edit.png" border="0" alt="{t}Edit user{/t}"/>
								</a>
							</li>
							<li>
								<a href="#" onClick="javascript:confirmar(this, {$user->id});" title="{t}Delete user{/t}">
									<img src="{$params.IMAGE_DIR}trash.png" border="0" alt="{t}Delete user{/t}"/>
								</a>
							</li>
						</ul>
					</td>
				</tr>

				{foreachelse}
				<tr>
					<td colspan="5" class="empty">
						{t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
					</td>
				</tr>
				{/foreach}
				<tfoot>
					<tr>
						<td colspan="5">
							&nbsp;
						</td>
					</tr>
				</tfoot>
			</tbody>
		</table>

		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$id|default:""}" />

	</div>
</form>
{/block}
