{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_usergroups}" method="post" name="formulario" id="formulario">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}User groups{/t}</h2></div>
            <ul class="old-button">
                {acl isAllowed="GROUP_CREATE"}
                    <li>
                        <a href="{url name="admin_acl_usergroups_create"}">
                            <img src="{$params.IMAGE_DIR}usergroup_add.png" title="{t}New Privilege{/t}" alt="{t}New User Group{/t}"><br />{t}New User group{/t}
                        </a>
                    </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>{t}Group name{/t}</th>
                    <th class="center" style="width:10px">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                {foreach name=c from=$user_groups item=group}
                <tr>
                    <td>
                        <a href="{url name="admin_acl_usergroups_show" id="{$group->id}"}" title="{t}Edit group{/t}">
                            {$group->name}
                        </a>
                    </td>
                    <td class="right nowrap">
                        <div class="btn-group">
							<a class="btn" href="{url name="admin_acl_usergroups_show" id="{$group->id}"}" title="{t}Edit group{/t}">
								<i class="icon-pencil"></i> {t}Edit{/t}
							</a>
							<a class="del btn btn-danger"
                                href="{url name=admin_acl_usergroups_delete id=$group->id}"
                                data-url="{url name=admin_acl_usergroups_delete id=$group->id}"
                                data-title="{$group->name}"
                                title="{t}Delete group{/t}">
								<i class="icon-trash icon-white"></i>
							</a>
                        </div>
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspa=2 class="empty">
                        {t}There is no groups created yet.{/t}
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="center">
                        <div class="pagination">
                            {$paginacion->links}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</form>
{include file="acl/user_group/modal/_modalDelete.tpl"}
{/block}
