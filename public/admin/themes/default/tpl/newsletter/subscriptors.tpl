{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/admin.css"}
{/block}

{block name="footer-js" append}
    {script_tag src="/switcher_flag.js"}
    <script type="text/javascript">
    /* <![CDATA[ */
    {literal}
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

    function resetForm() {
        $('filters_text').value = '';
        $('filters_status').value = '-1';
        $('filters_subscription').value = '-1';

        $('filters_page').value = '1';
        $('action').value='list';

        $('formulario').submit();
    }

    /* Init list actions */
    $('gridUsers').select('a.newsletterFlag').each(function(item){
        new SwitcherFlag(item);
    });

    document.observe('dom:loaded', function() {
        $$('imput.checkall').each(function(lnk) {
            lnk.observe('click', function() {
                var items = $('gridUsers').select('input[type=checkbox][name^=cid]');
                var status = !!items[0].checked;
                items.each(function(item){
                    if(!status) {
                        item.setAttribute('checked', 'checked');
                        item.checked = true;
                    } else {
                        item.removeAttribute('checked');
                        item.checked = false;
                    }
                });

                if(!status) {
                    this.select('span')[0].update('Deseleccionar');
                } else {
                    this.select('span')[0].update('Seleccionar todo');
                }
            });
        });

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

        // Setup this action by default
        $('action').value = 'list';
    });
    {/literal}
    /* ]]> */
    </script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{$titulo_barra}&nbsp; </h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add mdelete" id="submit_mult" title="Eliminar">
                        <img src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />Eliminar
                    </a>
                </li>

                <li class="separator"></li>

                <li>
                    <a href="#" class="admin_add unsubscribe" accesskey="U">
                        <img class="icon" src="{$params.IMAGE_DIR}subscription_0.png"
                             title="Desuscribir seleccionados" alt="Desuscribir seleccionados" height="50" /><br />
                        Desubscribir
                    </a>
                </li>

                <li>
                    <a href="#" class="admin_add subscribe" accesskey="S">
                        <img class="icon" src="{$params.IMAGE_DIR}subscription_1.png"
                             title="Suscribir seleccionados" alt="Suscribir seleccionados" height="50" /><br />
                        Subscribir
                    </a>
                </li>

                <li class="separator"></li>
                <li>
                    <a href="#" class="admin_add" onclick="enviar(this, '_self', 'new', 0);" accesskey="N">
                        <img src="{$params.IMAGE_DIR}authors_add.png" title="Nuevo Usuario" alt="Nuevo Usuario"><br />
                            Nuevo Usuario
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="newsletter.php" title="Cancelar">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Newsletter{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        <table class="adminheading">
            <tr>
                <th class="right">
                    <label>
                        {t}Search:{/t} <input type="text" name="filters[text]" id="filters_text" value="{$smarty.request.filters.text}" />
                    </label>

                    <select name="filters[subscription]" id="filters_subscription">
                        <option value="-1">{t}Subscribed to newsletter?{/t}</option>
                        <option value="1"{if $smarty.request.filters.subscription==1} selected="selected"{/if}>{t}Yes{/t}</option>
                        <option value="0"{if isset($smarty.request.filters.subscription) && $smarty.request.filters.subscription==0} selected="selected"{/if}>{t}No{/t}</option>
                    </select>

                    <input type="hidden" name="page" id="filters_page" value="{$smarty.request.page|default:'1'}" />

                    <button type="button" onclick="filterList();">{t}Filter list{/t}</button>
                    <button type="button" onclick="resetForm();">Clean</button>
                </th>
            </tr>
        </table>

        <table class="listing-table">
            <thead>
                <tr>
                    <th><input type="checkbox" class="minput" id="checkall" name="cid[]" value="" style="cursor:pointer;" /></th>
                    <th>{t}Name{/t}</th>
                    <th>{t}Email{/t}</th>
                    <th class="center">{t}Subscription{/t}</th>
                    <th class="center">{t}Status{/t}</th>
                    <th class="center" >{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody id="gridUsers">
                {section name=c loop=$users}
                 <tr {cycle values="class=row0,class=row1"}>
                    <td class="center">
                        <input type="checkbox" class="minput" name="cid[]" value="{$users[c]->id}" style="cursor:pointer;" />
                    </td>
                    <td style="padding:10px;">
                        {$users[c]->firstname}&nbsp;{$users[c]->lastname} {$users[c]->name}
                    </td>
                    <td class="center">
                        {$users[c]->email}
                    </td>
                    <td class="center">
                        <a href="?action=subscribe&amp;id={$users[c]->id}" class="newsletterFlag">
                        {if $users[c]->subscription eq 0}
                            <img src="{$params.IMAGE_DIR}subscription_0-16x16.png" title="Suscribir" />
                        {else}
                            <img src="{$params.IMAGE_DIR}subscription_1-16x16.png" title="Anular suscripción" />
                        {/if}
                        </a>
                    </td>
                    <td class="center">                            
                        {if      $users[c]->status eq 0} {t}Mail sent.Waiting for user{/t}
                        {elseif  $users[c]->status eq 1} {t}Accepted by user{/t}
                        {elseif  $users[c]->status eq 2} {t}Accepted by administrator{/t}
                        {elseif  $users[c]->status eq 3} {t}Disabled by administrator{/t}
                        {/if}
                    </td>
                    <td class="center">
                        <ul class="action-buttons">
                            <li>
                                {if $users[c]->status eq 0 || $users[c]->status eq 3}
                                    <a href="?id={$users[c]->id}&amp;action=change_status" class="newsletterFlag">
                                    <img src="{$params.IMAGE_DIR}publish_r.png" title="Habilitar" /></a>
                                {else}
                                    <a href="?id={$users[c]->id}&amp;action=change_status" class="newsletterFlag">
                                    <img src="{$params.IMAGE_DIR}publish_g.png" title="Deshabilitar" /></a>
                                 {/if}
                            </li>
                            <li>
                                <a href="{$smarty.server.PHP_SELF}?action=read&amp;id={$users[c]->id}&amp;page={$page|default:0}" title="{t}Edit user{/t}">
                                    <img src="{$params.IMAGE_DIR}edit.png" /></a>
                            </li>
                            <li>
                                <a href="#" onClick="javascript:confirmar(this, {$users[c]->id});" title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" /></a>
                            </li>
                        </ul>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td class="empty" colspan="6">{t}There is no subscriptors yet{/t}</td>
                </tr>
                {/section}
            </tbody>

            <tfoot>
            <tr>
                <td colspan="6">
                    {$paginacion->links|default:""}&nbsp;
                </td>
            </tr>
            </tfoot>
        </table>
                
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}
