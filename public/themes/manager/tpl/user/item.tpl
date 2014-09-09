<div class="content">
    <div class="page-title clearfix">
        <h3 class="pull-left">
            <i class="fa fa-user"></i>
            <span ng-if="!user.id">{t}New user{/t}</span>
            <span ng-if="user.id">{t}Edit user{/t}</span>
        </h3>
        <ul class="breadcrumb pull-right">
            <li>
                <p>{t}YOU ARE HERE{/t}</p>
            </li>
            <li>
                <a href="#">{t}Dashboard{/t}</a>
            </li>
            <li>
                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_users_list') %]">{t}Users{/t}</a>
            </li>
            <li>
                <span class="active" ng-if="!user.id">{t}New user{/t}</span>
                <span class="active" ng-if="user.id">{t}Edit user{/t}</span>
            </li>
        </ul>
    </div>
    <div class="grid simple">
        <div class="grid-title clearfix">
            <h3 class="pull-left" ng-if="user.id">
                [% user.name %]
            </h3>
            <div class="pull-right">
                <button class="btn btn-primary" ng-click="save();" ng-disabled="saving || userForm.$invalid" ng-if="!user.id">
                    <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
                <button class="btn btn-primary" ng-click="update();" ng-disabled="saving || userForm.$invalid" ng-if="user.id">
                    <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
            </div>
        </div>
        <div class="grid-body no-padding">
            <form name="userForm" novalidate>
                <tabset class="tab-form clearfix">
                    <tab heading="{t}User info{/t}">
                        <div class="form-group">
                            <label class="control-label" for="name">
                                {t}Display name{/t}
                                <span ng-show="userForm.name.$invalid">*</span>
                            </label>
                            <div class="controls input-with-icon right" ng-class="{ 'error-control': userForm.name.$dirty && userForm.name.$invalid, 'success-control': userForm.name.$dirty && userForm.name.$valid }">
                                <i class="fa" ng-class="{ 'fa-exclamation': userForm.name.$dirty && userForm.name.$invalid, 'fa-check': userForm.name.$dirty && userForm.name.$valid }"></i>
                                <input class="form-control" id="name" name="name" ng-model="user.name" ng-maxlength="50" required type="text"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="username">
                                {t}User name{/t}
                                <span ng-show="userForm.username.$invalid">*</span>
                            </label>
                            <div class="controls input-with-icon right" ng-class="{ 'error-control': userForm.username.$dirty && userForm.username.$invalid, 'success-control': userForm.username.$dirty && userForm.username.$valid }">
                                <i class="fa" ng-class="{ 'fa-exclamation': userForm.username.$dirty && userForm.username.$invalid, 'fa-check': userForm.username.$dirty && userForm.username.$valid }"></i>
                                <input class="form-control" id="username" name="username" ng-model="user.username"  ng-maxlength="20" required type="text"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">
                                {t}Email{/t}
                                <span ng-show="userForm.email.$invalid">*</span>
                            </label>
                            <div class="controls input-with-icon right" ng-class="{ 'error-control': userForm.email.$dirty && userForm.email.$invalid, 'success-control': userForm.email.$dirty && userForm.email.$valid }">
                                <i class="fa" ng-class="{ 'fa-exclamation': userForm.email.$dirty && userForm.email.$invalid, 'fa-check': userForm.email.$dirty && userForm.email.$valid }"></i>
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
                    </tab>
                    <tab heading="{t}Settings{/t}">
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
                    </tab>
                    <tab heading="{t}Privileges{/t}">
                        {acl isAllowed="GROUP_CHANGE"}
                            <div class="form-group">
                                <label for="id-user-group">{t}User group{/t}</label>
                                <select id="id-user-group" name="id_user_group" ui-select2 multiple ng-model="user.id_user_group" ng-options="key as value.name for (key, value) in template.groups track by value.id"></select>
                            </div>
                        {/acl}
                    </tab>
                    {is_module_activated name="PAYWALL"}
                        <tab heading="{t}Paywall{/t}">
                            <div class="form-group">
                                <label for="time-limit">{t}Paywall time limit:{/t}</label>
                                <quick-datepicker icon-class="fa fa-clock-o" id="time-limit" name="time_limit" ng-model="user.meta.paywall_time_limit" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                            </div>
                        </tab>
                    {/is_module_activated}
                </tabset>
            </form>
         </div>
    </div>
</div><!-- .content -->
