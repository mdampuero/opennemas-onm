{if count($users) > 0}
<table class="table table-condensed">
    {if count($users) > 0}
    <thead>
        <tr>
            <th class="left">{t}User name{/t}</th>
            <th class="left">{t}E-mail{/t}</th>
            <th class="center">{t}Last login{/t}</th>
            <th class="center">{t}End of subscription{/t}</th>
            {if $show_edit_button}
            <th class="center">{t}Status{/t}</th>
            <th style="width:10px">{t}Edit{/t}</th>
            {/if}

        </tr>
    </thead>
    {/if}
    <tbody class="sortable">
    {foreach from=$users item=user}
    <tr data-id="{$user->id}">
        <td class="left">
            <a href="{url name=backend_user_show id=$user->id}#paywall">{$user->username|clearslash}</a>
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
                <span class="fa fa-times"></span>
            {/if}
        </td>
        <td class="center">
            {if isset($user->meta['paywall_time_limit'])}
                {datetime date=$user->meta['paywall_time_limit']}
            {else}
                <span class="fa fa-times"></span>
            {/if}
        </td>
        {if $show_edit_button}
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
        <td class="right">
            <a href="{url name=backend_user_show id=$user->id}#paywall" class="btn btn-white" title="{t}Edit{/t}"><i class="fa fa-pencil"></i></a>
        </td>
        {/if}

    </tr>
    {/foreach}
    </tbody>
    {if count($users) > 0}
    <tfoot>
        <tr>
            <td colspan="11" class="center" style="padding:5px">
                <div class="pagination">
                    {$pagination->links}
                </div>
            </td>
        </tr>
    </tfoot>
    {/if}
</table>
<a href="{url name=admin_paywall_users}" class="btn btn-white">{t}Show all…{/t}</a>
{else}
<div class="listing-no-contents">
  <div class="center">
    <h4>{t}No paywall users yet.{/t}</h4>
  </div>
</div>
{/if}
