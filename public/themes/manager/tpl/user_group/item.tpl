<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_user_groups_list') %]">
              <i class="fa fa-users"></i>
              {t}User Groups{/t}
            </a>
          </h4>
        </li>
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            <span ng-if="!user_group.pk_user_group">{t}New user group{/t}</span>
            <span ng-if="user_group.pk_user_group">{t}Edit user group{/t}</span>
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
            <button class="btn btn-loading btn-success text-uppercase" ng-click="user_group.pk_user_group ? update() : save();" ng-disabled="saving">
              <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i>
              {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="!loading && user_group">
  <form name="groupForm" novalidate>
    <div class="grid simple">
      <div class="grid-body">
        <div class="form-group">
          <label class="form-label">
            {t}Group name{/t}
            <span ng-show="groupForm.name.$invalid">*</span>
          </label>
          <div class="controls" ng-class="{ 'error-control': formValidated && groupForm.name.$invalid }">
            <input class="form-control" name="name" ng-model="user_group.name" required type="text">
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
        <div class="checkbox check-default check-title">
          <input id="checkbox-all" ng-change="selectAll()" ng-checked="areAllSelected()" ng-model="selected.allSelected" type="checkbox">
          <label for="checkbox-all">
            <h5 class="semi-bold text-uppercase">{t}Toggle all privileges{/t}</h5>
          </label>
        </div>
        <div>
          <div ng-repeat="section in sections">
            <h5 class="m-t-30 semi-bold">[% section.title %]</h5>
            <div class="row" ng-repeat="columns in section.rows">
              <div class="col-sm-3" ng-repeat="name in columns">
                <div class="col-sm-12 m-b-10">
                  <div class="checkbox check-default check-title">
                    <input id="checkbox-[% name %]" ng-change="selectModule(name)" ng-checked="isModuleSelected(name)" ng-model="selected.all[name]" type="checkbox">
                    <label for="checkbox-[% name %]">
                      <h5 class="semi-bold">[% name %]</h5>
                    </label>
                  </div>
                </div>
                <div class="col-sm-12 m-b-5" ng-repeat="privilege in extra.modules[name]">
                  <div class="checkbox check-default">
                    <input id="checkbox-[% name + '-' + $index %]" checklist-model="user_group.privileges" checklist-value="privilege.id" type="checkbox">
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
    </div>
  </form>
</div>
