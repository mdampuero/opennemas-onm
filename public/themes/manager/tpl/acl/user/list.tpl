<div class="content">
	<ul class="breadcrumb">
        <li>
            <p>{t}YOU ARE HERE{/t}</p>
        </li>
        <li>
            <a href="#">{t}Dashboard{/t}</a>
        </li>
        <li>
            <a href="#/users" class="active">{t}Users{/t}</a>
        </li>
    </ul>
	<div class="page-title">
        <h3>
        	<i class="fa fa-user"></i>
        	{t}Users{/t}
        </h3>
    </div>
	<div ng-init="">

		{render_messages}

		<div class="grid simple">
			<div class="grid-title">
				<div class="form-inline">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon primary">
								<span class="arrow"></span>
								<i class="fa fa-user"></i>
							</span>
							<input class="form-control" type="text" placeholder="Filter by name or username">
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon primary">
								<span class="arrow"></span>
								<i class="fa fa-users"></i>
							</span>
							<select>
								<option value="1">1</option>
								<option value="2">2</option>
							</select>
						</div>
					</div>
					<div class="form-group pull-right">
						<button class="btn btn-primary">
							<i class="fa fa-plus"></i>
							{t}Create{/t}
						</button>
					</div>
				</div>
			</div>
			<div class="grid-body no-padding">
				<div class="spinner-wrapper" ng-if="loading">
                    <div class="spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
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
								<a href="{url name=manager_acl_user_show id=$user->id}" title="{t}Edit user{/t}">
									{$user->name}
								</a>
							</td>
							<td class="left">
								{$user->username}
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
									<a class="btn" href="{url name=manager_acl_user_toogle_enabled id=$user->id}" title="{t}Activate user{/t}">
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
									<a class="btn" href="{url name=manager_acl_user_show id=$user->id}" title="{t}Edit user{/t}">
										<i class="icon-pencil"></i> {t}Edit{/t}
									</a>

									<a class="del btn btn-danger"
										href="{url name=manager_acl_user_delete id=$user->id}"
										data-url="{url name=manager_acl_user_delete id=$user->id}"
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
		</div>
	</form>
	{include file="acl/user/modal/_modalDelete.tpl"}

</div>
