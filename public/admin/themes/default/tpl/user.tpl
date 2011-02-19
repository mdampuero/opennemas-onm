{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}
	 style="max-width:70% !important; margin: 0 auto; display:block;">

{* LIST ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}

{include file="botonera_up.tpl"}
{* Filters: filter[name], filter[login], filter[group]  *}
<table class="adminheading">
	<tr>
		<th nowrap="nowrap" align="right">
            Filter by
            <label for="username">{t}Name:{/t}</label>
            <input id="username" name="filter[name]" onchange="$('action').value='list';this.form.submit();" value="{$smarty.request.filter.name}" />
            &nbsp;&nbsp;&nbsp;

            <label for="userlogin">{t}Login:{/t}</label>
            <input id="userlogin" name="filter[login]" onchange="$('action').value='list';this.form.submit();" value="{$smarty.request.filter.login}" />
            &nbsp;&nbsp;&nbsp;

            <label for="usergroup">{t}Group:{/t}</label>
            <select id="usergroup" name="filter[group]" onchange="$('action').value='list';this.form.submit();">
                {html_options options=$groupsOptions selected=$smarty.request.filter.group}
            </select>

            <input type="hidden" name="page" value="{$smarty.request.page}" />
			<input type="submit" value="{t}Search{/t}">
        </th>
	</tr>
</table>

<table border="0" cellpadding="4" cellspacing="0" class="adminlist">
<thead>
<tr>
    <th class="title" align="left" style="width:40%;padding:10px;">{t}Name Surname{/t}</th>
    <th class="title" style="width:20%;padding:10px;">{t}Login{/t}</th>
    <th class="title" style="width:20%;padding:10px;">{t}Group{/t}</th>
    <th class="title" style="width:20%;padding:10px;" align="right">{t}Actions{/t}</th>
</tr>
</thead>

<tbody>
{section name=c loop=$users}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">

	<td style="padding:10px;">
                <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$users[c]->id}"  style="cursor:pointer;" ">
		<a href="?action=read&id={$users[c]->id}" title="{t}Edit user{/t}">
            {$users[c]->name}&nbsp;{$users[c]->firstname}&nbsp;{$users[c]->lastname}</a>
	</td>
	<td style="padding:10px;">
		{$users[c]->login}
	</td>
	<td style="padding:10px;">
		{section name=u loop=$user_groups}
			{if $user_groups[u]->id == $users[c]->fk_user_group}
					{$user_groups[u]->name}
	  		{/if}
		{/section}
	</td>
	<td style="padding:10px;" align="right">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$users[c]->id});" title="{t}Edit user{/t}">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" alt="{t}Edit user{/t}"/></a>
            &nbsp;
		<a href="#" onClick="javascript:confirmar(this, {$users[c]->id});" title="{t}Delete user{/t}">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" alt="{t}Delete user{/t}"/></a>
	</td>
</tr>
{sectionelse}
<tr colspan="5">
	<td align="center"><b>{t}There is no users created yet.{/t}</b></td>
</tr>
{/section}
</tbody>

{if count($users) gt 0}
    <tfoot>
    <tr>
        <td colspan="5" align="center">{$paginacion->links}</td>
    </tr>
    </tfoot>
{/if}

</table>
{/if}


{* FORM TO ADD/MODIFY A CONTENT ************************************** *}
{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}
<script language="javascript" type="text/javascript" src="{$params.JS_DIR}SpinnerControl.js"></script>
<script language="javascript" type="text/javascript" src="{$params.JS_DIR}modalbox.js"></script>

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}modalbox.css" media="screen" />

{literal}
<style>
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
{/literal}

