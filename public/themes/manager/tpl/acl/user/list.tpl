<div class="content">
	<div class="page-title">
        <h3 class="pull-left">
        	<i class="fa fa-user"></i>
        	{t}Users{/t}
        </h3>
		<ul class="breadcrumb pull-right">
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
							<input class="form-control" placeholder="Filter by name or username" type="text">
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon primary">
								<i class="fa fa-users"></i>
							</span>
							<select ng-model="group" ng-options="group.id as group.name for group in template.groups">
								<option value="">{t}All{/t}</option>
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
                <div class="text-center" ng-if="users.length == 0">
                	{t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
                </div>
				<table class="table table-condensed" ng-if="users.length > 0">
					<thead>
						<tr>
							<th style="width:15px;">
		                        <input type="checkbox" class="toggleallcheckbox">
		                    </th>
							<th class="left">{t}Full name{/t}</th>
							<th class="left" style="width:110px">{t}Username{/t}</th>
							<th class="left" >{t}Group{/t}</th>
							<th class="text-center" style="width: 180px;">{t}Activated{/t}</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat-start="user in users">
						 	<td>
								<input type="checkbox" name="selected[]" value="{$user->id}">
							</td>
							<td class="left">
								<a href="{url name=manager_acl_user_show id=$user->id}" title="{t}Edit user{/t}">
									[% user.name %]
								</a>
							</td>
							<td class="left">
								[% user.username %]
							</td>
							<td class="left">
								<ul class="no-style">
									<li ng-repeat="id in user.id_user_group">
										[% template.groups[id].name %]
									</li>
								</ul>
							</td>
							<td class="text-center">
								<button class="btn btn-white">
									<i class="fa" ng-class="{ 'fa-check': user.activated, 'fa-times': !user.activated }"></i>
								</button>
							</td>
						</tr>
						<tr ng-repeat-end>
							<td class="text-right" colspan="5" style="border-top: 0;">
								<div class="buttons">
									<a class="btn btn-link" href="#">
										<i class="fa fa-edit"></i> {t}Edit{/t}
									</a>
									<button class="btn btn-link" type="button">
										<i class="fa fa-times"></i> {t}Delete{/t}
									</button>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	{include file="acl/user/modal/_modalDelete.tpl"}

</div>
