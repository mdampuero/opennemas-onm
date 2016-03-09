<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_users_list') %]">
              <i class="fa fa-user"></i>
              {t}Users{/t}
            </a>
          </h4>
        </li>
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            <span ng-if="!user.id">{t}New user{/t}</span>
            <span ng-if="user.id">{t}Edit user{/t}</span>
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_users_list') %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-success text-uppercase" ng-click="save();" ng-disabled="saving" ng-if="!user.id">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
            <button class="btn btn-primary text-uppercase" ng-click="update();" ng-disabled="saving" ng-if="user.id">
              <i class="fa fa-save m-r-t" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <form name="userForm" novalidate>
    <div class="row">
      <div class="col-sm-7">
        <div class="grid simple">
          <div class="grid-body">
            <div class="form-group">
              <label class="control-label" for="name">
                {t}Display name{/t}
                <span ng-show="userForm.name.$invalid">*</span>
              </label>
              <div class="controls input-with-icon right" ng-class="{ 'error-control': formValidated && userForm.name.$invalid }">
                <input class="form-control" id="name" name="name" ng-model="user.name" ng-maxlength="50" required type="text"/>
                <span class="error" ng-show="formValidated && userForm.name.$invalid">
                  <label for="form1Amount" class="error">{t}This field is required{/t}</label>
                </span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label" for="username">
                {t}User name{/t}
                <span ng-show="userForm.username.$invalid">*</span>
              </label>
              <div class="controls" ng-class="{ 'error-control': formValidated && userForm.username.$invalid }">
                <input class="form-control" id="username" name="username" ng-model="user.username"  ng-maxlength="20" required type="text"/>
                <span class="error" ng-show="formValidated && userForm.username.$invalid">
                  <label for="form1Amount" class="error">{t}This field is required{/t}</label>
                </span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label" for="email">
                {t}Email{/t}
                <span ng-show="userForm.email.$invalid">*</span>
              </label>
              <div class="controls" ng-class="{ 'error-control': formValidated && userForm.email.$invalid }">
                <input class="form-control" id="email" name="email" placeholder="test@example.com"  ng-model="user.email" required type="email">
                <span class="error" ng-show="formValidated && userForm.email.$invalid">
                  <label for="form1Amount" class="error">{t}This field is required{/t}</label>
                </span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label" for="url">{t}Blog Url{/t}</label>
              <div class="controls">
                <input class="form-control" id="url" name="url" placeholder="http://" ng-model="user.url" type="text">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label" for="bio">{t}Biography{/t}</label>
              <div class="controls">
                <textarea class="form-control" id="bio" name="bio" ng-model="user.bio" rows="3"></textarea>
              </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error': userForm.password.$dirty && userForm.password.$invalid, 'has-success': userForm.password.$dirty && userForm.password.$valid }">
              <label class="control-label" for="password">{t}Password{/t}</label>
              <div class="controls">
                <div class="input-group">
                  <div class="input-group-addon"><i class="fa fa-key"></i></div>
                  <input class="form-control" id="password" name="password"  ng-model="user.password" maxlength="20" type="password"/>
                </div>
              </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error': userForm.rpassword.$dirty && userForm.rpassword.$invalid, 'has-success': userForm.rpassword.$dirty && userForm.rpassword.$valid }">
              <label class="control-label" for="rpassword">{t}Confirm password{/t}</label>
              <div class="controls">
                <div class="input-group">
                  <div class="input-group-addon"><i class="fa fa-key"></i></div>
                  <input class="form-control" id="rpassword" id="rpassword"  ng-model="user.rpassword" maxlength="20" type="password"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-5">
        {is_module_activated name="PAYWALL"}
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Paywall{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="form-group">
                <label for="time-limit">{t}Paywall time limit:{/t}</label>
                <input class="form-control" datetime-picker="picker" ng-model="user.meta.paywall_time_limit" type="text">
              </div>
            </div>
          </div>
        {/is_module_activated}
        {acl isAllowed="GROUP_CHANGE"}
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Privileges{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="form-group">
                <label for="id-user-group">{t}User group{/t}</label>
                <ui-select multiple ng-model="user.id_user_group" theme="select2" >
                  <ui-select-match>
                    [% $item.name %]
                  </ui-select-match>
                  <ui-select-choices repeat="item.id as item in template.groups">
                    <div ng-bind-html="item.name | highlight: $select.search"></div>
                  </ui-select-choices>
                </ui-select>
              </div>
            </div>
          </div>
        {/acl}
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Settings{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="form-group">
              <label for="user-type">{t}User type{/t}</label>
              <select id="user-type" ng-model="user.type">
                <option value="0">{t}Backend{/t}</option>
                <option value="1">{t}Frontend{/t}</option>
              </select>
            </div>
            <div class="form-group">
              <label for="user-language">{t}User language{/t}</label>
              <select id="user-language" ng-model="user.meta.user_language" ng-options="key as value for (key, value) in template.languages"></select>
              <div class="help-block">{t}Used for displayed messages, interface and measures in your page.{/t}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div><!-- .content -->
