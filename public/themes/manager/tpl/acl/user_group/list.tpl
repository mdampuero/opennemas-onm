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
            <div class="form-inline">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon primary">
                            <span class="arrow"></span>
                            <i class="fa fa-users"></i>
                        </span>
                        <input class="form-control" type="text" placeholder="Filter by name">
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
                <div class="pull-right">
                    <div class="form-group">
                        <button class="btn btn-primary">
                            <i class="fa fa-plus"></i> {t}Create{/t}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-body">
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th>{t}Group name{/t}</th>
                        <th class="center" style="width: 120px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="group in groups">
                        <td>
                            <a href="#" title="{t}Edit group{/t}">
                                [% group.name %]
                            </a>
                        </td>
                        <td class="right nowrap">
                            <div class="btn-group">
                                <a class="btn" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_group_show', { id: group.id }); %]" title="{t}Edit group{/t}">
                                    <i class="icon-pencil"></i> {t}Edit{/t}
                                </a>
                                <a class="btn btn-danger" href="#" title="{t}Delete group{/t}">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



{include file="acl/user_group/modal/_modalDelete.tpl"}
