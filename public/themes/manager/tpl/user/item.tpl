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
                        <div class="col-md-3">
                            <div class="fileupload {if $user->photo}fileupload-exists{else}fileupload-new{/if}" data-provides="fileupload">
                                {if $user->photo->name}
                                <div class="fileupload-preview thumbnail" style="width: 140px; height: 140px;">
                                    <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$user->photo->path_file}/{$user->photo->name}" alt="{t}Photo{/t}"/>
                                </div>
                                {else}
                                <div class="fileupload-preview thumbnail" style="width: 140px; height: 140px;" rel="tooltip" data-original-title="{t escape=off}If you want a custom avatar sign up in <a href='http://www.gravatar.com'>gravatar.com</a> with the same email address as you have here in OpenNemas{/t}">
                                    {gravatar email=$user->email image_dir="{$params.COMMON_ASSET_DIR}images/" image=true size="150"}
                                </div>
                                {/if}
                                <div>
                                    <span class="btn btn-file">
                                        <span class="fileupload-new">{t}Add new photo{/t}</span>
                                        <span class="fileupload-exists">{t}Change{/t}</span>
                                        <input type="file"/>
                                        <input type="hidden" name="avatar" class="file-input" value="1">
                                    </span>
                                    <a href="#" class="btn fileupload-exists delete" data-dismiss="fileupload">{t}Remove{/t}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group" ng-class="{ 'has-error': userForm.name.$dirty && userForm.name.$invalid, 'has-success': userForm.name.$dirty && userForm.name.$valid }">
                                <label class="control-label" for="name">{t}Display name{/t}</label>
                                <div>
                                    <input class="form-control" id="name" name="name" ng-model="user.name" ng-maxlength="50" required type="text"/>
                                </div>
                            </div>
                            <div class="form-group" ng-class="{ 'has-error': userForm.username.$dirty && userForm.username.$invalid, 'has-success': userForm.username.$dirty && userForm.username.$valid }">
                                <label class="control-label" for="username">{t}User name{/t}</label>
                                <div class="controls">
                                    <input class="form-control" id="username" name="username" ng-model="user.username"  ng-maxlength="20" required type="text"/>
                                </div>
                            </div>
                            <div class="form-group" ng-class="{ 'has-error': userForm.email.$dirty && userForm.email.$invalid, 'has-success': userForm.email.$dirty && userForm.email.$valid }">
                                <label class="control-label" for="email">{t}Email{/t}</label>
                                <div class="controls">
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
