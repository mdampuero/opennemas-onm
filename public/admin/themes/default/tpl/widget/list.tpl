{extends file="base/admin.tpl"}

{block name="footer-js" append}
{script_tag src="/prototype.js" language="javascript"}
{script_tag src="/prototype-date-extensions.js" language="javascript"}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js?load=effects,dragdrop"></script>
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

{/block}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Widget Manager{/t}</h2>
        </div>
        <ul class="old-button">
              {acl isAllowed="WIDGET_CREATE"}
            <li>
                <a href="widget.php?action=new" class="admin_add"
                   title="{t}New widget{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="" alt="" />
                    <br />{t}New{/t}
                </a>
            </li>
            {/acl}
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
                        {$widgets[wgt]->title}
                    </td>

                    <td>
                        {$widgets[wgt]->renderlet|upper}
                    </td>

                    <td class="center">
                        {acl isAllowed="WIDGET_AVAILABLE"}
                        {if $widgets[wgt]->available == 1}
                        <a href="?id={$widgets[wgt]->pk_widget}&amp;action=changeavailable" class="switchable" title="{t}Published{/t}">
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                        {else}
                        <a href="?id={$widgets[wgt]->pk_widget}&amp;action=changeavailable" class="switchable" title="{t}Unpublished{/t}">
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Unpublished{/t}" /></a>
                        {/if}
                        {/acl}
                    </td>

                    <td>
                        <ul class="action-buttons clearfix">
                            {if ($widgets[wgt]->renderlet != 'intelligentwidget' or true)}
                            {acl isAllowed="WIDGET_UPDATE"}
                            <li>
                                <a href="widget.php?action=edit&id={$widgets[wgt]->pk_widget}" title="{t}Edit{/t}">
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            </li>
                            {/acl}
                            {acl isAllowed="WIDGET_DELETE"}
                            <li>
                                <a class="del" data-controls-modal="modal-from-dom"
                                   data-id="{$widgets[wgt]->pk_widget}" title="{t}Delete{/t}"
                                   data-title="{$widgets[wgt]->title|capitalize}" href="#" >
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                            </li>
                            {/acl}
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
    {include file="widget/modals/_modalDelete.tpl"}
{/block}
