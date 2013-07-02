{extends file="base/base.tpl"}

{block name="footer-js" append}
{script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
{script_tag src="/jquery/jquery.multiselect.js" common=1}
{script_tag src="/onm/jquery.password-strength.js" common=1}
{script_tag src="/onm/bootstrap-fileupload.min.js" common=1}
<script>
    jQuery(document).ready(function($){
        $('[rel=tooltip]').tooltip({ placement: 'bottom', html: true });
        $("#user-editing-form").tabs();

        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });

        // Show/hide privilege tab depending on userType backend/frontend
        if($('select#usertype').val() == '1') {
            $('#id_user_group').removeAttr('required');
            $('#privileges').hide();
            $('.privileges-tab').hide();
        }
        $('select#usertype').change(function() {
            if($(this).val() == '1'){
                $('#id_user_group').removeAttr('required');
                $('#privileges').hide();
                $('.privileges-tab').hide();
            } else {
                $('#privileges').show();
                $('.privileges-tab').show();
                $('#id_user_group').attr('required', 'required');
            }
        });

        // Password strength checker
        var strength = $('#password').passStrength({
            userid: '#login'
        });

        // Avatar image uploader
        $('.fileupload').fileupload({
            name: 'avatar',
            uploadtype:'image'
        });

        $('.delete').on('click', function(){
            $('.file-input').val('0');
        })

        // Use multiselect on user groups and categories
        $('select#id_user_group').twosidedmultiselect();
        $('select#ids_category').twosidedmultiselect();

        // Paywall datepicker only if available
        {acl isAllowed='USER_ADMIN'}
            {is_module_activated name='PAYWALL'}
            jQuery('#paywall_time_limit').datetimepicker({
                hourGrid: 4,
                showAnim: 'fadeIn',
                dateFormat: 'yy-mm-dd',
                timeFormat: 'hh:mm:ss',
                minuteGrid: 10
            });
            {/is_module_activated}
        {/acl}
    });
</script>
{/block}


{block name="header-css" append}
{css_tag href="/bootstrap/bootstrap-fileupload.min.css" common=1}
<style type="text/css">
label {
    font-weight:normal;
}
.avatar, .user-info {
    vertical-align: top;
    display:inline-block;
}
.avatar {
    margin-right:20px;
}
.avatar img {
    width:150px;
    height:150px;
}

