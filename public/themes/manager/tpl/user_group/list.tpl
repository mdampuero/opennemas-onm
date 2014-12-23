<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <a ng-href="[% routing.ngGenerate('manager_user_groups_list') %]">
                            <i class="fa fa-users fa-lg"></i>
                            {t}Users groups{/t}
                        </a>
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-primary" ng-href="[% routing.ngGenerate('manager_user_group_create') %]">
                            <i class="fa fa-plus"></i>
                            {t}Create{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="page-navbar selected-navbar" ng-class="{ 'collapsed': selected.groups.length == 0 }">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section pull-left">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-check"></i>
                        [% selected.groups.length %] {t}items selected{/t}
                    </h4>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="selected.groups = []; selected.all = 0" tooltip="{t}Clear selection{/t}" tooltip-placement="bottom" type="button">
                      {t}Deselect{/t}
                    </button>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <a class="btn btn-link" ng-click="deleteSelected()"  tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                        <i class="fa fa-trash-o"></i>
                    </a>
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
                    <input class="no-boarder" ng-keyup="searchByKeypress($event)" ng-model="criteria.name_like[0].value" placeholder="Filter by name" type="text" style="width:250px;"/>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks hidden-xs">
                    <select class="xmedium" ng-model="epp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                    </select>
                </li>
                <li class="quicklinks hidden-xs">
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
                    <div class="btn-group">
                        <button class="btn btn-white" ng-click="pagination.page = pagination.page - 1" ng-disabled="pagination.page - 1 < 1" type="button">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-white" ng-click="pagination.page = pagination.page + 1" ng-disabled="pagination.page == pagination.pages" type="button">
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
                <div class="table-wrapper">
                    <table class="table table-hover no-margin">
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
                                <td class="text-center" colspan="10">
                                    {t escape=off}There is no available groups yet or <br/>your search don't match your criteria{/t}
                                </td>
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
                                        <a class="link" ng-href="[% routing.ngGenerate('manager_user_group_show', { id: group.id }); %]" title="{t}Edit group{/t}">
                                            <i class="fa fa-pencil"></i>{t}Edit{/t}
                                        </a>
                                        <button class="link link-danger" ng-click="delete(group)" type="button">
                                            <i class="fa fa-trash-o"></i>{t}Delete{/t}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-footer clearfix">
                <div class="pull-left pagination-info" ng-if="groups.length > 0">
                    {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total|number %]
                </div>
                <div class="pull-right" ng-if="groups.length > 0">
                    <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="pagination.epp" ng-model="pagination.page" total-items="pagination.total" num-pages="pagination.pages"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/ng-template" id="modal-confirm">
    {include file="common/modal_confirm.tpl"}
</script>
