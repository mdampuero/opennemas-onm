{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}User group manager{/t} :: {t 1=$user_group->name}Editing %1{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user_group->id}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                <li>
                {if isset($user_group->id)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$user_group->id|default:""}, 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0,'formulario');">
                {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save{/t}" alt="Guardar y salir"><br />{t}Save{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=list" value="{t}Go back{/t}" title="{t}Go back{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <table class="adminform">
            <tbody>
            <!-- Id -->
            <tr>
                <td align="right" style="padding:4px;" width="20%">
                    <label for="id">{* Id: *}</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="80%">
                    <input type="hidden" id="idReadOnly" name="idReadOnly" title="{t}Id{/t}"
                        value="{$user_group->id}" readonly />
                </td>
            </tr>
            <!-- Nome -->
            <tr>
                <td valign="top" align="right" style="padding:4px;" width="20%">
                    <label for="name">{t}Name:{/t}</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="80%">
                    <input type="text" id="name" name="name" title="{t}Group name{/t}"
                        value="{$user_group->name}" class="required"
                        {if $user_group->name eq $smarty.const.SYS_NAME_GROUP_ADMIN}disabled="disabled"{/if} />
                </td>
            </tr>
            <!-- Privileges -->
            <tr>
                <td valign="top" align="right" style="padding:4px;" width="20%">
                    <label for="privileges">{t}Grants:{/t}</label>
                </td>

                <td style="padding:4px;" nowrap="nowrap" width="80%">
                    {foreach item=privileges from=$modules key=mod name=priv}
                    <div style="width:90%">
                        <div>
                            <table  class="listing-table">
                                <thead>
                                    <tr>
                                        <th colspan=2 onClick="Element.toggle('{$mod}');" style="cursor:pointer;">{t}{$mod}{/t}</th>
                                    </tr>
                                </thead>
                                <tbody id="{$mod}" style="display:none">
                                {section name=privilege loop=$privileges}
                                    <tr>
                                        <td style="padding:4px;" nowrap="nowrap" width="5%">
                                         <label style="cursor:pointer;">
                                        {if $user_group->containsPrivilege($privileges[privilege]->id)}
                                           <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}" checked>
                                           <script  type="text/javascript">
                                                $('{$mod}').setStyle('display:block');
                                           </script>

                                        {else}
                                           <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}">
                                        {/if}
                                              {t}{$privileges[privilege]->description}{/t} </label>
                                        </td>
                                    </tr>
                                {/section}
                            </tbody>
                            </table>
                        </div>
                    </div>
                    {/foreach}
                </td>
            </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="center">{$paginacion->links|default:""}</td>
                </tr>
            </tfoot>
        </table>
	</div>

	<input type="hidden" id="action" name="action" value="" />
	<input type="hidden" name="id" id="id" value="{$id|default:""}" />

</form>
{/block}
