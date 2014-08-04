<html>
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
        {block name="header-css"}
            {css_tag href="/bootstrap/bootstrap.css" common=1}
            {css_tag href="/fontawesome/font-awesome.min.css" common=1}
            {css_tag href="/style.css" common=1}
        {/block}
        <style>
            html, body {
                margin:0 auto;
                padding:0;
                min-height:0;
                overflow-y:hidden;
            }
        </style>
    </head>
    <body>
        <div class="social-connections">
            {if $connected}
                <p>
                    {if $current_user_id == $user->id}
                        {t 1=$resource_name}Your account is connected to %1.{/t}
                        <a href="{url name=admin_acl_user_social_disconnect id=$user->id resource=$resource}" title="{t}Disconnect from Facebook{/t}" class="disconnect">{t}Disconnect{/t}</a>
                    {/if}
                </p>
                <ul class="social-connection">
                    <li>
                        {if $user->photo->name}
                        <div style="width: 40px; height: 40px;">
                            <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$user->photo->path_file}/{$user->photo->name}" alt="{t}Photo{/t}"/>
                        </div>
                        {else}
                        <div style="width: 40px; height: 40px;" >
                            {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="40"}
                        </div>
                        {/if}
                    </li>
                    <li class="arrow"><i class="icon-arrow-right"></i></li>
                    <li>
                        <div class="btn btn-social btn-{$resource}">
                            <i class="icon-{$resource}"></i>
                            {assign var="meta" value="{$resource}_realname"}
                            {$user->meta[$meta]}
                        </div>
                    </li>
                </ul>
                <p>{t 1=$resource_name}Allows you to login into Opennemas with %1{/t}.</p>
            {else}
                {if $current_user_id == $user->id}
                    <button class="social-network-connect btn btn-social btn-{$resource}" data-url="{hwi_oauth_login_url name={$resource}}" type="button">
                        <i class="icon-{$resource}"></i> {t}Connect with {if $resource == 'facebook'}Facebook{else}Twitter{/if}{/t}
                    </button>
                    <div class="help-block">{t}Associate your {if $resource == 'facebook'}Facebook{else}Twitter{/if} account to login into Opennemas with it.{/t}</div>
                {else}
                    <p>Only the user can connect their social accounts with Opennemas.</p>
                {/if}
            {/if}
        </div>
    </body>
    {block name="footer-js"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/admin.js" common=1}
    {/block}
</html>
