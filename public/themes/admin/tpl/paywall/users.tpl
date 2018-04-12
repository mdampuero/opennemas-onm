{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_paywall_users}" method="get">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-paypal"></i>
                        {t}Paywall{/t}
                    </h4>
                </li>
                <li class="quicklinks hidden-xs">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks hidden-xs">
                    <h5>{t}Premium users{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" href="{url name=admin_paywall}" title="{t}Go back to list{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <a class="btn btn-primary" href="{url name=backend_user_create}" title="{t}Create new user{/t}">
                            {t}Create{/t}
                        </a>
                    </li>
            </div>
        </div>
    </div>
</div>

<div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="m-r-10 input-prepend inside search-input no-boarder">
                    <span class="add-on">
                        <span class="fa fa-search fa-lg"></span>
                    </span>
                    <input class="no-boarder" type="text" name="title" value="{$smarty.request.searchname|default:""}" placeholder="{t}Filter by name{/t}" />
                </li>
                <li class="quicklinks hidden-xs">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks hidden-xs">
                    <div class="input-append">
                        <select id="order" name="order" class="span2">
                            {assign var=order value=$smarty.request.order}
                            <option value="username" {if ($order eq "") || ($order eq "username")}selected{/if}>{t}Name{/t}</option>
                            <option value="email" {if ($order eq "email")}selected{/if}>{t}E-mail{/t}</option>
                            <option value="last_login" {if ($order eq "last_login")}selected{/if}>{t}Last login{/t}</option>
                            <option value="paywall" {if ($order eq "paywall")}selected{/if}>{t}End of subscription{/t}</option>
                        </select>
                    </div>
                </li>
                <li class="quicklinks hidden-xs">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks hidden-xs">
                    <select id="usertype" name="type">
                        {assign var=type value=$smarty.request.type}
                        <option value="" {if ($type eq "")}selected{/if}>{t}--All--{/t}</option>
                        <option value="0" {if ($type eq "0")}selected{/if}>{t}Paid{/t}</option>
                        <option value="1" {if ($type eq "1")}selected{/if}>{t}Registered{/t}</option>
                        <option value="2" {if ($type eq "2")}selected{/if}>{t}Expired{/t}</option>
                    </select>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks hidden-xs">
                    <span class="info">{t 1=$pagination->_totalItems}%1 users{/t}</span>
                </li>
                <li class="quicklinks form-inline pagination-links">
                    <a class="btn btn-primary" href="{url name=admin_paywall_users_list_export type=$smarty.request.type order=$smarty.request.order searchname=$smarty.request.searchname}">
                    {t}Export list{/t}
                </a>
            </ul>
        </div>
    </div>
</div>
<div class="content paywall">
    <div class="grid simple">
        <div class="grid-body no-padding">
            {include file="paywall/partials/user_listing.tpl" show_edit_button=true}
        </div>
    </div>
</form>
{/block}
