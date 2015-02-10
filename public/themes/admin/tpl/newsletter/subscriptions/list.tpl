{extends file="base/admin.tpl"}
{block name="footer-js"}
{include file="newsletter/subscriptions/modals/_modalDelete.tpl"}
{include file="newsletter/subscriptions/modals/_modalBatchDelete.tpl"}
{include file="newsletter/subscriptions/modals/_modalBatchSubscribe.tpl"}
{include file="newsletter/subscriptions/modals/_modalAccept.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_newsletter_subscriptors}" name="formulario" id="formulario">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Newsletters{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{t}Subscriptions{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" href="{url name=admin_newsletters}" title="{t}Go back to newsletter manager{/t}">
                            <span class="fa fa-reply"></span>
                            {t}Newsletters{/t}
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                        <a href="{url name=admin_newsletter_subscriptor_create}" class="btn btn-primary" accesskey="N">
                            <span class="fa fa-plus"></span>
                            {t}Create{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<!-- <div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section pull-left">
                <li class="quicklinks">
                  <button class="btn btn-link" ng-click="selected.contents = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
                    <i class="fa fa-check fa-lg"></i>
                  </button>
                </li>
                 <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <h4>
                        [% selected.contents.length %] {t}items selected{/t}
                    </h4>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <button class="btn btn-link batchDeleteButton" accesskey="d">
                        <span class="fa fa-trash-o"></span>
                        {t}Delete{/t}
                    </button>
                </li>
                <li class="quicklinks">
                    <button data-subscribe="0" class="btn btn-link batchSubscribeButton">
                        {t}Unsubscribe{/t}
                    </button>
                </li>

                <li class="quicklinks">
                    <button data-subscribe="1" class="btn btn-link batchSubscribeButton">
                        {t}Subscribe{/t}
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div> -->

<div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="m-r-10 input-prepend inside search-input no-boarder">
                    <span class="add-on">
                        <span class="fa fa-search fa-lg"></span>
                    </span>
                    <input class="no-boarder" name="filters[text]" id="filters_text" value="{$smarty.request.filters.text}" placeholder="{t}Search by name{/t}" type="search"/>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <select name="filters[subscription]" id="filters_subscription">
                        <option value="-1">{t}All{/t}</option>
                        <option value="1"{if $smarty.request.filters.subscription==1} selected="selected"{/if}>{t}Subscribed{/t}</option>
                        <option value="0"{if isset($smarty.request.filters.subscription) && $smarty.request.filters.subscription==0} selected="selected"{/if}>{t}No subscribed{/t}</option>
                    </select>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <button type="submit" class="btn btn-search">{t}Filter{/t}</button>
                </li>
                <li class="quicklinks">
                    <span class="info">
                        {t}Results{/t}: {$pager->_totalItems}
                    </span>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks form-inline pagination-links">
                    <div class="btn-group">
                        <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </li>
                <input type="hidden" name="page" id="filters_page" value="{$smarty.request.page|default:'1'}" />
            </ul>
        </div>
    </div>
</div>

<div class="content">

    {render_messages}

    <div class="grid simple">
        <div class="grid-body no-padding">

            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th style="width:10px"><input type="checkbox" class="toggleallcheckbox" style="cursor:pointer;" /></th>
                        <th>{t}Name{/t}</th>
                        <th>{t}Email{/t}</th>
                        <th class="left">{t}Status{/t}</th>
                        <th class="center" style="width:10px">{t}Activated{/t}</th>
                        <th class="center" style="width:10px">{t}Subscribed{/t}</th>
                        <th class="center nowrap" style="width:10px">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach name=c from=$users item=user}
                     <tr>
                        <td class="center">
                            <input type="checkbox" class="minput" name="cid[]" value="{$user->id}" style="cursor:pointer;" />
                        </td>
                        <td class="left">
                            {$user->firstname}&nbsp;{$user->lastname} {$user->name}
                        </td>
                        <td class="left">
                            {$user->email}
                        </td>
                        <td class="left">
                            {if      $user->status eq 0} {t}Mail sent.Waiting for user{/t}
                            {elseif  $user->status eq 1} {t}Accepted by user{/t}
                            {elseif  $user->status eq 2} {t}Accepted by administrator{/t}
                            {elseif  $user->status eq 3} {t}Disabled by administrator{/t}
                            {/if}
                        </td>
                        <td class="center">
                            {if $user->status eq 0 || $user->status eq 3}
                                <a href="{url name=admin_newsletter_subscriptor_toggle_activated id=$user->id}" class="newsletterFlag">
                                <img src="{$params.IMAGE_DIR}publish_r.png" title="Habilitar" /></a>
                            {else}
                                <a href="{url name=admin_newsletter_subscriptor_toggle_activated id=$user->id}" class="newsletterFlag">
                                <img src="{$params.IMAGE_DIR}publish_g.png" title="Deshabilitar" /></a>
                            {/if}
                        </td>
                        <td class="center">
                            <a href="{url name=admin_newsletter_subscriptor_toggle_subscription id=$user->id}" class="newsletterFlag">
                            {if $user->subscription eq 0}
                                <img src="{$params.IMAGE_DIR}subscription_0-16x16.png" title="Suscribir" />
                            {else}
                                <img src="{$params.IMAGE_DIR}subscription_1-16x16.png" title="Anular suscripción" />
                            {/if}
                            </a>
                        </td>
                        <td class="right nowrap">
                            <div class="btn-group">
                                <a href="{url name=admin_newsletter_subscriptor_show id=$user->id}" title="{t}Edit user{/t}" class="btn">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a  href ="{url name=admin_newsletter_subscriptor_delete id=$user->id}"
                                    class="del btn btn-danger"
                                    data-title="{$user->email}"
                                    data-url="{url name=admin_newsletter_subscriptor_delete id=$user->id}"
                                    title="{t}Delete user{/t}">
                                    <i class="icon-white icon-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    {foreachelse}
                    <tr>
                        <td class="empty" colspan="7">{t}There is no subscriptors yet{/t}</td>
                    </tr>
                    {/foreach}
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="7" class="center">
                            <div class="pagination">
                                {$pager->links|default:""}
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>


    </div>
</form>
{/block}
