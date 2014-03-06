{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_widgets}" method="GET" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Widgets{/t}</h2>
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
        {render_messages}
        <div class="table-info clearfix">
            {acl hasCategoryAccess=$category}<div class="pull-left"><strong>{t 1=$totalWidgets}%1 widgets{/t}</strong></div> {/acl}
            <div class="pull-right">
                <div class="form-inline">
                    <select name="type">
                        <option value="-1" {if $status === -1} selected {/if}> {t}-- All --{/t} </option>
                        <option value="intelligentwidget" {if $status === intelligentwidget} selected {/if}> {t}IntelligentWidget{/t} </option>
                        <option value="html" {if  $status === html} selected {/if}> {t}HTML{/t} </option>
                        <option value="smarty" {if $status === smarty} selected {/if}> {t}Smarty{/t} </option>
                    </select>
                    {t}Status:{/t}
                    <div class="input-append">
                        <select name="status">
                            <option value="-1" {if $status === -1} selected {/if}> {t}-- All --{/t} </option>
                            <option value="1" {if  $status === 1} selected {/if}> {t}Published{/t} </option>
                            <option value="0" {if $status === 0} selected {/if}> {t}No published{/t} </option>
                        </select>
                        <button type="submit" class="btn"><i class="icon-search"></i> </button>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-condensed" >
            <thead>
                {if count($widgets) > 0}
                <th>{t}Name{/t}</th>
                <th style="width:70px">{t}Type{/t}</th>
                <th class="center" style="width:20px">{t}Published{/t}</th>
                <th class="center" style="width:10px">Actions</th>
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

                    <td class="right nowrap" >
                        <div class="btn-group">

                        {if ($widgets[wgt]->renderlet != 'intelligentwidget' or true)}
                        {acl isAllowed="WIDGET_UPDATE"}
                        <a href="{url name=admin_widget_show id=$widgets[wgt]->pk_widget page=$page}" title="{t}Edit{/t}" class="btn">
                            <i class="icon-pencil"></i> {t}Edit{/t}
                        </a>
                        {/acl}
                        {acl isAllowed="WIDGET_DELETE"}
                            <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                               data-url="{url name=admin_widget_delete id=$widgets[wgt]->pk_widget page=$page}" title="{t}Delete{/t}"
                               data-title="{$widgets[wgt]->title|capitalize}" href="{url name=admin_widget_delete id=$widgets[wgt]->pk_widget page=$page}" >
                                <i class="icon-trash icon-white"></i>
                            </a>
                        {/acl}
                        {/if}
                        </div>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td class="empty" colspan="5">
                        {t}There is no available widgets{/t}
                    </td>
                </tr>
                {/section}
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="5" class="center">
                        <div class="pagination">
                            {$pagination->links}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
        {include file="widget/modals/_modalDelete.tpl"}
</form>
{/block}
