{extends file="base/admin.tpl"}

{block name="footer-js" append}
{script_tag src="/prototype.js" language="javascript"}
{script_tag src="/prototype-date-extensions.js" language="javascript"}
{script_tag src="/scriptaculous/scriptaculous.js?load=effects,dragdrop" language="javascript"}

{* Ajax button to change availability *}
{script_tag src="/switcher_flag.js" language="javascript"}
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
            <h2>{t}Widget Manager{/t}</h2>
        </div>
        <ul class="old-button">
            <li>
                <a href="widget.php?action=new" class="admin_add"
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
                {if count($widgets) > 0}
                <th width="85%">{t}Name{/t}</th>
                <th>{t}Type{/t}</th>
                <th class="center">{t}Published{/t}</th>
                <th class="center">Actions</th>
                {else}
                <th scope="col" colspan=4>&nbsp;</th>
                {/if}
            </thead>

            <tbody>
                {section name=wgt loop=$widgets}
                <tr>
                    <td>
                        <a href="widget.php?action=edit&id={$widgets[wgt]->pk_widget}" title="{t}Edit{/t}">
                            {$widgets[wgt]->title}
                        </a>

                    </td>

                    <td>
                        {$widgets[wgt]->renderlet|upper}
                    </td>

                    <td class="center">
                        {if $widgets[wgt]->available == 1}
                        <a href="?id={$widgets[wgt]->pk_widget}&amp;action=changeavailable" class="switchable" title="{t}Published{/t}">
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                        {else}
                        <a href="?id={$widgets[wgt]->pk_widget}&amp;action=changeavailable" class="switchable" title="{t}Unpublished{/t}">
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Unpublished{/t}" /></a>
                        {/if}
                    </td>

                    <td>
                        <ul class="action-buttons clearfix">

                            {if ($widgets[wgt]->renderlet != 'intelligentwidget' or true)}
                            <li>
                                <a href="widget.php?action=edit&id={$widgets[wgt]->pk_widget}" title="{t}Edit{/t}">
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            </li>

                            <li>
                                <a href="widget.php?action=delete&id={$widgets[wgt]->pk_widget}" onclick="confirmar()" title="{t}Delete{/t}">
                                <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                            </li>
                            {else}
                            <li></li>
                            {/if}

                        </ul>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td class="empty" colspan="5">{t}There is no available widgets{/t}</td>
                </tr>
                {/section}
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
