{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_paywall_purchases}" method="get">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Paywall{/t} :: {t}Purchases listing{/t}</h2></div>
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
        <!-- Filter bar -->
        <div class="table-info clearfix">
            <div class="pull-left">
                {$pagination->_totalItems} {t}purchases{/t}
                <a href="{url name=admin_paywall_purchases_list_export order=$smarty.request.order searchname=$smarty.request.searchname}">
                    {image_tag src="{$params.COMMON_ASSET_DIR}images/csv.png" base_url=""}
                    {t}Export list{/t}
                </a>
            </div>
            <div class="pull-right form-inline">
                <input type="text" id="username" name="searchname" value="{$smarty.request.searchname|default:""}" placeholder="{t}Filter by name or email{/t}" />
                <label for="usergroup">{t}Order by{/t}</label>
                <div class="input-append">
                    <select id="order" name="order" class="span2">
                        {assign var=order value=$smarty.request.order}
                        <option value="" {if $order eq ""}selected{/if}>{t}Payment date{/t}</option>
                        <option value="username" {if ($order eq "username")}selected{/if}>{t}User name{/t}</option>
                        <option value="name" {if ($order eq "name")}selected{/if}>{t}Full name{/t}</option>
                    </select>
                    <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
            </div>
        </div>
        {include file="paywall/partials/purchases_listing.tpl"}

    </div>
</form>
{/block}
