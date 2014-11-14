<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_users_list') %]">
                            <i class="fa fa-user fa-lg"></i>
                            {t}Users{/t}
                        </a>
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-primary" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_create') %]">
                            <i class="fa fa-plus"></i>
                            {t}Create{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="page-navbar selected-navbar" ng-class="{ 'collapsed': selected.users.length == 0 }">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section pull-left">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-check"></i>
                        [% selected.users.length %] {t}items selected{/t}
                    </h4>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="selected.users = []; selected.all = 0" tooltip="{t}Clear selection{/t}" tooltip-placement="bottom" type="button">
                      {t}Deselect{/t}
                    </button>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="setEnabledSelected(0)" tooltip="{t}Disable{/t}" tooltip-placement="bottom">
                        <i class="fa fa-times fa-lg"></i>
                    </button>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="setEnabledSelected(1)" tooltip="{t}Enable{/t}" tooltip-placement="bottom">
                        <i class="fa fa-check fa-lg"></i>
                    </button>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="deleteSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                        <i class="fa fa-trash-o fa-lg"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="m-r-10 input-prepend inside search-form no-boarder">
                    <span class="add-on">
                        <span class="fa fa-search fa-lg"></span>
                    </span>
                    <input class="no-boarder" ng-keyup="searchByKeypress($event)" ng-model="criteria.name_like[0].value" placeholder="Filter by name or username" type="text" style="width:250px;"/>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <select class="btn btn-white form-control" ng-model="criteria.fk_user_group[0].value" ng-options="value.id as value.name for (key, value) in template.groups">
                                <option value="">{t}All groups{/t}</option>
                    </select>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <select class="xmedium" ng-model="epp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                    </select>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="criteria = { name_like: [ { value: '', operator: 'like' } ], fk_user_group: [ { value: '' }] }; orderBy = [ { name: 'name', value: 'asc' } ]; page = 1; epp = 25; refresh()">
                        <i class="fa fa-trash-o fa-lg"></i>
                    </button>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="refresh()">
                        <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
                    </button>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks form-inline pagination-links">
                    <div class="btn-group">
                        <button class="btn btn-white" ng-click="page = page - 1" ng-disabled="page - 1 < 1" type="button">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-white" ng-click="page = page + 1" ng-disabled="page == pages" type="button">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="content">
	<div class="grid simple">
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
                            <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
                        </th>
						<th class="left pointer" ng-click="sort('username')">
                            {t}Username{/t}
                            <i ng-class="{ 'fa fa-caret-up': isOrderedBy('username') == 'asc', 'fa fa-caret-down': isOrderedBy('username') == 'desc'}"></i>
                        </th>
                        <th>{t}Group{/t}</th>
						<th class="text-center pointer" style="width: 10px;" ng-click="sort('activated')">{t}Activated{/t}</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="user in users" ng-class="{ row_selected: isSelected(user.id) }">
					 	<td>
							<div class="checkbox check-default">
                                <input id="checkbox[%$index%]" checklist-model="selected.users" checklist-value="user.id" type="checkbox">
                                <label for="checkbox[%$index%]"></label>
                            </div>
						</td>
						<td class="left">
							<a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_show', { id: user.id }); %]">
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
                                <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="$parent.$parent.epp" ng-model="$parent.$parent.page" total-items="$parent.$parent.total" num-pages="$parent.$parent.pages"></pagination>
                            </div>
                        </td>
                    </tr>
                </tfoot>
			</table>
		</div>
	</div>
</div>
<script type="text/ng-template" id="modal-confirm">
    {include file="common/modal_confirm.tpl"}
</script>
