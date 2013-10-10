{t}User name{/t},{t}Full name{/t},{t}E-mail{/t},{t}Last login{/t},{t}End of subscription{/t},{t}Status{/t}
{foreach from=$users item=user}
{if isset($user->meta['paywall_time_limit'])}
    {capture name=time}{datetime date=$user->meta['paywall_time_limit']}{/capture}
    {if $smarty.capture.time > $smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}
        {assign var=state value="{t}Paid{/t}"}
    {else}
        {assign var=state value="{t}Expired{/t}"}
    {/if}
{else}
    {assign var=state value="{t}Registered{/t}"}
{/if}
"{$user->username}","{$user->name}","{$user->email}","{datetime date=$user->meta['last_login']}","{datetime date=$user->meta['paywall_time_limit']}","{$state}"
{/foreach}