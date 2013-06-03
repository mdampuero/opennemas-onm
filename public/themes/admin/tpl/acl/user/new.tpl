{extends file="base/admin.tpl"}

{block name="footer-js" append}
{script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
{script_tag src="/onm/jquery.password-strength.js" common=1}
<script>
    jQuery(document).ready(function($){
        $('[rel=tooltip]').tooltip({ placement: 'bottom', html: true });
        $("#user-editing-form").tabs();

        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });

        if($('select#usertype').val() == '1'){
                $('#id_user_group').removeAttr('required');
                $('#privileges').hide();
                $('.privileges-tab').hide();
            }


        $('select#usertype').change(function(){
            if($(this).val() == '1'){
                $('#id_user_group').removeAttr('required');
                $('#privileges').hide();
                $('.privileges-tab').hide();
            } else {
                $('#privileges').show();
                $('.privileges-tab').show();
                $("#id_user_group").attr("required", "required");
            }
        });

        var strength = $("#password").passStrength({
            userid: "#login"
        });

        {acl isAllowed="USER_ADMIN"}
            {is_module_activated name="PAYWALL"}
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
</style>
{/block}

{block name="content"}
<form action="{if isset($user->id)}{url name=admin_acl_user_update id=$user->id}{else}{url name=admin_acl_user_create}{/if}" method="POST" id="formulario">

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
                    <a href="{url name=admin_acl_user type=$user->type}">
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
                    <div class="avatar-image thumbnail"  rel="tooltip" data-original-title="{t escape=off}If you want a custom avatar sign up in <a href='http://www.gravatar.com'>gravatar.com</a> with the same email address as you have here in OpenNemas{/t}">
                        {if $user}
                            {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="150"}
                        {else}
                            <img src="{$params.IMAGE_DIR}default_avatar.png" alt="Default avatar" width=150>
                            {gravatar email="fake@mail.com" image_dir=$params.IMAGE_DIR image=true size=150}
                        {/if}
                    </div>
                </div>
                <div class="user-info form-vertical">

                    <fieldset>
                        <div class="control-group">
                            <label for="name" class="control-label">{t}Display name{/t}</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" value="{$user->name|default:""}" class="input-xlarge required" required="required" maxlength="50"/>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="control-group">
                            <label for="login" class="control-label">{t}User name{/t}</label>
                            <div class="controls">
                                <input type="text" id="login" name="login" value="{$user->login|default:""}" class="input-xlarge" required="required" maxlength="20"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="login" class="control-label">{t}Email{/t}</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on">@</span><input class=" input-large" id="email" type="email" name="email" value="{$user->email}" required="required">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="control-group">
                            <label for="password" class="control-label">{t}Password{/t}</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-key"></i></span>
                                    <input type="password" id="password" name="password" value="" class="input-medium" {if $user->id eq null}required="required"{/if} maxlength="20"/>
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
                        <label for="user_language" class="control-label">{t}User language{/t}</label>
                        <div class="controls">
                            {html_options name="user_language" options=$languages selected=$user->meta['user_language']}
                            <div class="help-block">{t}Used for displayed messages, interface and measures in your page.{/t}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="privileges">
                <table style="margin:1em;">
                    <tbody>
                       {acl isAllowed="GROUP_CHANGE"}
                        <tr>
                            <th scope="row">
                                <label for="id_user_group">{t}User group:{/t}</label>
                            </th>
                            <td>
                                <select id="id_user_group" name="id_user_group" title="{t}User group:{/t}"  required="required" class="validate-selection" onchange="onChangeGroup(this, new Array('comboAccessCategory','labelAccessCategory'));">
                                    <option  value ="">{t}--Select one--{/t}</option>
                                    {if $smarty.session.isMaster}
                                        <option value="4" {if $user->id_user_group == 4}selected="selected"{/if}>{t}Master{/t}</option>
                                    {/if}
                                    {section name=user_group loop=$user_groups}
                                        {if $user_groups[user_group]->id == $user->id_user_group}
                                            <option  value="{$user_groups[user_group]->id}" selected="selected">{$user_groups[user_group]->name}</option>
                                        {else}
                                            <option  value="{$user_groups[user_group]->id}">{$user_groups[user_group]->name}</option>
                                        {/if}
                                    {/section}
                                </select>
                            </td>
                        </tr>
                        {/acl}
                        {acl isAllowed="USER_CATEGORY"}
                        <tr {if !is_null($user) && $user->id_user_group == 5}style="display:none"{/if}</tr>
                            <th scope="row">
                                <label for="id_user_group">{t}Categories{/t}</label>
                            </th>
                            <td>
                                <div id="comboAccessCategory">
                                    <select id="ids_category" name="ids_category[]" size="12" title="Categorias" class="validate-selection" multiple="multiple">
                                        {if isset($content_categories_select) && count($content_categories_select)<=0}
                                            <option value ="" selected="selected"></option>
                                        {else}
                                            <option value =""></option>
                                        {/if}
                                        <option value="0" {if isset($content_categories_select) && is_array($content_categories_select) && in_array(0, $content_categories_select)} selected="selected" {/if}>{t}HOME{/t}</option>
                                        {foreach item="c_it" from=$content_categories}

                                            <option value="{$c_it->pk_content_category}" {if isset($content_categories_select) && is_array($content_categories_select) && in_array($c_it->pk_content_category, $content_categories_select)}selected="selected"{/if}>{$c_it->title}</option>

                                            {if count($c_it->childNodes)>0}
                                                {foreach item="sc_it" from=$c_it->childNodes}
                                                    <option value="{$sc_it->pk_content_category}" {if isset($content_categories_select) && is_array($content_categories_select) && in_array($sc_it->pk_content_category, $content_categories_select)} selected="selected" {/if}>
                                                            &nbsp; &rArr; {$sc_it->title}
                                                    </option>
                                                {/foreach}
                                            {/if}
                                        {/foreach}

                                    </select>

                                    <!--<select id="ids_category" name="ids_category[]" size="12" title="Categorias" class="validate-selection" multiple="multiple">
                                        <option value=""></option>
                                        {html_options options=$categories_options selected=$categories_selected}
                                    </select>-->

                                </div>
                            </td>
                        </tr>
                       {/acl}
                    </tbody>
                </table>
            </div><!-- /privileges -->

            {acl isAllowed="USER_ADMIN"}
            {is_module_activated name="PAYWALL"}
            <div id="paywall">
                <div class="form-horizontal">
                        <div class="control-group">
                        <label for="sessionexpire" class="control-label">{t}Paywall time limit:{/t}</label>
                        <div class="controls">
                            <input type="datetime" id="paywall_time_limit" name="meta[paywall_time_limit]" value="{datetime date=$user->meta['paywall_time_limit']}" />
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
