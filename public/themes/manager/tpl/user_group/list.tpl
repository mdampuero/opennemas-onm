<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_groups_list') %]">
                            <i class="fa fa-users fa-lg"></i>
                            {t}Users groups{/t}
                        </a>
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-primary" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_group_create') %]">
                            <i class="fa fa-plus"></i>
                            {t}Create{/t}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="selected-actions pull-right" ng-class="{ 'collapsed': selected.groups.length == 0 }">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                        <h4>
                            [% selected.groups.length %] items selected
                        </h4>
                    </li>
                </ul>
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" ng-click="deleteSelected()">
                            <i class="fa fa-trash-o"></i>
                            {t}Delete{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="page-navbar filters-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="m-r-10 input-prepend inside search-form no-boarder">
                        <span class="add-on">
                            <span class="fa fa-search fa-lg"></span>
                        </span>
                        <input class="no-boarder" ng-keyup="searchByKeypress($event)" ng-model="criteria.name_like[0].value" placeholder="Filter by name" type="text" style="width:250px;"/>
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
                        <button class="btn btn-white" ng-click="criteria = {  name_like: [ { value: '', operator: 'like' } ] }; orderBy = [ { name: 'name', value: 'asc' } ]; page = 1; epp = 25; refresh()">
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
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button class="btn btn-white" ng-click="page = page - 1" ng-disabled="page - 1 < 1">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                            </div>
                            <input class="form-control" type="text" value="[% page + '/' + pages %]" readonly>
                            <div class="input-group-btn">
                                <button class="btn btn-white" ng-click="page = page + 1" ng-disabled="page == pages">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="grid simple">
        <div class="grid-body no-padding">
            <div class="grid-overlay" ng-if="loading"></div>
            <table class="table no-margin">
                <thead>
                    <tr>
                        <th style="width:15px;">
                            <div class="checkbox checkbox-default">
                                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                <label for="select-all"></label>
                            </div>
                        </th>
                        <th class="pointer" ng-click="sort('name')">
                            {t}Group name{/t}
                            <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="groups.length == 0">
                        <td class="text-center" colspan="10">{t}There is no available groups yet{/t}</td>
                    </tr>
                    <tr ng-repeat="group in groups" ng-class="{ row_selected: isSelected(group.id) }">
                        <td>
                            <div class="checkbox check-default">
                                <input id="checkbox[%$index%]" checklist-model="selected.groups" checklist-value="group.id" type="checkbox">
                                <label for="checkbox[%$index%]"></label>
                            </div>
                        </td>
                        <td>
                            [% group.name %]
                            <div class="listing-inline-actions">
                                <a class="link" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_group_show', { id: group.id }); %]" title="{t}Edit group{/t}">
                                    <i class="fa fa-pencil"></i>{t}Edit{/t}
                                </a>
                                <button class="link link-danger" ng-click="delete(group)" type="button">
                                    <i class="fa fa-trash-o"></i>{t}Delete{/t}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot ng-if="groups.length > 0">
                    <tr>
                        <td colspan="3">
                            <div class="pagination-info pull-left" ng-if="groups.length > 0">
                                {t}Showing{/t} [% ((page - 1) * epp > 0) ? (page - 1) * epp : 1 %]-[% (page * epp) < total ? page * epp : total %] {t}of{/t} [% total|number %]
                            </div>
                            <div class="pull-right" ng-if="groups.length > 0">
                                <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="$parent.$parent.epp" ng-model="$parent.$parent.page" total-items="$parent.$parent.total" num-pages="pages"></pagination>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
