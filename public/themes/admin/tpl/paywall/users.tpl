{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_paywall_users}" method="get">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Paywall{/t} :: {t}Premium users{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{url name=admin_acl_user_create}" title="{t}Create new user{/t}">
                        <img src="{$params.IMAGE_DIR}user_add.png" alt="Nuevo"><br />{t}New user{/t}
                    </a>
                </li>
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
        <!-- Filter bar -->
        <div class="table-info clearfix">
            <div class="pull-right form-inline">
                <label for="usergroup">{t}Order by{/t}</label>
                <div class="input-append">
                    <select id="order" name="order" class="span2">
                        {assign var=order value=$smarty.request.order}
                        <option value="name" {if ($order eq "") || ($order eq "name")}selected{/if}>{t}Name{/t}</option>
                        <option value="email" {if ($order eq "email")}selected{/if}>{t}E-mail{/t}</option>
                        <option value="last_login" {if ($order eq "last_login")}selected{/if}>{t}Last login{/t}</option>
                        <option value="paywall" {if ($order eq "paywall")}selected{/if}>{t}End of subscription{/t}</option>
                    </select>
                </div>
                <label for="usergroup">{t}type{/t}</label>
                <div class="input-append">
                    <select id="usertype" name="type" class="span2">
                        {assign var=type value=$smarty.request.type}
                        <option value="" {if ($type eq "")}selected{/if}>{t}--All--{/t}</option>
                        <option value="0" {if ($type eq "0")}selected{/if}>{t}Premium{/t}</option>
                        <option value="1" {if ($type eq "1")}selected{/if}>{t}Registered{/t}</option>
                    </select>
                    <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
            </div>
        </div>
        {include file="paywall/partials/user_listing.tpl" show_edit_button=true}

    </div>
</form>
{/block}
