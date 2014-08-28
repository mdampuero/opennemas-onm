<div class="content">
    <div class="page-title">
        <h3 class="pull-left">
            <i class="fa fa-users"></i>
            {t}Users groups{/t}
        </h3>
        <ul class="breadcrumb pull-right">
            <li>
                <p>{t}YOU ARE HERE{/t}</p>
            </li>
            <li>
                <a href="#">{t}Dashboard{/t}</a>
            </li>
            <li>
                <a href="#/users" class="active">{t}Users groups{/t}</a>
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
                                <i class="fa fa-users"></i>
                            </span>
                            <input class="form-control" ng-model="criteria.name[0].value" placeholder="Filter by name" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <select class="xsmall" ng-model="epp">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                    <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="loading"></i>
                </div>
                <div class="action-buttons">
                    <div class="form-group" ng-if="selected.groups.length > 0">
                        <div class="btn-group">
                            <button class="btn btn-white dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-edit"></i> {t}Actions{/t} <i class="fa fa-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <span class="a" ng-click="deleteSelected()">
                                        <i class="fa fa-trash-o"></i> {t}Delete{/t}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_group_create') %]" class="btn btn-primary">
                            <i class="fa fa-plus"></i> {t}Create{/t}
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
                        <th class="pointer" ng-click="sort('name')">{t}Group name{/t}</th>
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