{include file="botonera_up.tpl"}

	<table  border="0" cellpadding="4" cellspacing="0" class="fuente_cuerpo" width="100%">
	<tr>
        <td valign="top" width="50%">

			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
			<tbody>

			<!-- Login -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="login">{t}Login:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="login" name="login" title="Login del usuario"
						value="{$user->login}" class="required"  size="14" maxlength="20" />
				</td>
			</tr>

			<!-- Password -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="password">{t}Password:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="password" id="password" name="password" title="Password"  size="20" autocomplete="off"
						value="" class="{if $smarty.request.action eq "new"}required validate-password{/if}" />
				</td>
			</tr>

            <!-- Password Confirm-->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="passwordconfirm">{t}Re-enter password:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="password" id="passwordconfirm" name="passwordconfirm" title="Confirm Password"  size="20"
						value="" autocomplete="off" class="{if $smarty.request.action eq "new"}required{/if} validate-password-confirm" />
				</td>
			</tr>

            <!-- SessionExpire -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="sessionexpire">{t}Session expire time:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
                    <input type="text" id="sessionexpire" name="sessionexpire" title="Expiraci&oacute;n de Sessi&oacute;n"
						value="{$user->sessionexpire|default:"15"}" class="required validate-digits" style="text-align:right" size="4" />
                    <input id="up" class="spinner_button" type="button" value="+" />
                    <input id="dn" class="spinner_button" type="button" value="-" />
                    <sub>{t}minutes{/t}</sub>
				</td>
			</tr>

			<!-- Email Adress -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="email">{t}Email adress:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="email" name="email" title="{t}Email adress:{/t}"
						value="{$user->email}" class="required validate-email"  size="50"/>
				</td>
			</tr>

			<!-- Name -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="name">{t}Name:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="name" name="name" title="{t}Name:{/t}"
						value="{$user->name}" class="required"  size="50"/>
				</td>
			</tr>

			<!-- Surname -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="firstname">{t}Surname:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="firstname" name="firstname" title="{t}Surname:{/t}"
						value="{$user->firstname}" class="required"  size="50"/>
				</td>
			</tr>

			<!-- Maiden surname -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="lasname">{t}Maiden surname:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="lastname" name="lastname" title="{t}Maiden surname:{/t}"
						value="{$user->lastname}"  size="50"/>
				</td>
			</tr>

			<!-- Adress -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="address">{t}Address:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="address" name="address" title="{t}Address:{/t}"
						value="{$user->address}"  size="50"/>
				</td>
			</tr>

			<!-- Telephone -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="phone">{t}Telephone:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="phone" name="phone" title="{t}Telephone:{/t}" class="validate-digits"
						value="{$user->phone}"  size="15"/>
				</td>
			</tr>

            </tbody>
            </table>

        </td>
        <td valign="top" width="50%">
            
            <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
            </tbody>

			<!-- User_Group -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="id_user_group">{t}User group:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<select id="id_user_group" name="id_user_group" title="{t}User group:{/t}" class="validate-selection" onchange="onChangeGroup(this, new Array('comboAccessCategory','labelAccessCategory'));">
                        <option  value ="" selected="selected"> </OPTION>
						{section name=user_group loop=$user_groups}
							{if $user_groups[user_group]->id == $user->id_user_group}
                                <option  value = "{$user_groups[user_group]->id}" selected="selected">{$user_groups[user_group]->name}</option>
					  		{else}
								<option  value = "{$user_groups[user_group]->id}">{$user_groups[user_group]->name}</option>
							{/if}
						{/section}
					</select>

                    {* FIXME: separar todo a un fichero js que tenga las funcionalidades de los usuarios *}
                    {literal}
                    <script type="text/javascript">
                    function showGroupUsers(elto) {
                        /* Modalbox.show('user_groups.php?action=read&id=' + $('id_user_group').value, {
                            title: elto.title, width: 800, height: 640}); */
                        /*if( confirm('¿Está seguro de querer salir de la edición del usuario?') ) {
                            window.open('user_groups.php?action=read&id=' + $('id_user_group').value, 'centro');
                        }*/

                        Modalbox.show('<iframe width="100%" height="450" src="user_groups.php?action=read&id=' + $('id_user_group').value+'"  frameborder="0" marginheight="0" marginwidth="0"></iframe>', {title: '{t}User group manager{/t}', width: 760});
                    }
                    </script>
                    {/literal}

                    <a href="javascript:void(0);" title="{t}Edit groups and privileges{/t}" onclick="showGroupUsers(this);return false;">
                        <img src="{$params.IMAGE_DIR}users_edit.png" border="0" style="vertical-align: middle;" /></a>
				</td>
			</tr>

            <!-- Categories -->
            <tr>
				<td valign="top" align="right" style="padding:4px;">
                    <div id="labelAccessCategory" name="labelAccessCategory">
                        <label for="id_user_group">{t}Sections:{/t}</label>
                    </div>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
                    <div  id="comboAccessCategory" name="comboAccessCategory">
                        <select id="ids_category" name="ids_category[]" size="12" title="Categorias" class="validate-selection" multiple="multiple">
                            {if count($content_categories_select)<=0}
                                <option value ="" selected="selected"></option>
                            {else}
                                <option value =""></option>
                            {/if}
                            {foreach item="c_it" from=$content_categories}

                                <option value="{$c_it->pk_content_category}" {if is_array($content_categories_select) && in_array($c_it->pk_content_category, $content_categories_select)}selected="selected"{/if}>{$c_it->title}</option>

                                {if count($c_it->childNodes)>0}
                                    {foreach item="sc_it" from=$c_it->childNodes}
                                        <option value="{$sc_it->pk_content_category}" {if is_array($content_categories_select) && in_array($sc_it->pk_content_category, $content_categories_select)}selected="selected"{/if}>
                                    &nbsp; &rArr; {$sc_it->title}</option>

                                    {/foreach}
                                {/if}
                            {/foreach}

                            {*section name=category loop=$content_categories}
                                {if  isset($content_categories_select) &&
                                    !empty($content_categories_select) &&
                                    in_array($content_categories[category]->pk_content_category, $content_categories_select)}
                                    {if $content_categories[category]->fk_content_category == 0}
                                        <option  value = "{$content_categories[category]->pk_content_category}" selected="selected">{$content_categories[category]->title}</option>
                                    {else}
                                        <option  value = "{$content_categories[category]->pk_content_category}" selected="selected">&nbsp;&nbsp;&nbsp;{$content_categories[category]->title}</option>
                                    {/if}
                                {else}
                                    {if $content_categories[category]->fk_content_category == 0}
                                        <option  value = "{$content_categories[category]->pk_content_category}">{$content_categories[category]->title}</option>
                                    {else}
                                        <option  value = "{$content_categories[category]->pk_content_category}">&nbsp;&nbsp;&nbsp;{$content_categories[category]->title}</option>
                                    {/if}
                                {/if}
                            {/section*}
                        </select>

                        <!--<select id="ids_category" name="ids_category[]" size="12" title="Categorias" class="validate-selection" multiple="multiple">
                            <option value=""></option>
                            {html_options options=$categories_options selected=$categories_selected}
                        </select>-->

                    </div>
				</td>
			</tr>

			</tbody>
			</table>
	</td>
	</tr>
	</table>

{/if}

<input type="hidden" id="action" name="action" value="" /><input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}

{block name="footer-js"}
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