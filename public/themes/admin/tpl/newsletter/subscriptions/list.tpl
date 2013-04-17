{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_newsletter_subscriptors}" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Newsletter{/t} :: {t}Subscriptions{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{url name=admin_newsletter_subscriptor_create}" class="admin_add" accesskey="N">
                        <img src="{$params.IMAGE_DIR}authors_add.png" title="Nuevo Usuario" alt="Nuevo Usuario"><br />
                        {t}New{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <button class="admin_add batchDeleteButton" accesskey="d">
                        <img src="{$params.IMAGE_DIR}trash.png" alt="{t}Delete{/t}"><br />
                        {t}Delete{/t}
                    </button>
                </li>
                <li>
                    <button data-subscribe="0" class="batchSubscribeButton">
                        <img class="icon" src="{$params.IMAGE_DIR}subscription_0.png"
                             title="Desuscribir seleccionados" alt="Desuscribir seleccionados" height="50" /><br />
                        {t}Unsubscribe{/t}
                    </button>
                </li>

                <li>
                    <button data-subscribe="1" class="batchSubscribeButton">
                        <img class="icon" src="{$params.IMAGE_DIR}subscription_1.png"
                             title="Suscribir seleccionados" alt="Suscribir seleccionados" height="50" /><br />
                        {t}Subscribe{/t}
                    </button>
                </li>

                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_newsletters}" title="Cancelar">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />
                        {t}Newsletters{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            <div class="pull-left total">
                {$pager->_totalItems} {t}Subscriptors{/t}
            </div>
            <div class="pull-right form-inline">
                <input type="search" name="filters[text]" id="filters_text" value="{$smarty.request.filters.text}" placeholder="{t}Search by name{/t}"/>

                <select name="filters[subscription]" id="filters_subscription">
                    <option value="-1">{t}All{/t}</option>
                    <option value="1"{if $smarty.request.filters.subscription==1} selected="selected"{/if}>{t}Subscribed{/t}</option>
                    <option value="0"{if isset($smarty.request.filters.subscription) && $smarty.request.filters.subscription==0} selected="selected"{/if}>{t}No subscribed{/t}</option>
                </select>

                <input type="hidden" name="page" id="filters_page" value="{$smarty.request.page|default:'1'}" />

                <button type="submit" class="btn btn-search">{t}Filter{/t}</button>
            </div>
        </div>

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
                                <i class="icon-pencil"></i>
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
    <input type="hidden" id="subscribe" name="subscribe" value="">
</form>

{include file="newsletter/subscriptions/modals/_modalDelete.tpl"}
{include file="newsletter/subscriptions/modals/_modalBatchDelete.tpl"}
{include file="newsletter/subscriptions/modals/_modalBatchSubscribe.tpl"}
{include file="newsletter/subscriptions/modals/_modalAccept.tpl"}
{/block}
