<table class="table table-hover table-condensed">
    {if count($users) > 0}
    <thead>
        <tr>
            <th class="left">{t}User name{/t}</th>
            <th class="left">{t}End of subscription{/t}</th>
            {if $show_edit_button}
            <th style="width:10px"></th>
            {/if}

        </tr>
    </thead>
    {else}
    <thead>
        <tr>
            <th colspan="11">
                &nbsp;
            </th>

        </tr>
    </thead>
    {/if}
    <tbody class="sortable">
    {foreach from=$users item=user}
    <tr data-id="{$user->id}">
        <td class="left">
            <a href="{url name=admin_acl_user_show id=$user->id}#paywall">{$user->name|clearslash}</a>
        </td>
        <td class="left">
            {$user->meta['paywall_time_limit']|clearslash}
        </td>
        {if $show_edit_button}
        <td class="center">
            <a href="{url name=admin_acl_user_show id=$user->id}#paywall" class="btn" title="{t}Edit{/t}"><i class="icon-pencil"></i></a>
        </td>
        {/if}

    </tr>
    {foreachelse}
    <tr>
        <td class="empty" colspan="11">{t}No users with paywall{/t}</td>
    </tr>
    {/foreach}
    </tbody>
    <tfoot>
        <tr>
            <td colspan="11" class="center">
                <div class="pagination">
                    {$pagination->links}
                </div>
            </td>
        </tr>
    </tfoot>
</table>