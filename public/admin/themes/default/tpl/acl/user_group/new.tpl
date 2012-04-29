{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if isset($user_group->id)}{url name="admin_acl_usergroups_update" id=$user_group->id}{else}{url name="admin_acl_usergroups_create"}{/if}" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}User group manager{/t} :: {t 1=$user_group->name}Editing %1{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit" name="action" name="validate">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </button>
                </li>
                <li>
                {if isset($user_group->id)}
                    <button type="submit" name="action" name="update">
                {else}
                    <button type="submit" name="action" name="create">
                {/if}
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name="admin_acl_usergroups"}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <table class="adminform">
            <tbody>
            <tr>
                <td align="right" style="padding:4px;" width="20%">
                    <label for="id">{* Id: *}</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="80%">
                    <input type="hidden" id="idReadOnly" name="idReadOnly" title="{t}Id{/t}"
                        value="{$user_group->id}" readonly />
                </td>
            </tr>
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
                                        {if $user_group->contains_privilege($privileges[privilege]->id)}
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
	<input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}
