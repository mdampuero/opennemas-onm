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
<form action="{url name=admin_acl_user}" method="get" id="userform">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Users{/t}</h2></div>
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
				<input type="text" id="username" name="name" value="{$smarty.request.name|default:""}" placeholder="{t}Filter by name or email{/t}" />

				<label for="usergroup">{t}type{/t}</label>
				<div class="input-append">
					<select id="usertype" name="type" class="span2">
						{assign var=type value=$smarty.request.type}
						<option value="" {if ($type eq "")}selected{/if}>{t}--All--{/t}</option>
                        <option value="0" {if ($type eq "0")}selected{/if}>{t}Backend{/t}</option>
                        <option value="1" {if ($type eq "1")}selected{/if}>{t}Frontend{/t}</option>
					</select>
				</div>

				<label for="usergroup">{t}and group{/t}</label>
				<div class="input-append">
					<select id="usergroup" name="group" class="span2">
						{html_options options=$groupsOptions selected=$smarty.request.group|default:""}
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
                    <th></th>
					<th class="left">{t}Full name{/t}</th>
					<th class="center" style="width:110px">{t}Username{/t}</th>

					<th class="center" >{t}E-mail{/t}</th>

					<th class="center" >{t}Group{/t}</th>

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
					<td>
                        {if $user->photo->name}
                        {dynamic_image src="{$user->photo->path_file}/{$user->photo->name}" transform="thumbnail,40,40"}
                        {else}
                        {gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true size="40"}
                        {/if}
					</td>
					<td class="left">
						<a href="{url name=admin_acl_user_show id=$user->id}" title="{t}Edit user{/t}">
							{$user->name}
						</a>
					</td>
					<td class="center">
						{$user->username}
					</td>

					<td class="center">
						{$user->email}
					</td>
					<td class="center">
						{foreach $user_groups as $group}
							{if in_array($group->id, $user->fk_user_group)}
								{$group->name}<br>
							{/if}
						{/foreach}
					</td>

					<td class="center">
						<div class="btn-group">
							<a class="btn" href="{url name=admin_acl_user_toogle_enabled id=$user->id}" title="{t}Activate user{/t}">
								{if $user->activated eq 1}
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
					<td colspan="8" class="empty">
						{t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
					</td>
				</tr>
				{/foreach}
			</tbody>
			<tfoot>
				<tr >
					<td colspan="8" class="center">
		                <div class="pagination">
		    				{$pagination->links|default:""}&nbsp;
		                </div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</form>
{include file="acl/user/modal/_modalDelete.tpl"}
{/block}
