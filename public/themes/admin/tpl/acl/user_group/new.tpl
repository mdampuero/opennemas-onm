{extends file="base/admin.tpl"}

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
<form action="{if isset($user_group->id)}{url name="admin_acl_usergroups_update" id=$user_group->id}{else}{url name="admin_acl_usergroups_create"}{/if}" method="post">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-users"></i>
                            {t}User groups{/t}
                        </h4>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <h5>
                            {if isset($user_group->id)}{t}Editing group{/t}{else}{t}Creating user group{/t}{/if}
                        </h5>
                    </li>
                </ul>
            </div>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" href="{url name="admin_acl_usergroups"}" title="{t}Go back{/t}">
                            <i class="fa fa-reply"></i>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-save"></i>
                            {t}Save{/t}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content">

        {render_messages}

        <div class="grid simple">
            <div class="grid-title">
                <h4>{t}Basic information{/t}</h4>
            </div>
            <div class="grid-body">
                <div class="form-group">
                    <label for="name" class="form-label">{t}Group name{/t}</label>
                    <div class="controls">
                        <input type="text" id="name" name="name" value="{$user_group->name}" class="form-control" required="required"
                            {if $user_group->name eq $smarty.const.SYS_NAME_GROUP_ADMIN}disabled="disabled"{/if} />
                    </div>
                </div>
            </div>
        </div>

        <div class="grid simple">
            <div class="grid-title">
                <h4>{t}Privileges{/t}</h4>
            </div>

            <div class="grid-body" id="privileges">
                <div class="controls row">
                    {foreach item=privileges from=$modules key=mod name=priv}
                    <!-- {if $smarty.foreach.priv.first || $smarty.foreach.priv.index % 2 == 0}<div style="display:block; width:100%" class="clearfix">{/if} -->
                        <table  class="table table-condensed table-hover" class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="toggleallcheckbox"></th>
                                    <th>{$mod}</th>
                                </tr>
                            </thead>
                            <tbody id="{$mod}">
                            {section name=privilege loop=$privileges}
                                <tr>
                                    <td nowrap="nowrap" width="10px">
                                        <label for="{$privileges[privilege]->id}" style="cursor:pointer;">
                                          <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}" {if $user_group->containsPrivilege($privileges[privilege]->id)}checked{/if}>
                                        </label>
                                    </td>
                                    <td>
                                        {t}{$privileges[privilege]->description}{/t}
                                    </td>
                                </tr>
                            {/section}
                            </tbody>
                        </table>
                    <!-- {if $smarty.foreach.priv.last || $smarty.foreach.priv.index % 2 == 1}</div>{/if} -->
                    {/foreach}
                </div>
            </div>
        </div>

	</div>
    <input type="hidden" name="id" value="{$user_group->id}">
</form>
{/block}
