{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_paywall_settings_save}" method="post">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Paywall{/t} :: {t}Premium users{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{url name=admin_paywall_settings}" class="admin_add" title="{t}Config newsletter module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Settings{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_paywall}" title="{t}Go back to list{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content clearfix">

        {render_messages}


        {include file="paywall/partials/user_listing.tpl" show_edit_button=true}

    </div>
</form>
{/block}
