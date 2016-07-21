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
            <button class="btn btn-loading btn-success text-uppercase" ng-click="save();" ng-disabled="saving || userForm.$invalid || (user.password && user.password !== rpassword)" ng-if="!user.id">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
            <button class="btn btn-loading btn-success text-uppercase" ng-click="update();" ng-disabled="saving || userForm.$invalid || (user.password && user.password !== rpassword)" ng-if="user.id">
              <i class="fa fa-save m-r-t" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="user">
  <form name="userForm" novalidate>
    <div class="row">
      <div class="col-sm-7">
        <div class="grid simple">
          <div class="grid-body">
            <div class="form-group">
              <label class="control-label" for="name">{t}Display name{/t}</label>
              <div class="controls input-with-icon right">
                <input class="form-control" id="name" name="name" ng-model="user.name" ng-maxlength="50" required type="text"/>
                <span class="icon right">
                  <span class="fa fa-check text-success" ng-if="userForm.name.$dirty && userForm.name.$valid"></span>
                  <span class="fa fa-asterisk" ng-if="!userForm.name.$dirty && userForm.name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                  <span class="fa fa-times text-error" ng-if="userForm.name.$dirty && userForm.name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                </span>
              </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error': userForm.username.$dirty && userForm.username.$invalid }">
              <label class="control-label" for="username">{t}User name{/t}</label>
              <div class="controls input-with-icon right">
                <input class="form-control" id="username" name="username" ng-model="user.username"  ng-maxlength="20" required type="text"/>
                <span class="icon right">
                  <span class="fa fa-check text-success" ng-if="userForm.username.$dirty && userForm.username.$valid"></span>
                  <span class="fa fa-asterisk" ng-if="!userForm.username.$dirty && userForm.username.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                  <span class="fa fa-times text-error" ng-if="userForm.username.$dirty && userForm.username.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                </span>
              </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error': userForm.email.$dirty && userForm.email.$invalid }">
              <label class="control-label" for="email">{t}Email{/t}</label>
              <div class="controls input-with-icon right">
                <span class="icon right">
                  <span class="fa fa-check text-success" ng-if="userForm.email.$dirty && userForm.email.$valid"></span>
                  <span class="fa fa-asterisk" ng-if="!userForm.email.$dirty && userForm.email.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                  <span class="fa fa-times text-error" ng-if="userForm.email.$dirty && userForm.email.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                </span>
                <input class="form-control" id="email" name="email" placeholder="test@example.com"  ng-model="user.email" required type="email">
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
            <div class="form-group" ng-class="{ 'has-error': userForm.password.$dirty && userForm.password.$invalid }">
              <label class="control-label" for="password">{t}Password{/t}</label>
              <div class="controls">
                <div class="input-group">
                  <span class="input-group-addon pointer" ng-click="passwordUnlocked = !passwordUnlocked">
                    <i class="fa fa-lock" ng-class="{ 'fa-unlock': passwordUnlocked }"></i>
                  </span>
                  <input class="form-control no-animate" id="password" name="password" ng-model="user.password" maxlength="20" type="[% !passwordUnlocked ? 'password' : 'text' %]">
                </div>
              </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error': userForm.password.$valid && user.password && user.password !== rpassword }">
              <label class="control-label" for="rpassword">{t}Confirm password{/t}</label>
              <div class="controls">
                <div class="input-group input-group-transparent">
                  <span class="input-group-addon pointer" ng-click="rpasswordUnlocked = !rpasswordUnlocked">
                    <i class="fa fa-lock" ng-class="{ 'fa-unlock': rpasswordUnlocked }"></i>
                  </span>
                  <input class="form-control" id="rpassword" id="rpassword" maxlength="20" ng-model="rpassword" maxlength="20" type="[% !rpasswordUnlocked ? 'password' : 'text' %]">
                  <span class="input-group-addon input-group-addon-transparent">
                    <span class="fa fa-check text-success" ng-if="userForm.password.$dirty && user.password === rpassword"></span>
                    <span class="fa fa-times text-error" ng-if="userForm.password.$valid && user.password && user.password !== rpassword" uib-tooltip="{t}The passwords don't match{/t}"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-5">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Settings{/t}</h4>
          </div>
          <div class="grid-body">
            {acl isAllowed="GROUP_CHANGE"}
              <div class="form-group">
                <label class="form-label">
                  {t}User groups{/t}
                </label>
                <div class="controls">
                  <tags-input add-from-autocomplete-only="true" display-property="name" ng-model="user.fk_user_group">
                    <auto-complete source="autocomplete($query)" min-length="0" load-on-focus="true" load-on-empty="true"></auto-complete>
                  </tags-input>
                </div>
              </div>
            {/acl}
            <div class="form-group">
              <label class="form-label">
                {t}User type{/t}
              </label>
              <div class="controls">
                <div class="radio">
                  <input class="form-control" id="backend" ng-model="user.type" ng-value="0" type="radio"/>
                  <label for="backend">
                    {t}Backend{/t}
                  </label>
                </div>
                <div class="radio">
                  <input class="form-control" id="frontend" ng-model="user.type" ng-value="1" type="radio"/>
                  <label for="frontend">
                    {t}Frontend{/t}
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="user-language">{t}User language{/t}</label>
              <select id="user-language" ng-model="user.user_language" ng-options="key as value for (key, value) in extra.languages"></select>
              <div class="help-block">{t}Used for displayed messages, interface and measures in your page.{/t}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div><!-- .content -->
