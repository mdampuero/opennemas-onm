{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script>
jQuery(function($){
	$('#batch-delete').on('click', function(){
		var form = $('#userform');
		form.attr('action', '{url name="admin_acl_user_batchdelete"}');
	});
});
</script>
{/block}


{block name="content"}
<form action="{url name=admin_user_list}" method="get" id="userform">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}User manager{/t}</h2></div>
			<ul class="old-button">
				<li>
					<button type="submit" id="batch-delete" title="{t}Delete selected users{/t}">
						<img src="{$params.IMAGE_DIR}trash.png" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
					</button>
				</li>
				<li class="separator"></li>
				<li>
					<a href="{url name=admin_acl_user_create}" title="{t}Create new user{/t}">
						<img src="{$params.IMAGE_DIR}user_add.png" alt="Nuevo"><br />{t}New user{/t}
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="wrapper-content">

		{render_messages}

		<div class="table-info clearfix">
			<div class="pull-right form-inline">
				<input type="text" id="username" name="filter[name]" value="{$smarty.request.filter.name|default:""}" placeholder="{t}Filter by name{/t}"  />

				<label for="userlogin">{t}or{/t}</label>
				<input type="text" id="userlogin" name="filter[login]" value="{$smarty.request.filter.login|default:""}" placeholder="{t}username{/t}" />


				<label for="usergroup">{t}and group:{/t}</label>
				<div class="input-append">
					<select id="usergroup" name="filter[group]" class="span2">
						{if isset($smarty.request.filter) && isset($smarty.request.filter.group)}
							{assign var=filter_selected value=$smarty.request.filter.group}
						{/if}
						{html_options options=$groupsOptions selected=$filter_selected|default:""}
					</select>
					<button type="submit" class="btn"><i class="icon-search"></i></button>
				</div>
			</div>
		</div>
		<table class="table table-hover table-condensed">
			{if count($users) gt 0}
			<thead>
				<tr>
					<th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
					<th class="left">{t}Full name{/t}</th>
					<th class="left" style="width:110px">{t}Username{/t}</th>
					<th class="left" >{t}Group{/t}</th>
					<th class="center" >{t}Activated{/t}</th>
					<th class="center" style="width:10px">{t}Actions{/t}</th>
				</tr>
			</thead>
			{/if}
			<tbody>
				{foreach from=$users item=user name=user_listing}
				<tr>
					<td>
						<input type="checkbox" name="selected[]" value="{$user->id}">
					</td>
					<td class="left">
						<a href="{url name=admin_acl_user_show id=$user->id}" title="{t}Edit user{/t}">
							{$user->name}&nbsp;{$user->firstname}&nbsp;{$user->lastname}</a>
					</td>
					<td class="left">
						{$user->login}
					</td>
					<td class="left">
						{section name=u loop=$user_groups}
							{if $user_groups[u]->id == $user->fk_user_group}
								{$user_groups[u]->name}
							{/if}
						{/section}
					</td>
					<td class="center">
						<div class="btn-group">
							<a class="btn" href="{url name=admin_acl_user_toogle_enabled id=$user->id}" title="{t}Activate user{/t}">
								{if $user->authorize eq 1}
									<i class="icon16 icon-ok"></i>
								{else}
									<i class="icon16 icon-remove"></i>
								{/if}
							</a>
						</div>
					</td>
					<td class="right nowrap">
						<div class="btn-group">
							<a class="btn" href="{url name=admin_acl_user_show id=$user->id}" title="{t}Edit user{/t}">
								<i class="icon-pencil"></i> {t}Edit{/t}
							</a>

							<a class="del btn btn-danger"
								href="{url name=admin_acl_user_delete id=$user->id}"
								data-url="{url name=admin_acl_user_delete id=$user->id}"
								data-title="{$user->name}"
								title="{t}Delete this user{/t}">
								<i class="icon-trash icon-white"></i>
							</a>
						</div>
					</td>
				</tr>

				{foreachelse}
				<tr>
					<td colspan="5" class="empty">
						{t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
					</td>
				</tr>
				{/foreach}
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">
						&nbsp;
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</form>
{include file="acl/user/modal/_modalDelete.tpl"}
{/block}
