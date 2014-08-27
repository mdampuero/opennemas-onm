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
        <div class="grid-body clearfix">
            <form name="groupForm" novalidate>
                <h4>General information</h4>
                <div class="form-group">
                    <label class="form-label">{t}Group name{/t}</label>
                    <div class="controls">
                        <input class="form-control" ng-model="group.name" required type="text">
                    </div>
                </div>
                <h4>{t}Privileges{/t}</h4>
                <div class="form-group">
                    <label class="form-label">{t}Presets{/t}</label>
                    <div class="controls">
                        <button class="btn btn-white" type="button">{t}Admin{/t}</button>
                        <button class="btn btn-white" type="button">{t}Author{/t}</button>
                        <button class="btn btn-white" type="button">{t}User{/t}</button>
                        <button class="btn btn-white" ng-click="custom ? custom = 0 : custom = 1" type="button">{t}Custom{/t}</button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{t}Selected privileges{/t}</label>
                    <div class="controls selected-items clearfix">
                        <span ng-repeat="(name,module) in template.modules">
                            <span class="item" ng-repeat="privilege in module" ng-if="isSelected(privilege.id)">
                                <strong>[% privilege.module %]:</strong> [% privilege.description %]
                            </span>
                        </span>
                    </div>
                </div>
                <div ng-show="custom">
                    <div ng-repeat="(name,module) in template.modules" class="pull-left">
                        <div class="checkbox check-default check-title">
                            <input id="checkbox-[% name %]" ng-model="selected.all[name]" type="checkbox" ng-change="selectAll(name);" ng-checked="allSelected(name)">
                            <label for="checkbox-[% name %]">
                                <h5>[% name %]</h5>
                            </label>
                        </div>
                        <div class="checkbox check-default" ng-repeat="privilege in module">
                            <input id="checkbox-[% name + '-' + $index %]" checklist-model="group.privileges" checklist-value="privilege.id" type="checkbox">
                            <label for="checkbox-[% name + '-' + $index %]">
                                [% privilege.description %]
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
