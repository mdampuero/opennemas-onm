{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts src="@Common/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js,
      @Common/js/jquery/jquery.validate.min.js,
      @Common/js/jquery/jquery.multiselect.js,
      @Common/js/jquery/localization/messages_es.js,
      @Common/js/onm/jquery.password-strength.js,
      @Common/components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js,
      @Common/js/admin.js "}
    <script>
      jQuery(document).ready(function($){
        // Password strength checker
        var strength = $('#password').passStrength({
          userid: '#login'
        });

        // Password and confirm password match
        $("#passwordconfirm").on('keyup blur', validate);
        function validate() {
          var password1 = $("#password").val();
          var password2 = $("#passwordconfirm").val();

          if(password1 == password2) {
            $(".checker").html(
              '<div class="alert-pass  alert-success"><strong>Valid</strong></div>'
              );
          }
          else {
            $(".checker").html(
              '<div class="alert-pass  alert-error"><strong>Invalid</strong></div>'
              );
          }
        }

        // Avatar image uploader
        $('.fileinput').fileinput({
          name: 'avatar',
          uploadtype:'image'
        });

        // Use multiselect on user groups and categories
        $('select#id_user_group').twosidedmultiselect();
        $('select#ids_category').twosidedmultiselect();

        // Paywall datepicker only if available
        {acl isAllowed='USER_ADMIN'}
          {is_module_activated name='PAYWALL'}
            $('#paywall_time_limit').datetimepicker({
              format: 'YYYY-MM-D HH:mm:ss'
            });
          {/is_module_activated}
        {/acl}
      });
    </script>
  {/javascripts}
{/block}

{block name="header-css" append}
  {stylesheets src="@Common/components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" css="cssrewrite"}
  {/stylesheets}
{/block}

{block name="content"}
<form action="{if isset($user->id)}{url name=admin_acl_user_update id=$user->id}{else}{url name=admin_acl_user_create}{/if}" method="POST" enctype="multipart/form-data" id="formulario" autocomplete="off" ng-controller="UserCtrl" ng-init="user = {json_encode($user)|clear_json};extra = {json_encode($extra)|clear_json};activated=user.activated;type=user.type">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-user fa-lg"></i>
              Users
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <h5>
              {if isset($user->id)}{t}Editing user{/t}{else}{t}Creating user{/t}{/if}
            </h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_acl_user type=$user->type}">
                <i class="fa fa-reply"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-primary" name="action" ng-click="confirmUser()" type="button" value="validate">
                <i class="fa fa-save"></i>
                {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="grid simple">
          <div class="grid-body">
            <div class="row">
              <div class="col-sm-8">
                <div class="form-group">
                  <label class="form-label" for="name">
                    {t}Display name{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="name" name="name" maxlength="50" required="required" type="text" value="{$user->name|escape:"html"|default:""}" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="login">
                    {t}User name{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="login" maxlength="20" pattern="[a-z0-9\d-.]+" name="login" required="required" type="text" value="{$user->username|default:""}" title="{t}Only lowercase letters, numbers, point and hyphen allowed{/t}" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="email">
                    {t}Email{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="email" name="email" placeholder="test@example.com" required="required"type="email" value="{$user->email|default:""}">
                  </div>
                </div>
              </div>
              <div class="col-sm-4 text-center">
                <div class="fileinput {if $user->photo}fileinput-exists{else}fileinput-new{/if}" data-provides="fileinput">
                  <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                  </div>
                  {if $user->photo->name}
                  <div class="fileinput-exists fileinput-preview thumbnail" style="width: 140px; height: 140px;">
                    <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$user->photo->path_file}/{$user->photo->name}" alt="{t}Photo{/t}"/>
                  </div>
                  {else}
                  <div class="fileinput-exists fileinput-preview thumbnail" style="width: 140px; height: 140px;" rel="tooltip" data-original-title="{t escape=off}If you want a custom avatar sign up in <a href='http://www.gravatar.com'>gravatar.com</a> with the same email address as you have here in OpenNemas{/t}">
                    {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="150"}
                  </div>
                  {/if}
                  <div>
                    <span class="btn btn-file">
                      <span class="fileinput-new">{t}Add new photo{/t}</span>
                      <span class="fileinput-exists">{t}Change{/t}</span>
                      <input type="file"/>
                      <input type="hidden" name="avatar" class="file-input" value="1">
                    </span>
                    <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput">
                      <i class="fa fa-trash-o"></i>
                      {t}Remove{/t}
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="url">
                {t}Blog Url{/t}
              </label>
              <div class="controls">
                <input class="form-control" id="url" name="url" placeholder="http://" type="text" value="{$user->url|default:""}">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="bio">
                {t}Short Biography{/t}
              </label>
              <div class="controls">
                <input class="form-control" id="bio" name="bio" type="text" value="{$user->bio|default:""}">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="bio">
                {t}Biography{/t}
              </label>
              <div class="controls">
                <textarea class="form-control" id="bio" name="meta[bio_description]" rows="3">{$user->meta['bio_description']|default:""}</textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="password">
                {t}Password{/t}
              </label>
              <div class="controls">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-key"></i>
                  </span>
                  <input class="form-control" id="password" minlength="6" name="password" data-min-strength="{$min_pass_level}" type="password" value="" {if $user->id eq null}required="required"{/if} maxlength="20"/>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="passwordconfirm">
                {t}Confirm password{/t}
              </label>
              <div class="controls">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-key"></i>
                  </span>
                  <input class="form-control validate-password-confirm" data-password-equals="password" id="passwordconfirm" maxlength="20" minlength=6 name="passwordconfirm" type="password" value=""/>
                </div>
                <span class="checker"></span>
              </div>
            </div>
          </div>
        </div>
        {acl isAllowed="USER_ADMIN"}
        {is_module_activated name="CONTENT_SUBSCRIPTIONS"}
        <div class="grid simple" ng-if="user.type == 1">
          <div class="grid-title">
            <h4>{t}Additional data{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="password">
                  {t}Registered date:{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="meta_register_date" type="datetime" name="meta[register_date]" value="{$user->meta['register_date']}"/>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="password">
                  {t}Postal Code:{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="meta_postal_code" type="text" name="meta[postal_code]" value="{$user->meta['postal_code']}"/>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="password">
                  {t}Birth Date:{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="meta_birth_date" type="datetime" name="meta[birth_date]" value="{$user->meta['birth_date']}"/>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="password">
                  {t}Gender:{/t}
                </label>
                <div class="controls">
                  <select name="meta[gender]" id="meta_gender">
                    {html_options options=$gender_options selected=$user->meta['gender']}
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        {/is_module_activated}
        {/acl}
      </div>
      <div class="col-md-4">
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Settings{/t}</h4>
              </div>
              <div class="grid-body">
                {acl isAllowed="USER_ADMIN"}
                <div class="form-group">
                  <div class="checkbox">
                    <input id="activated" name="activated" ng-model="user.activated" ng-true-value="'1'" ng-false-value="'0'" ng-checked="user.activated == 1" type="checkbox" value="1"/>
                    <label class="form-label" for="activated">
                      {t}Activated{/t}
                    </label>
                  </div>
                </div>
                {/acl}

                {acl isAllowed="USER_ADMIN"}
                <div class="form-group">
                  <div class="checkbox">
                    <input id="type" name="type" ng-checked="user.type == 0" ng-model="user.type" ng-true-value="'0'" ng-false-value="'1'" type="checkbox" value="0"/>
                    <label class="form-label" for="type">
                      {t}Has backend access{/t}
                    </label>
                    <div class="help m-t-5">{t}Used to enable or disable user access to control panel.{/t}</div>
                  </div>
                </div>
                {/acl}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Privileges{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label" for="id_user_group">{t}User group:{/t}</label>
                  <div class="controls" ng-init="groups = {json_encode($user_groups)|clear_json};selectedGroups = {json_encode($selected_groups)|clear_json}">
                    <multiselect ng-model="selectedGroups" options="g.name for g in groups" ms-header="{t}Select{/t}" ms-selected="[% selectedGroups.length %] {t}selected{/t}" data-compare-by="id" scroll-after-rows="5" data-multiple="true"></multiselect>
                  </div>
                  <div class="m-t-10 m-b-10">
                    <span class="badge m-r-5" ng-repeat="group in selectedGroups">
                      [% group.name %]
                      <input type="hidden" name="id_user_group[]" value="[% group.id %]">
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label" for="ids_category">{t}Categories{/t}:</label>
                  <div class="controls" ng-init="categories = {json_encode($content_categories)|clear_json};selectedCategories = {json_encode($content_categories_select)|clear_json}">
                    <multiselect ng-model="selectedCategories" options="c.title for c in categories" ms-header="{t}Select{/t}" ms-selected="[% selectedCategories.length %] {t}selected{/t}" data-compare-by="id" scroll-after-rows="5" data-multiple="true"></multiselect>
                  </div>
                  <div class="m-t-10 m-b-10">
                    <span class="badge m-r-5" ng-repeat="category in selectedCategories">
                      [% category.title %]
                      <input type="hidden" name="ids_category[]" value="[% category.id %]">
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {if isset($user->id) && $user->id == $smarty.session.userid}
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Social Networks{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="form-group">
                  <label class="control-label" for="facebook_login">{t}Facebook{/t}</label>
                  <div class="controls">
                    <iframe src="{url name=admin_acl_user_social id=$user->id resource='facebook'}" frameborder="0" style="width:100%;overflow-y:hidden;"></iframe>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label" for="twitter_login">{t}Twitter{/t}</label>
                  <div class="controls">
                    <iframe src="{url name=admin_acl_user_social id=$user->id resource='twitter'}" frameborder="0" style="width:100%;overflow-y:hidden;"></iframe>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {/if}
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Options{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="form-group">
                  <label class="form-label" for="meta">
                    {t}User language{/t}
                  </label>
                  <div class="controls">
                    {html_options name="meta[user_language]" options=$languages selected=$user->meta['user_language']}
                    <div class="help-blockacl/user/modals/_modalBatchUpdate.tpl">{t}Used for displayed messages, interface and measures in your page.{/t}</div>
                  </div>
                </div>
                {acl isAllowed="USER_ADMIN"}
                {is_module_activated name="PAYWALL"}
                <div class="form-group">
                  <label class="form-label" for="paywall_time_limit">
                    {t}Paywall time limit:{/t}
                  </label>
                  <div class="controls">
                    <input id="paywall_time_limit" name="paywall_time_limit" type="datetime" value="{datetime date=$user->meta['paywall_time_limit']}"/>
                  </div>
                </div>
                {/is_module_activated}
                {/acl}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-update-selected">
    {include file="acl/user/modals/_modalBatchUpdate.tpl"}
  </script>
</form>
{/block}
