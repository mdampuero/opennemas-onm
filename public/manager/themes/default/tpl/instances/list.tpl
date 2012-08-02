{extends file="base/base.tpl"}

{block name="footer-js" append}
{script_tag language="javascript" src="/switcher_flag.js"}
<script type="text/javascript" language="javascript">
/* <![CDATA[ */

document.observe('dom:loaded', function() {
    $('pagina').select('a.switchable').each(function(item){
        new SwitcherFlag(item);
    });
});

</script>


<script language="javascript">
// <![CDATA[
function enviar(frm, trg, acc, id) {
    frm.target = trg;

    $('action').value = acc;
    $('id').value = id;

    frm.submit();
}

function confirmarDelete(action) {
    var confirm1 = confirm('¿Está seguro de eliminar esta instancia completa?');
    if (confirm1) {
        var confirm2 = confirm('¡Se perderá toda la información almacenada así como toda la base de datos!');
        if (confirm2) {
            window.location = action.href;
        }
    }
}
    // ]]>
</script>

{/block}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Instance Manager{/t}</h2>
        </div>
        <ul class="old-button">
            <li>
                <a href="{url name=manager_instance_create}" class="admin_add"
                   title="{t}New widget{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="" alt="" />
                    <br />{t}New{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    {render_messages}
</div>
<div class="wrapper-content">
<form action="{url name=manager_instances}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>
    <table class="adminheading">
        <tr>
            <th align="left">Total: {$pagination->_totalItems} instances.</th>
            <th class="right form-inline">
                <label for="username">
                    {t}Filter by name{/t}</label>
                    <input id="username" class="" name="filter_name" onchange="this.form.submit();" value="{$smarty.request.filter_title}" />
                </label>
                <label for="usergroup">
                    {t}Per page{/t}
                    <select id="usergroup" name="filter_per_page" onchange="this.form.submit();">
                         <option value="10" {if $per_page eq 10}selected{/if}>10</option>
                         <option value="20" {if $per_page eq 20}selected{/if}>20</option>
                         <option value="50" {if $per_page eq 50}selected{/if}>50</option>
                         <option value="100" {if $per_page eq 100}selected{/if}>100</option>
                    </select>
                </label>
                <input type="hidden" name="page" value="1" />
                <button type="submit" class="btn">{t}Search{/t}</button>
            </th>
        </tr>
    </table>

    <table class="listing-table" >

        <thead>
            {if count($instances) > 0}
            <th width="15px">{t}#{/t}</th>
            <th width="200px">{t}Name{/t}</th>
            <th>{t}Domains{/t}</th>
            <th>{t}Contact{/t}</th>
            <th>{t}Articles{/t}</th>
            <th>{t}Images{/t}</th>
            <th>{t}Ads{/t}</th>
            <th width="100px">{t}Created{/t}</th>
            <th class="center" width="50px">{t}Activated{/t}</th>
            <th class="center" width="110px">{t}Actions{/t}</th>
            {else}
            <th scope="col" colspan=4>&nbsp;</th>
            {/if}
        </thead>

        <tbody>
            {foreach from=$instances item=instance name=instance_list}
            <tr>
                <td>
                    {$instance->id}
                </td>
                <td>
                    <a href="instances.php?action=edit&id={$instance->id}" title="{t}Edit{/t}">
                        {$instance->name}
                    </a>

                </td>
                <td>
                    {foreach from=$instance->domains item=domain name=instance_domains}
                        <a href="http://{$domain}" target="_blank" title="{$instance->name}">{$domain}</a><br/>
                    {/foreach}
                </td>
                <td>
                    {$instance->configs['contact_mail']}
                </td>
                <td>
                    {$instance->totals[1]}
                </td>
                <td>
                    {$instance->totals[8]}
                </td>
                <td>
                    {$instance->totals[2]}
                </td>
                 <td>
                    {$instance->configs['site_created']}
                </td>
                <td class="center">
                    {if $instance->activated == 1}
                    <a href="{url name=manager_instance_toggleavailable id=$instance->id}" title="{t}Published{/t}">
                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                    {else}
                    <a href="{url name=manager_instance_toggleavailable id=$instance->id}" title="{t}Unpublished{/t}">
                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Unpublished{/t}" /></a>
                    {/if}
                </td>

                <td class="right">
                    <div class="btn-group">
                        <a class="btn" href="{url name=manager_instance_show id=$instance->id}" title="{t}Edit{/t}">
                            <i class="icon-pencil"></i>
                        </a>
                        <a class="btn btn-danger" href="{url name=manger_instance_delete id=$instance->id}" title="{t}Delete{/t}">
                            <i class="icon-trash icon-white"></i>
                        </a>
                    </div>
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td class="empty" colspan="11">{t}There is no available instances yet{/t}</td>
            </tr>
            {/foreach}
        </tbody>

        <tfoot>
            <tr class="pagination">
                <td colspan="11">
                    {$pagination->links}&nbsp;
                </td>
            </tr>
        </tfoot>
    </table>
    <input type="hidden" id="action" name="action" value="list" />
</form>
</div>
{/block}
