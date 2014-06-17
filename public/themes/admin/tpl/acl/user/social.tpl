<html>
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
        {block name="header-css"}
            {css_tag href="/bootstrap/bootstrap.css" common=1}
            {css_tag href="/fontawesome/font-awesome.min.css" common=1}
            {css_tag href="/style.css" common=1}
        {/block}
    </head>
    <body>
        <div class="social-connections">
            {if $current_user_id == $user->id}
                {if $connected}
                    <p>
                        {t}Your account is connected to {if $resource == 'facebook'}Facebook{else}Twitter{/if}.{/t}
                        <a href="#" title="{t}Disconnect from Facebook{/t}" class="disconnect">{t}Disconnect{/t}</a>
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
                    <p>Allows you to login into Opennemas with {if $resource == 'facebook'}Facebook{else}Twitter{/if}.</p>
                {else}
                    <button class="social-network-connect btn btn-social btn-{$resource}" data-url="{hwi_oauth_login_url name={$resource}}" type="button">
                        <i class="icon-{$resource}"></i> {t}Connect with {if $resource == 'facebook'}Facebook{else}Twitter{/if}{/t}
                    </button>
                    <div class="help-block">{t}Associate your {if $resource == 'facebook'}Facebook{else}Twitter{/if} account to login into Opennemas with it.{/t}</div>
                {/if}
            {else}
                <p>Only the user can connect their social accounts with Opennemas.</p>
            {/if}
        </div>
    </body>
    {block name="footer-js"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/admin.js" common=1}
    {/block}
</html>
