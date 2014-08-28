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
                    <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="loading"></i>
                </div>
				<div class="action-buttons">
					<div class="form-group" ng-if="selected.users.length > 0">
                        <div class="btn-group">
                            <button class="btn btn-white dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-edit"></i> {t}Actions{/t} <i class="fa fa-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <span class="a" ng-click="setEnabledSelected(1)">
                                        <i class="fa fa-check"></i> {t}Enable{/t}
                                    </span>
                                </li>
                                <li>
                                    <span class="a" ng-click="setEnabledSelected(0)">
                                        <i class="fa fa-times"></i> {t}Disable{/t}
                                    </span>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <span class="a" ng-click="deleteSelected()">
                                        <i class="fa fa-trash-o"></i> {t}Delete{/t}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
					<a class="btn btn-primary" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_create') %]">
						<i class="fa fa-plus"></i>
						{t}Create{/t}
					</a>
				</div>
			</div>
		</div>
		<div class="grid-body no-padding">
			<div class="grid-overlay" ng-if="loading"></div>
            <div class="text-center" ng-if="users.length == 0">
            	{t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
            </div>
			<table class="table no-margin" ng-if="users.length > 0">
				<thead>
					<tr>
						<th style="width:15px;">
							<div class="checkbox checkbox-default">
                                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                <label for="select-all"></label>
                            </div>
	                    </th>
						<th class="left pointer" ng-click="sort('name')">
                            {t}Full name{/t}
                            <i ng-class="{ 'fa fa-caret-up': orderBy.name == 'asc', 'fa fa-caret-down': orderBy.name == 'desc'}"></i>
                        </th>
						<th class="left pointer" ng-click="sort('username')">
                            {t}Username{/t}
                            <i ng-class="{ 'fa fa-caret-up': orderBy.username == 'asc', 'fa fa-caret-down': orderBy.username == 'desc'}"></i>
                        </th>
                        <th>{t}Group{/t}</th>
						<th class="text-center pointer" style="width: 10px;" ng-click="sort('activated')">{t}Activated{/t}</th>
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
								<button class="link link-danger" ng-click="delete(user)" type="button">
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
							<button class="btn btn-white" ng-click="setEnabled(user, user.activated == '1' ? '0' : '1')">
                                <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': user.loading, 'fa-check text-success' : !user.loading &&user.activated == '1', 'fa-times text-error': !user.loading && user.activated == '0' }"></i>
							</button>
						</td>
					</tr>
				</tbody>
                <tfoot ng-if="users.length > 0">
                    <tr>
                        <td colspan="5">
                            <div class="pagination-info pull-left" ng-if="users.length > 0">
                                {t}Showing{/t} [% ((page - 1) * epp > 0) ? (page - 1) * epp : 1 %]-[% (page * epp) < total ? page * epp : total %] {t}of{/t} [% total|number %]
                            </div>
                            <div class="pull-right" ng-if="users.length > 0">
                                <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="$parent.$parent.epp" ng-model="$parent.$parent.page" total-items="$parent.$parent.total" num-pages="pages"></pagination>
                            </div>
                        </td>
                    </tr>
                </tfoot>
			</table>
		</div>
	</div>
</div>
