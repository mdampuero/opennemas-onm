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
	<div class="grid simple">
		<div class="grid-title">
			<div class="form-inline clearfix">
                <div class="form-filter">
                	<div class="hidden-md hidden-lg filter">{t}Filter:{/t}</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon primary">
								<span class="arrow"></span>
								<i class="fa fa-user"></i>
							</span>
							<input class="form-control" ng-model="criteria.name[0].value" placeholder="Filter by name or username" type="text">
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon primary">
								<i class="fa fa-users"></i>
							</span>
							<select ng-model="criteria.fk_user_group[0].value" ng-options="value.id as value.name for (key, value) in template.groups">
								<option value="">{t}All{/t}</option>
							</select>
						</div>
					</div>
                </div>
				<div class="action-buttons">
					<div class="form-group">
                        <div class="btn-group" ng-if="selected.users.length > 0">
                            <button class="btn btn-white dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-edit"></i> {t}Actions{/t} <i class="fa fa-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                                <li class="divider" ng-if="selected.instances.length > 0"></li>
                                <li ng-if="selected.instances.length > 0">
                                    <span class="a" ng-click="setEnabledSelected(1)">
                                        <i class="fa fa-check"></i> {t}Enable{/t}
                                    </span>
                                </li>
                                <li ng-if="selected.instances.length > 0">
                                    <span class="a" ng-click="setEnabledSelected(0)">
                                        <i class="fa fa-times"></i> {t}Disable{/t}
                                    </span>
                                </li>
                                <li class="divider" ng-if="selected.instances.length > 0"></li>
                                <li ng-if="selected.instances.length > 0">
                                    <span class="a" ng-click="deleteSelected()">
                                        <i class="fa fa-trash-o"></i> {t}Delete{/t}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
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
							<div class="checkbox checkbox-default">
                                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                <label for="select-all"></label>
                            </div>
	                    </th>
						<th class="left">{t}Full name{/t}</th>
						<th class="left">{t}Username{/t}</th>
						<th class="left" >{t}Group{/t}</th>
						<th class="text-center" style="width: 10px;">{t}Activated{/t}</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="user in users">
					 	<td>
							<div class="checkbox check-default">
                                <input id="checkbox[%$index%]" checklist-model="selected.users" checklist-value="user.id" type="checkbox">
                                <label for="checkbox[%$index%]"></label>
                            </div>
						</td>
						<td class="left">
							<a href="{url name=manager_acl_user_show id=$user->id}" title="{t}Edit user{/t}">
								[% user.name %]
							</a>
							<div class="listing-inline-actions">
								<a class="link" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_show', { id: user.id }); %]">
									<i class="fa fa-pencil"></i>{t}Edit{/t}
								</a>
								<button class="link link-danger" type="button">
									<i class="fa fa-trash"></i>{t}Delete{/t}
								</button>
							</div>
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
							<button class="btn btn-white btn-sm">
								<i class="fa" ng-class="{ 'fa-check': user.activated, 'fa-times': !user.activated }"></i>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
