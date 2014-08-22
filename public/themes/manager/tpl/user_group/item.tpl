<div class="content">
    <div class="page-title clearfix">
        <h3 class="pull-left">
            <i class="fa fa-cubes"></i>
            <span ng-if="!group.id">{t}New user group{/t}</span>
            <span ng-if="group.id">{t}Edit user group{/t}</span>
        </h3>
        <ul class="breadcrumb pull-right">
            <li>
                <p>{t}YOU ARE HERE{/t}</p>
            </li>
            <li>
                <a href="#">{t}Dashboard{/t}</a>
            </li>
            <li>
                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_groups_list') %]">{t}User groups{/t}</a>
            </li>
            <li>
                <span class="active" ng-if="!group.id">{t}New user group{/t}</span>
                <span class="active" ng-if="group.id">{t}Edit user group{/t}</span>
            </li>
        </ul>
    </div>
    <div class="grid simple">
        <div class="grid-title clearfix">
            <h3 class="pull-left">
                <span class="semi-bold" ng-if="group.id">
                    [% group.name %]
                </span>
                <span class="semi-bold" ng-if="!group.id">
                    {t}New user group{/t}
                </span>
            </h3>
            <div class="pull-right">
                <button class="btn btn-primary" ng-click="save();" ng-disabled="saving || groupForm.$invalid" ng-if="!group.id">
                    <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
                <button class="btn btn-primary" ng-click="update();" ng-disabled="saving || groupForm.$invalid" ng-if="group.id">
                    <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
            </div>
        </div>
        <div class="grid-body">
            <form name="groupForm" novalidate>
                <h4>General information</h4>
                <div class="form-group">
                    <label class="form-label">{t}Group name{/t}</label>
                    <div class="controls">
                        <input class="form-control" ng-model="group.name" required type="text">
                    </div>
                </div>

                <h4>Modules</h4>
                <div class="form-group">
                    <label for="modules" class="form-label">{t}Modules{/t}</label>
                    <!-- <button class="btn btn-default">{t}Basic{/t}</button>
                    <button class="btn btn-default">{t}Pro{/t}</button>
                    <button class="btn btn-default">{t}Silver{/t}</button>
                    <button class="btn btn-default">{t}Gold{/t}</button>
                    <div class="controls">
                        <div ng-repeat="(name,module) in template.modules">
                            <h4>[% name %]</h4>
                            <ul>
                                <li ng-repeat="privilege in module">
                                    [% privilege.name %]
                                </li>
                            </ul>
                        </div>
                    </div> -->
                </div>
            </form>
        </div>
    </div>
</div>
