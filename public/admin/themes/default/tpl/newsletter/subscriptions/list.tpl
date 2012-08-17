{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/admin.css"}
{/block}

{block name="footer-js" append}
    {script_tag src="/switcher_flag.js"}
    <script type="text/javascript">
    /* <![CDATA[ */
    /* Utils filter functions */
    function paginate(page) {
        $('filters_page').value = page;
        $('formulario').submit();
    }

    function filterList() {
        $('filters_page').value = '1';

        $('action').value='list';
        $('formulario').submit();
    }

    /* Init list actions */
    $('gridUsers').select('a.newsletterFlag').each(function(item){
        new SwitcherFlag(item);
    });

    document.observe('dom:loaded', function() {

        $$('a.subscribe').each(function(lnk) {
            lnk.observe('click', function() {
                var frm = $('formulario');
                $('action').value = 'msubscribe';
                frm.submit();
            });
        });

        $$('a.unsubscribe').each(function(lnk) {
            lnk.observe('click', function() {
                var frm = $('formulario');
                $('action').value = 'munsubscribe';
                frm.submit();
            });
        });

        $$('a.mdelete').each(function(lnk) {
            lnk.observe('click', function() {
                var frm = $('formulario');
                $('action').value = 'mdelete';
                frm.submit();
            });
        });
    });
    /* ]]> */
    </script>
{/block}

{block name="content"}
<form action="{url name=admin_newsletter_subscriptors}" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Newsletter Subscriptions{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add mdelete" id="submit_mult" title="Eliminar">
                        <img src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />
                        {t}Delete{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add unsubscribe" accesskey="U">
                        <img class="icon" src="{$params.IMAGE_DIR}subscription_0.png"
                             title="Desuscribir seleccionados" alt="Desuscribir seleccionados" height="50" /><br />
                        {t}Unsubscribe{/t}
                    </a>
                </li>

                <li>
                    <a href="#" class="admin_add subscribe" accesskey="S">
                        <img class="icon" src="{$params.IMAGE_DIR}subscription_1.png"
                             title="Suscribir seleccionados" alt="Suscribir seleccionados" height="50" /><br />
                        {t}Subscribe{/t}
                    </a>
                </li>

                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_newsletter_subscription_create}" class="admin_add" accesskey="N">
                        <img src="{$params.IMAGE_DIR}authors_add.png" title="Nuevo Usuario" alt="Nuevo Usuario"><br />
                        {t}New{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_newsletters}" title="Cancelar">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />
                        {t}Newsletter{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <table class="adminheading">
            <tr>
                <th></th>
                <th class="right">
                    <input type="search" name="filters[text]" id="filters_text" value="{$smarty.request.filters.text}" placeholder="{t}Search by name{/t}"/>

                    <select name="filters[subscription]" id="filters_subscription">
                        <option value="-1">{t}All{/t}</option>
                        <option value="1"{if $smarty.request.filters.subscription==1} selected="selected"{/if}>{t}Subscribed{/t}</option>
                        <option value="0"{if isset($smarty.request.filters.subscription) && $smarty.request.filters.subscription==0} selected="selected"{/if}>{t}No subscribed{/t}</option>
                    </select>

                    <input type="hidden" name="page" id="filters_page" value="{$smarty.request.page|default:'1'}" />

                    <button type="submit" class="btn btn-search">{t}Filter{/t}</button>
                </th>
            </tr>
        </table>

        <table class="listing-table">
            <thead>
                <tr>
                    <th style="width:10px"><input type="checkbox" class="minput" id="toggleallcheckbox" name="cid[]" value="" style="cursor:pointer;" /></th>
                    <th>{t}Name{/t}</th>
                    <th>{t}Email{/t}</th>
                    <th class="center">{t}Status{/t}</th>
                    <th class="center" style="width:50px">{t}Activated{/t}</th>
                    <th class="center" style="width:10px">{t}Subscribed{/t}</th>
                    <th class="center" style="width:100px">{t}Actions{/t}</th>
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
                    <td class="center">
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
                    <td class="center">
                        <div class="btn-group">
                            <a href="{url name=admin_newsletter_subscriptor_show id=$user->id}" title="{t}Edit user{/t}" class="btn">
                                <i class="icon-pencil"></i>
                            </a>
                            <a href ="{url name=admin_newsletter_subscriptor_delete id=$user->id}" class="btn btn-danger" title="{t}Delete user{/t}">
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
                    <td colspan="7">
                        {$paginacion->links|default:""}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</form>
{/block}
