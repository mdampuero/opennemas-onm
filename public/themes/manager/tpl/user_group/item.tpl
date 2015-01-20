<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <a ng-href="[% routing.ngGenerate('manager_user_groups_list') %]">
                            <i class="fa fa-users fa-lg"></i>
                            {t}User Groups{/t}
                        </a>
                    </h4>
                </li>
                <li class="quicklinks seperate">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <h5>
                        <span ng-if="!group.id">{t}New user group{/t}</span>
                        <span ng-if="group.id">{t}Edit user group{/t}</span>
                    </h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_user_groups_list') %]">
                            <i class="fa fa-reply"></i>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <button class="btn btn-primary" ng-click="save();" ng-disabled="saving" ng-if="!group.id">
                            <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                        </button>
                        <button class="btn btn-primary" ng-click="update();" ng-disabled="saving" ng-if="group.id">
                            <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <form name="groupForm" novalidate>
        <div class="grid simple">
            <div class="grid-title">
                <h4>
                    <span class="semi-bold">[% group.name %]</span>
                </h4>
            </div>
            <div class="grid-body">
                <div class="form-group">
                    <label class="form-label">
                        {t}Group name{/t}
                        <span ng-show="groupForm.name.$invalid">*</span>
                    </label>
                    <div class="controls" ng-class="{ 'error-control': formValidated && groupForm.name.$invalid }">
                        <input class="form-control" name="name" ng-model="group.name" required type="text">
                        <span class="error" ng-show="formValidated && groupForm.name.$invalid">
                            <label for="form1Amount" class="error">{t}This field is required{/t}</label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid simple">
            <div class="grid-title">
                <h4>{t}Privileges{/t}</h4>
            </div>
            <div class="grid-body">
                <div class="form-group">
                    <label class="form-label">{t}Selected privileges{/t}</label>
                    <select id="modules" multiple ui-select2 ng-model="group.privileges">
                        <option value="[% value.id %]" ng-repeat="value in modules">
                            <strong>[% value.module %]</strong>: [% value.description %]
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn" ng-click="selectAllPrivileges()">{t}Toggle all privileges{/t}</button>
                </div>
                <div>
                    <div ng-repeat="section in sections">
                        <h5>{t}[% section.title %]{/t}</h5>
                        <div class="row" ng-repeat="columns in section.rows">
                            <div class="col-sm-3" ng-repeat="name in columns">
                                <div class="checkbox check-default check-title">
                                    <input id="checkbox-[% name %]" ng-model="selected.all[name]" type="checkbox" ng-change="selectAll(name);" ng-checked="allSelected(name)">
                                    <label for="checkbox-[% name %]">
                                        <h5>[% name %]</h5>
                                    </label>
                                </div>
                                <div class="checkbox check-default" ng-repeat="privilege in template.modules[name]">
                                    <input id="checkbox-[% name + '-' + $index %]" checklist-model="group.privileges" checklist-value="privilege.id" type="checkbox">
                                    <label for="checkbox-[% name + '-' + $index %]">
                                        [% privilege.description %]
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
