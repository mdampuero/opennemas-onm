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
            <h3 class="pull-left">
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
            <tabset class="tab-form">
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
                        <div class="form-group">
                            <label for="name">{t}Display name{/t}</label>
                            <div>
                                <input class="form-control" id="name" ng-model="user.name" ng-required="required" ng-maxlength="50" type="text"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="login">{t}User name{/t}</label>
                            <div class="controls">
                                <input class="form-control" id="login" ng-model="user.username"  ng-required="required" ng-maxlength="20" type="text"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">{t}Email{/t}</label>
                            <div class="controls">
                                <input class="form-control" id="email" placeholder="test@example.com"  ng-model="user.email" ng-required="required" type="email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="url">{t}Blog Url{/t}</label>
                            <div class="controls">
                                <input class="form-control" id="url" placeholder="http://" ng-model="user.url" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bio">{t}Biography{/t}</label>
                            <div class="controls">
                                <textarea class="form-control" id="bio" rows="3">[% user.bio %]</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">{t}Password{/t}</label>
                            <div class="controls">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-key"></i></div>
                                    <input class="form-control" id="password" ng-model="user.password" maxlength="20" type="password"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="passwordconfirm">{t}Confirm password{/t}</label>
                            <div class="controls">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-key"></i></div>
                                    <input class="form-control" id="rpassword" ng-model="user.password" maxlength="20" type="password"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </tab>
                <tab heading="{t}Settings{/t}">
                    <div role="form-horizontal">
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
                </tab>
                <tab heading="{t}Privileges{/t}">
                    {acl isAllowed="GROUP_CHANGE"}
                        <div class="groups">
                            <label for="id_user_group">{t}User group:{/t}</label>
                            <select id="id_user_group" name="id_user_group[]" size="8" multiple="multiple" title="{t}User group:{/t}" class="validate-selection">
                                {if $smarty.session.isMaster}
                                    <option value="4" {if !is_null($user->id) && in_array(4, $user->id_user_group)}selected="selected"{/if}>{t}Master{/t}</option>
                                {/if}
                                {foreach $user_groups as $group}
                                    {if $user->id_user_group neq null && in_array($group->id, $user->id_user_group)}
                                        <option  value="{$group->id}" selected="selected">{$group->name}</option>
                                    {else}
                                        <option  value="{$group->id}">{$group->name}</option>
                                    {/if}
                                {/foreach}
                            </select>
                        </div>
                    {/acl}
                </tab>
                {is_module_activated name="PAYWALL"}
                    <tab heading="{t}Paywall{/t}">
                        <div class="form-group">
                            <label for="time-limit">{t}Paywall time limit:{/t}</label>
                            <input type="datetime" id="time-limit" ng-model="user.meta.paywall_time_limit"/>
                        </div>
                    </tab>
                {/is_module_activated}
            </tabset>
         </div>
    </div>
</div><!-- .content -->
