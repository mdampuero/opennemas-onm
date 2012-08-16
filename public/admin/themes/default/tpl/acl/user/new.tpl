{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {include file="acl/user/modal/_modal_edit_user_group.tpl"}
    {script_tag src="/SpinnerControl.js"}
    <script>
        jQuery(document).ready(function($){
            $("#user-editing-form").tabs();
        })
        document.observe('dom:loaded', function(){
            onChangeGroup( document.formulario.id_user_group, new Array('comboAccessCategory','labelAccessCategory') );

            // Refrescar los elementos seleccionados
            $('ids_category').select('option').each(function(item){
                if( item.getAttribute('selected') ) {
                    item.selected=true;
                    item.setAttribute('selected', 'selected');
                }
            });

            new SpinnerControl('sessionexpire', 'up', 'dn', { interval: 5,  min: 15, max: 250 });
        });
    </script>
{/block}


{block name="header-css" append}
<style type="text/css">
    label {
        font-weight:normal;
    }
</style>
{/block}

{block name="content"}
<form action="{if isset($user->id)}{url name=admin_acl_user_update id=$user->id}{else}{url name=admin_acl_user_create}{/if}" method="POST">

	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}User manager{/t} :: {t}Editing user information{/t}</h2></div>
			<ul class="old-button">
                <li>
                {if isset($user->id)}
                    <button action="submit" name="action" value="update">
                {else}
                    <button action="submit" name="action" value="create">
                {/if}
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li>
                    <button action="submit" name="action" value="validate">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_acl_user}">
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
                <li><a href="#basic" title="{t}Basic information{/t}">{t}Basic information{/t}</a></li>
                <li><a href="#privileges" title="{t}Privileges{/t}">{t}Privileges{/t}</a></li>
            </ul><!-- / -->
            <div id="basic" class="form-horizontal">
                <fieldset>
                    <div class="control-group">
                        <label for="login" class="control-label">{t}User name{/t}</label>
                        <div class="controls">
                            <input type="text" id="login" name="login" value="{$user->login|default:""}" class="input-xlarge required" maxlength="20"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="login" class="control-label">{t}Email{/t}</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">@</span><input class="span2" id="email" type="email" name="email" value="{$user->email}" class="required validate-email"  size="50">
                            </div>
                            <div class="help-block">
                                {t escape=off}Sign up in <a href="http://www.gravatar.com">gravatar.com</a> and ensure that you use the same email as you have here in OpenNemas{/t}
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <div class="control-group">
                        <label for="password" class="control-label">{t}Password{/t}</label>
                        <div class="controls">
                            <input type="password" id="password" name="password" value="" class="input-medium {if $smarty.request.action eq "new"}required{/if}" maxlength="20"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="passwordconfirm" class="control-label">{t}Confirm password{/t}</label>
                        <div class="controls">
                            <input type="password" id="passwordconfirm" name="passwordconfirm" value="" class="input-medium {if $smarty.request.action eq "new"}required{/if} validate-password-confirm" maxlength="20"/>
                        </div>
                    </div>
                <fieldset>

                <fieldset>
                    <div class="control-group">
                        <label for="name" class="control-label">{t}Name{/t}</label>
                        <div class="controls">
                            <input type="text" id="name" name="name" value="{$user->name|default:""}" class="input-xlarge required" maxlength="50"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="firstname" class="control-label">{t}Surname{/t}</label>
                        <div class="controls">
                            <input type="text" id="firstname" name="firstname" value="{$user->firstname|default:""}" class="input-xlarge required" maxlength="50"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="lastname" class="control-label">{t}Maiden surname{/t}</label>
                        <div class="controls">
                            <input type="text" id="lastname" name="lastname" value="{$user->lastname|default:""}" class="input-xlarge required" maxlength="50"/>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="control-group">
                        <label for="sessionexpire" class="control-label">{t}Session expire time:{/t}</label>
                        <div class="controls">
                            <input type="number" id="sessionexpire" name="sessionexpire" value="{$user->sessionexpire|default:"15"}" class="input-mini validate-digits" maxlength="20"/>
                            <span>{t}minutes{/t}</span>
                        </div>
                    </div>
                </fieldset>
            </div><!-- /personal -->
            <div id="privileges">
                <table style="margin:1em;">
                    <tbody>
                       {acl isAllowed="GROUP_CHANGE"}
                        <tr>
                            <th scope="row">
                                <label for="id_user_group">{t}User group:{/t}</label>
                            </th>
                            <td>
                                <select id="id_user_group" name="id_user_group" title="{t}User group:{/t}" class="validate-selection" onchange="onChangeGroup(this, new Array('comboAccessCategory','labelAccessCategory'));">
                                    <option  value ="">{t}--Select one--{/t}</option>
                                    {section name=user_group loop=$user_groups}
                                        {if $user_groups[user_group]->id == $user->id_user_group}
                                            <option  value = "{$user_groups[user_group]->id}" selected="selected">{$user_groups[user_group]->name}</option>
                                        {else}
                                            <option  value = "{$user_groups[user_group]->id}">{$user_groups[user_group]->name}</option>
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
        </div>
    </div>
</form>
{/block}
