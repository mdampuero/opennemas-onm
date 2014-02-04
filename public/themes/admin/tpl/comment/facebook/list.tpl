{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
.facebook {
    padding: 20px;
    background-color: #ededed;
    border-radius: 5px;
}
.facebook .lead {
    font-size: 14px;
}
</style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2 class="facebook">{t}Comments{/t}</h2>
        </div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_comments_facebook_config}" title="{t}facebook module configuration{/t}">
                    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config facebook module{/t}" alt="{t}Config facebook module{/t}" ><br />{t}Config{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    <div class="facebook">
        <h1>{t}You are now using Facebook comments{/t}</h1>
        {if empty($fb_app_id)}
        <p class="lead">{t escape=off}If you want to moderate comments, you first need to create a Facebook application in <a href="https://developers.facebook.com/" target="_blank">here</a> to get an application Id and then click on settings to configure it{/t}.</p>
        {else}
        <p class="lead">{t escape=off}To moderate your comments go to <a href="https://developers.facebook.com/tools/comments" target="_blank">Facebook moderation tool page</a>{/t}.</p>
        {/if}
    </div>
</div>
{/block}

{block name="copyright"}
{/block}
