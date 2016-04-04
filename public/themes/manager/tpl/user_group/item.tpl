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
            <button class="btn btn-loading btn-success text-uppercase" ng-click="save();" ng-disabled="saving" ng-if="!group.id">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
            <button class="btn btn-loading btn-success text-uppercase" ng-click="update();" ng-disabled="saving" ng-if="group.id">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="group">
  <form name="groupForm" novalidate>
    <div class="grid simple">
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
        <!-- <div class="form-group">
          <label class="form-label">{t}Selected privileges{/t}</label>
          <ui-select id="modules" multiple ng-model="group.privileges">
          <ui-select-match>
          <strong>[% $item.module %]:</strong> [% $item.description %]
          </ui-select-match>
          <ui-select-choices repeat="item.id as item in modules track by item.name">
          <div ng-bind-html="item.module + ': ' + item.description | highlight: $select.search"></div>
          </ui-select-choices>
          </ui-select>
          </div> -->
          <div class="form-group">
            <button class="btn" ng-click="selectAllPrivileges()">{t}Toggle all privileges{/t}</button>
          </div>
          <div>
            <div ng-repeat="section in sections">
              <h5>{t}[% section.title %]{/t}</h5>
              <div class="row" ng-repeat="columns in section.rows">
                <div class="col-sm-3" ng-repeat="name in columns">
                  <div class="col-sm-12 m-b-10">
                    <div class="checkbox check-default check-title">
                      <input id="checkbox-[% name %]" ng-model="selected.all[name]" type="checkbox" ng-change="selectAll(name);" ng-checked="allSelected(name)">
                      <label for="checkbox-[% name %]">
                        <h5>[% name %]</h5>
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-12 m-b-5" ng-repeat="privilege in extra.modules[name]">
                    <div class="checkbox check-default">
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
    </div>
  </form>
</div>
