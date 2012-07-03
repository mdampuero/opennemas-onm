{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/prototype.js" language="javascript"}
    {script_tag src="/prototype-date-extensions.js" language="javascript"}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js?load=effects,dragdrop"></script>
    {* Ajax button to change availability *}
    {script_tag src="/switcher_flag.js" language="javascript"}
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
                <a href="{url name=admin_widget_create}" class="admin_add"
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
    ̈́{render_messages}

    <div id="pagina">
        <table class="listing-table" >

            <thead>
                {if count($widgets) > 0}
                <th>{t}Name{/t}</th>
                <th style="width:70px">{t}Type{/t}</th>
                <th class="center" style="width:20px">{t}Published{/t}</th>
                <th class="right" style="width:100px">Actions</th>
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
                        <a href="{url name=admin_widget_toogle_available id=$widgets[wgt]->pk_widget page=$page}" class="switchable" title="{t}Published{/t}">
                            <img src="{$params.IMAGE_DIR}publish_g.png"alt="{t}Published{/t}" /></a>
                        {else}
                        <a href="{url name=admin_widget_toogle_available id=$widgets[wgt]->pk_widget page=$page}" class="switchable" title="{t}Unpublished{/t}">
                            <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Unpublished{/t}" /></a>
                        {/if}
                        {/acl}
                    </td>

                    <td class="right" >
                        {if ($widgets[wgt]->renderlet != 'intelligentwidget' or true)}
                        {acl isAllowed="WIDGET_UPDATE"}
                        <a href="{url name=admin_widget_show id=$widgets[wgt]->pk_widget page=$page}" title="{t}Edit{/t}" class="btn btn-mini">
                            {t}Edit{/t}
                        </a>
                        {/acl}
                        {acl isAllowed="WIDGET_DELETE"}
                            <a class="del btn btn-mini btn-danger" data-controls-modal="modal-from-dom"
                               data-url="{url name=admin_widget_delete id=$widgets[wgt]->pk_widget page=$page}" title="{t}Delete{/t}"
                               data-title="{$widgets[wgt]->title|capitalize}" href="{url name=admin_widget_delete id=$widgets[wgt]->pk_widget page=$page}" >
                                {t}Delete{/t}
                            </a>
                        {/acl}
                        {/if}
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
                        {$pagination->links}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>
    {include file="widget/modals/_modalDelete.tpl"}
{/block}
