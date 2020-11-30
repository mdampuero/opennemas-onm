<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_user_groups_list') %]">
              <i class="fa fa-users"></i>
              {t}User groups{/t}
            </a>
          </h4>
        </li>
        <li class="quicklinks hidden-xs ng-cloak" ng-if="!loading && user_group">
          <div class="p-l-10 p-r-10 p-t-10">
            <i class="fa fa-angle-right"></i>
          </div>
        </li>
        <li class="quicklinks hidden-xs ng-cloak" ng-if="!loading && user_group">
          <h5>
            <strong ng-if="!user_group.pk_user_group">{t}Create{/t}</strong>
            <strong ng-if="user_group.pk_user_group">{t}Edit{/t}</strong>
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
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
  <form name="form" novalidate>
    <div class="row">
      <div class="col-md-4 col-md-push-8">
        <div class="grid simple">
          <div class="grid-body no-padding">
            <div class="grid-collapse-title">
              <div class="checkbox">
                <input class="form-control" id="enabled" name="enabled" ng-model="user_group.enabled" type="checkbox">
                <label for="enabled" class="form-label">
                  {t}Enabled{/t}
                </label>
              </div>
            </div>
            <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.visibility }" ng-click="expanded.visibility = !expanded.visibility">
              <i class="fa fa-eye m-r-5"></i>
              {t}Visibility{/t}
              <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.visibility }"></i>
              <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.visibility">
                <span ng-show="user_group.private">{t}Private{/t}</span>
                <span ng-show="!user_group.private">{t}Public{/t}</span>
              </span>
            </div>
            <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.visibility }">
              <div class="form-group no-margin">
                <div class="checkbox">
                  <input class="form-control" id="private" name="private" ng-false-value="0" ng-model="user_group.private" ng-true-value="1" type="checkbox">
                  <label for="private" class="form-label">
                    {t}Private{/t}
                  </label>
                </div>
                <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                  <i class="fa fa-info-circle m-r-5 text-info"></i>
                  {t}If enabled, subscribers will not see this subscription while registering or editing profile{/t}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8 col-md-pull-4">
        <div class="grid simple">
          <div class="grid-body">
            <div class="form-group">
              <label class="form-label">
                {t}Group name{/t}
                <span ng-show="form.name.$invalid">*</span>
              </label>
              <div class="controls" ng-class="{ 'error-control': formValidated && form.name.$invalid }">
                <input class="form-control" name="name" ng-model="user_group.name" required type="text">
                <span class="error" ng-show="formValidated && form.name.$invalid">
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
                <h5 class="m-t-30 semi-bold text-uppercase">[% section.title %]</h5>
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
      </div>
    </div>
  </form>
</div>
