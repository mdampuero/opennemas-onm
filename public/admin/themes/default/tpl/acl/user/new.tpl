{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript">
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
table th, table label {
    color: #888;
    text-shadow: white 0 1px 0;
    font-size: 13px;
}
th {
    vertical-align: top;
    text-align: left;
    padding: 10px;
    width: 200px;
    font-size: 13px;
}
label{
    font-weight:normal;
}
.panel {
    background:White;
}
fieldset {
    border:none;
    border-top:1px solid #ccc;
}
legend {
    color:#666;
    text-transform:uppercase;
    font-size:13px;
    padding:0 10px;
}

.awesome {
    border:0;
}
.panel {
    margin:0;
}
.default-value {
    display:inline;
    color:#666;
    margin-left:10px;
    vertical-align:middle
}
input[type="text"],
input[type="password"]{
    width:300px;
    max-height:80%
}
.spinner_button {
    width: 18px;
    height: 18px;

    color: #204A87;
    font-weight: bold;
    background-color: #DDD;

    border-top: 1px solid #CCC;
    border-right: 1px solid #999;
    border-bottom: 1px solid #999;
    border-left: 1px solid #CCC;
}

.spinner_button:hover {
    background-color: #EEE;

    border-top: 1px solid #DDD;
    border-right: 1px solid #CCC;
    border-bottom: 1px solid #CCC;
    border-left: 1px solid #DDD;
}
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/SpinnerControl.js" language="javascript"}
{script_tag src="/modalbox.js" language="javascript"}

{* FIXME: separar todo a un fichero js que tenga las funcionalidades de los usuarios *}
<script type="text/javascript">
function showGroupUsers(elto) {
    /* Modalbox.show('user_groups.php?action=read&id=' + $('id_user_group').value, {
        title: elto.title, width: 800, height: 640}); */
    /*if( confirm('¿Está seguro de querer salir de la edición del usuario?') ) {
        window.open('user_groups.php?action=read&id=' + $('id_user_group').value, 'centro');
    }*/
    Modalbox.show('<iframe width="100%" height="450" src="user_groups.php?action=read&id=' + $('id_user_group').value+'"  frameborder="0" marginheight="0" marginwidth="0"></iframe>', { title: '{t}User group manager{/t}', width: 760 });
}
</script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{$titulo_barra} :: {t}Editing user information{/t}</h2></div>
			<ul class="old-button">
				<li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user->id}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>

                <li>
                {if isset($user->id)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$user->id}, 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0, 'formulario');">
                {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}"><br />{t}Save and exit{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="{t}Cancel{/t}" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
			</ul>
		</div>
	</div>


    <div class="wrapper-content">
        <table class="adminform"  width="100%">
            <tr>
                <td valign="top">

                    <table style="margin:1em;">
                        <tbody>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="login">{t}Login:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="login" name="login" title="Login del usuario"
                                        value="{$user->login}" class="required"  size="14" maxlength="20" />
                                </td>
                                <td rowspan="12">
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Complete user information{/t}</h4></div>
                                        <div class="content">{t escape=off}Please complete the user information by filling the aside form.{/t}</div>
                                    </div>
                                    <br>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Do you want a custom avatar?{/t}</h4></div>
                                        <div class="content">{t escape=off}Sign up in <a href="http://www.gravatar.com">gravatar.com</a> and ensure that you use the same email as you have here in OpenNemas{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="password">{t}Password:{/t}</label>
                                </th>
                                <td>
                                    <input type="password" id="password" name="password" title="Password"  size="20" autocomplete="off"
                                        value="" class="{if $smarty.request.action eq "new"}required validate-password{/if}" />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="passwordconfirm">{t}Re-enter password:{/t}</label>
                                </th>
                                <td>
                                    <input type="password" id="passwordconfirm" name="passwordconfirm" title="Confirm Password"  size="20"
                                            value="" autocomplete="off" class="{if $smarty.request.action eq "new"}required{/if} validate-password-confirm" />
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="sessionexpire">{t}Session expire time:{/t}</label>
                                </th>
                                <td>
                                    <input type="number" id="sessionexpire" name="sessionexpire" title="Expiraci&oacute;n de Sessi&oacute;n"
                                        value="{$user->sessionexpire|default:"15"}" class="required validate-digits" style="text-align:right" size="4" />
                                    <apan>{t}minutes{/t}</span>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="email">{t}Email adress:{/t}</label>
                                </th>
                                <td>
                                    <input type="email" id="email" name="email" title="{t}Email adress:{/t}"
                                        value="{$user->email}" class="required validate-email"  size="50"/>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="name">{t}Name:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="name" name="name" title="{t}Name:{/t}"
                                        value="{$user->name}" class="required"  size="50"/>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="firstname">{t}Surname:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="firstname" name="firstname" title="{t}Surname:{/t}"
                                        value="{$user->firstname}" class="required"  size="50"/>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="lasname">{t}Maiden surname:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="lastname" name="lastname" title="{t}Maiden surname:{/t}"
                                        value="{$user->lastname}"  size="50"/>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="address">{t}Address:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="address" name="address" title="{t}Address:{/t}"
                                        value="{$user->address}"  size="50"/>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="phone">{t}Telephone:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="phone" name="phone" title="{t}Telephone:{/t}" class="validate-digits"
                                        value="{$user->phone}"  size="15"/>
                                </td>
                            </tr>
                           {acl isAllowed="GROUP_CHANGE"}
                            <tr valign="top">
                                <th scope="row">
                                    <label for="id_user_group">{t}User group:{/t}</label>
                                </th>
                                <td>
                                    <select id="id_user_group" name="id_user_group" title="{t}User group:{/t}" class="validate-selection" onchange="onChangeGroup(this, new Array('comboAccessCategory','labelAccessCategory'));">
                                        <option  value ="" selected="selected"> </option>
                                        {section name=user_group loop=$user_groups}
                                            {if $user_groups[user_group]->id == $user->id_user_group}
                                                <option  value = "{$user_groups[user_group]->id}" selected="selected">{$user_groups[user_group]->name}</option>
                                            {else}
                                                <option  value = "{$user_groups[user_group]->id}">{$user_groups[user_group]->name}</option>
                                            {/if}
                                        {/section}
                                    </select>

                                    <a href="javascript:void(0);" title="{t}Edit groups and privileges{/t}" onclick="showGroupUsers(this);return false;">
                                        <img src="{$params.IMAGE_DIR}users_edit.png" border="0" style="vertical-align: middle;" /></a>
                                </td>
                            </tr>
                            {/acl}
                            {acl isAllowed="USER_CATEGORY"}
                            <tr valign="top">
                                <th scope="row">
                                    <label for="id_user_group">{t}Sections:{/t}</label>
                                </th>
                                <td>
                                    <div  id="comboAccessCategory" name="comboAccessCategory">
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
                </td>
            </tr>
            <tfoot>
                <tr>
                    <td colspan=2></td>
                </tr>
            </tfoot>
        </table>
		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$id|default:""}" />
	</form>
</div>
{/block}