.tooltip {
    max-width:160px;
}
/* Styles for password strenght */
.alert-pass {
    background: #F8D47A url("/assets/images/alert-ok-small.png") no-repeat 16px;
    display: inline-block;
    margin: 0;
    padding: 5px 15px 5px 50px;
    margin-left: 10px;
    border-radius: 5px;
    font-size: 14px;
    color: white;
}
.alert-pass.alert-success { background: #468847 url("/assets/images/alert-ok-small.png") no-repeat 16px; }
.alert-pass.alert-error { background: #B22222 url("/assets/images/alert-error-small.png") no-repeat 16px; }
/* Recommended styles tsms */
.tsmsselect {
        float: left;
}

.tsmsselect select {
}

.tsmsoptions {
        width: 10%;
        float: left;
}

.tsmsoptions p {
        margin: 2px;
        text-align: center;
        font-size: larger;
        cursor: pointer;
}

.tsmsoptions p:hover {
        color: White;
        background-color: Silver;
}
.groups, .categorys {
    display: inline-block;
    width: 100%;
}
</style>
{/block}

{block name="content"}
<form action="{if isset($user->id)}{url name=manager_acl_user_update id=$user->id}{else}{url name=manager_acl_user_create}{/if}" method="POST" enctype="multipart/form-data" id="formulario" autocomplete="off">

	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{if isset($user->id)}{t}Editing user{/t}{else}{t}Creating user{/t}{/if}</h2></div>
			<ul class="old-button">
                <li>
                    <button action="submit"  name="action" value="validate">
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=manager_acl_user type=$user->type}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
			</ul>
		</div>
	</div>

    <div class="wrapper-content">
        {render_messages}
        <div id="user-editing-form" class="wrapper-content tabs">
            <ul>
                <li><a href="#basic" title="{t}Basic information{/t}">{t}User info{/t}</a></li>
                <li><a href="#settings" title="{t}Settings{/t}">{t}Settings{/t}</a></li>
                <li><a class="privileges-tab" href="#privileges" title="{t}Privileges{/t}">{t}Privileges{/t}</a></li>
                {acl isAllowed="USER_ADMIN"}
                {is_module_activated name="PAYWALL"}
                <li><a href="#paywall" title="{t}Paywall{/t}">{t}Paywall{/t}</a></li>
                {/is_module_activated}
                {/acl}
            </ul><!-- / -->
            <div id="basic">
                <div class="avatar">
                    <div class="fileupload {if $user->photo}fileupload-exists{else}fileupload-new{/if}" data-provides="fileupload">
                        {if $user->photo->name}
                        <div class="fileupload-preview thumbnail" style="width: 140px; height: 140px;">
                            <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$user->photo->path_file}/{$user->photo->name}" alt="{t}Photo{/t}"/>
                        </div>
                        {else}
                        <div class="fileupload-preview thumbnail" style="width: 140px; height: 140px;" rel="tooltip" data-original-title="{t escape=off}If you want a custom avatar sign up in <a href='http://www.gravatar.com'>gravatar.com</a> with the same email address as you have here in OpenNemas{/t}">
                            {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="150"}
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

                <div class="user-info form-vertical">
                    <fieldset>
                        <div class="control-group">
                            <label for="name" class="control-label">{t}Display name{/t}</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" value="{$user->name|default:""}" class="input-xlarge required" maxlength="50"/>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="control-group">
                            <label for="login" class="control-label">{t}User name{/t}</label>
                            <div class="controls">
                                <input type="text" id="login" name="login" value="{$user->username|default:""}" class="input-xlarge" required=required maxlength="20"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="email" class="control-label">{t}Email{/t}</label>
                            <div class="controls">
                                <input class="input-xlarge" id="email" type="email" name="email" placeholder="test@example.com"  value="{$user->email|default:""}" required="required">
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="meta[twitter]" class="control-label">{t}Twitter user{/t}</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on">@</span>
                                    <input class="span2" id="prependedInput" type="text" placeholder="{t}Username{/t}" id="meta[twitter]" name="meta[twitter]" value="{$user->meta['twitter']|default:""}">
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="url" class="control-label">{t}Blog Url{/t}</label>
                            <div class="controls">
                                <input type="text" name="url" id="url" placeholder="http://" value="{$user->url|default:""}" class="input-xxlarge" >
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="bio" class="control-label">{t}Biography{/t}</label>
                            <div class="controls">
                                <textarea id="bio" name="bio" rows="3" class="input-xxlarge">{$user->bio|default:""}</textarea>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="control-group">
                            <label for="password" class="control-label">{t}Password{/t}</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-key"></i></span>
                                    <input type="password" id="password" name="password" value="" class="input-medium {if $smarty.request.action eq "new"}required{/if}" maxlength="20"/>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="passwordconfirm" class="control-label">{t}Confirm password{/t}</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-key"></i></span>
                                    <input type="password" id="passwordconfirm" name="passwordconfirm" value="" data-password-equals="password" class="input-medium {if $smarty.request.action eq "new"}required{/if} validate-password-confirm" maxlength="20"/>
                                </div>
                            </div>
                        </div>
                    <fieldset>
                </div>

            </div><!-- /personal -->
            <div id="settings">
                <div class="form-horizontal">
                        <div class="control-group">
                        <label for="sessionexpire" class="control-label">{t}Session expire time:{/t}</label>
                        <div class="controls">
                            <input type="number" id="sessionexpire" name="sessionexpire" value="{$user->sessionexpire|default:"15"}" class="input-mini validate-digits" maxlength="20"/>
                            <span>{t}minutes{/t}</span>
                        </div>
                    </div>

                    {is_module_activated name="PAYWALL"}
                    <div class="control-group">
                        <label for="user_language" class="control-label">{t}User type{/t}</label>
                        <div class="controls">
                            <select id="usertype" name="type">
                                <option value="0" {if ($user->type eq "0")}selected{/if}>{t}Backend{/t}</option>
                                <option value="1" {if ($user->type eq "1")}selected{/if}>{t}Frontend{/t}</option>
                            </select>
                        </div>
                    </div>
                    {/is_module_activated}

                    <div class="control-group">
                        <label for="meta[user_language]" class="control-label">{t}User language{/t}</label>
                        <div class="controls">
                            {html_options name="meta[user_language]" options=$languages selected=$user->meta['user_language']}
                            <div class="help-block">{t}Used for displayed messages, interface and measures in your page.{/t}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="privileges">
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
            <label>&nbsp;</label>
            </div><!-- /privileges -->

            {acl isAllowed="USER_ADMIN"}
            {is_module_activated name="PAYWALL"}
            <div id="paywall">
                <div class="form-horizontal">
                        <div class="control-group">
                        <label for="paywall_time_limit" class="control-label">{t}Paywall time limit:{/t}</label>
                        <div class="controls">
                            <input type="datetime" id="paywall_time_limit" name="paywall_time_limit" value="{datetime date=$user->meta['paywall_time_limit']}" />
                        </div>
                    </div>
                </div>
            </div>
            {/is_module_activated}
            {/acl}
            <!-- paywall -->
        </div>
    </div>
</form>
{/block}
