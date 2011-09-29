{extends file="base/base.tpl"}

{block name="footer-js" append}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype-date-extensions.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js?load=effects,dragdrop"></script>

{* Ajax button to change availability *}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}switcher_flag.js"></script>
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

function confirmar() {
    if (confirm('¿Está seguro de querer eliminar este elemento?')) {
        window.location = this.href;
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
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add"
                   title="{t}New widget{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="" alt="" />
                    <br />{t}New{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    <div id="pagina">
        <table class="listing-table" >

            <thead>
                {if count($instances) > 0}
                <th width="15px">{t}#{/t}</th>
                <th width="200px">{t}Name{/t}</th>
                <th>{t}Domains{/t}</th>
                <th class="center" width="50px">{t}Activated{/t}</th>
                <th class="center" width="50px">{t}Actions{/t}</th>
                {else}
                <th scope="col" colspan=4>&nbsp;</th>
                {/if}
            </thead>

            <tbody>
                {foreach from=$instances item=instance   name=instance_list}
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
                        {$instance->domains}
                    </td>

                    <td class="center">
                        {if $instance->activated == 1}
                        <a href="?id={$instance->id}&action=changeactivated" class="switchable" title="{t}Published{/t}">
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                        {else}
                        <a href="?id={$instance->id}&action=changeactivated" class="switchable" title="{t}Unpublished{/t}">
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Unpublished{/t}" /></a>
                        {/if}
                    </td>

                    <td class="right">
                        <ul class="action-buttons clearfix">

                            <li>
                                <a href="instances.php?action=edit&id={$instance->id}" title="{t}Edit{/t}">
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            </li>

                            <li>
                                <a href="instances.php?action=delete&id={$instance->id}" onclick="confirmar()" title="{t}Delete{/t}">
                                <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                            </li>
                        </ul>
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td class="empty" colspan="5">{t}There is no available instances yet{/t}</td>
                </tr>
                {/foreach}
            </tbody>

            <tfoot>
                <tr class="pagination">
                    <td colspan="5">
                        {$pager->links}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>
{/block}
