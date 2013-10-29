{extends file="base/base.tpl"}

{block name="footer-js" append}
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });
    });
    </script>
{/block}

{block name="content"}
<form action="{if isset($user_group->id)}{url name="manager_acl_usergroups_update" id=$user_group->id}{else}{url name="manager_acl_usergroups_create"}{/if}" method="post" name="formulario" id="formulario">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if isset($user_group->id)}{t}Editing group{/t}{else}{t}Creating user group{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit" name="action" value="validate">
                        <img border="0" src="{$params.COMMON_ASSET_DIR}images/save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name="manager_acl_usergroups"}" title="{t}Go back{/t}">
                        <img src="{$params.COMMON_ASSET_DIR}images/previous.png" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="name" class="control-label">{t}Group name{/t}</label>
                <div class="controls">
                    <input type="text" id="name" name="name" value="{$user_group->name}" class="input-xxlarge" required="required"
                        {if $user_group->name eq $smarty.const.SYS_NAME_GROUP_ADMIN}disabled="disabled"{/if} />
                </div>
            </div>
            <div class="control-group" id="privileges">
                <label for="privileges" class="control-label">{t}Privileges{/t}</label>
                <div class="controls">

                    {foreach item=privileges from=$modules key=mod name=priv}
                    {if $smarty.foreach.priv.first || $smarty.foreach.priv.index % 2 == 0}<div style="display:block; width:100%" class="clearfix">{/if}
                        <table  class="table table-condensed table-hover" style="display:inline-block; width:49%; float:left; margin-right:2px;">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="toggleallcheckbox"></th>
                                    <th>{$mod}</th>
                                </tr>
                            </thead>
                            <tbody id="{$mod}">
                            {section name=privilege loop=$privileges}
                                <tr>
                                    <td style="padding:4px;" nowrap="nowrap" width="5%">
                                        <label for="{$privileges[privilege]->id}" style="cursor:pointer;">
                                            {if $user_group->containsPrivilege($privileges[privilege]->id)}
                                               <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}" checked>
                                            {else}
                                               <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}">
                                            {/if}
                                        </label>
                                    </td>
                                    <td>
                                        {t}{$privileges[privilege]->description}{/t}
                                    </td>
                                </tr>
                            {/section}
                            </tbody>
                        </table>
                    {if $smarty.foreach.priv.last || $smarty.foreach.priv.index % 2 == 1}</div>{/if}
                    {/foreach}
                </div>
            </div>
        </div>

	</div>
    <input type="hidden" name="id" value="{$user_group->id}">
</form>
{/block}
