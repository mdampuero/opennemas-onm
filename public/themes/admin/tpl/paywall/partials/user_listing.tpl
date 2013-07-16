<table class="table table-hover table-condensed">
    {if count($users) > 0}
    <thead>
        <tr>
            <th class="left">{t}User name{/t}</th>
            <th class="left">{t}E-mail{/t}</th>
            <th class="center">{t}Last login{/t}</th>
            <th class="center">{t}End of subscription{/t}</th>
            <th class="center">{t}Status{/t}</th>
            {if $show_edit_button}
            <th style="width:10px">{t}Edit{/t}</th>
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
            <a href="mailto:{$user->email|clearslash}" >
                {$user->email|clearslash}
            </a>
        </td>
        <td class="center">
            {if isset($user->meta['last_login'])}
                {datetime date=$user->meta['last_login']}
            {else}
                <span class="icon-remove"></span>
            {/if}
        </td>
        <td class="center">
            {if isset($user->meta['paywall_time_limit'])}
                {datetime date=$user->meta['paywall_time_limit']}
            {else}
                <span class="icon-remove"></span>
            {/if}
        </td>
        <td class="center">
            {if isset($user->meta['paywall_time_limit'])}
                {capture name=time}{datetime date=$user->meta['paywall_time_limit']}{/capture}
                {if $smarty.capture.time > $smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}
                    <span style="color:green">{t}Paid{/t}</span>
                {else}
                    <span style="color:red">{t}Expired{/t}</span>
                {/if}
            {else}
                <span style="color:#0000A0">{t}Registered{/t}</span>
            {/if}
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