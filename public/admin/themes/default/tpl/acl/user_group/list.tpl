{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_usergroups}" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}User group management{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{url name="admin_acl_usergroups_create"}">
                        <img src="{$params.IMAGE_DIR}usergroup_add.png" title="{t}New Privilege{/t}" alt="{t}New User Group{/t}"><br />{t}New User group{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <table class="listing-table table-striped">
            <thead>
                <tr>
                    <th>{t}Group name{/t}</th>
                    <th class="center" style="width:110px">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                {section name=c loop=$user_groups}
                <tr>
                    <td>
                        <a href="{url name="admin_acl_usergroups_show" id="{$user_groups[c]->id}"}" title="{t}Edit group{/t}">
                            {$user_groups[c]->name}
                        </a>
                    </td>
                    <td class="center">
                        <div class="btn-group">
							<a class="btn" href="{url name="admin_acl_usergroups_show" id="{$user_groups[c]->id}"}" title="{t}Edit group{/t}">
								<i class="icon-pencil"></i> {t}Edit{/t}
							</a>
							<a class="del btn btn-danger"
                                href="{url name=admin_acl_usergroups_delete id=$user_groups[c]->id}"
                                data-url="{url name=admin_acl_usergroups_delete id=$user_groups[c]->id}"
                                data-title="{$user_groups[c]->name}"
                                title="{t}Delete group{/t}">
								<i class="icon-trash icon-white"></i>
							</a>
                        </div>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td colspa=2 class="empty">
                        {t}There is no groups created yet.{/t}
                    </td>
                </tr>
                {/section}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        {$paginacion->links|default:""}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{include file="acl/user_group/modal/_modalDelete.tpl"}
{/block}
