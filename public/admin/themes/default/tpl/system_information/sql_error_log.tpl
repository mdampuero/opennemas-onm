{extends file="base/admin.tpl"}
{block name="content"}
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}SQL error log{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a class="admin_add" href="{$smarty.server.PHP_SELF}?action=purge">
                        <img border="0" alt="" src="{$params.IMAGE_DIR}/editclear.png"><br>
                        {t}Clean{/t}
                    </a>
                </li>
                <li>
                    <a title="{t}Refresh the list for getting newest error list{/t}" href="{$smarty.server.PHP_SELF}">
                        <img border="0" src="{$params.IMAGE_DIR}/template_manager/refresh48x48.png"><br>
                        {t}Refresh list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">


         <div id="{$category}">
            <div class="table-info clearfix">
                <div>
                    <div class="left">
                        {$total_errors} registered SQL errors
                    </div>
                    <div class="right">
                        <form method="GET" action="{$smarty.server.PHP_SELF}">
                            <input type="text" name="search" value="{$search}">
                            <button type="submit">{t}Search{/t}</button>
                        </form>
                    </div>
                </div>
            </div>

            <table class="listing-table">

                <thead>
                   <tr>
                        <th scope="col">#</th>
                        <th style="width:120px; text-align:center;">{t}Date{/t}</th>
                        <th >{t}Error explanation{/t}</th>
                        <th style="width:80px; text-align:center;">{t}Execution time{/t}</th>
                   </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan=4 scope=col class="family_type">
                            asdfasdf
                        </td>
                    </tr>
                    {foreach from=$errors item=error name=errors}
                    <tr>
                        <td class="number">
                            {$smarty.foreach.errors.iteration+$elements_page}
                        </td>
                        <td style="text-align:center;">
                            {$error['created']}
                        </td>
                        <td class="errorexplanation">
                            <strong>{$error['tracer']}</strong>
                            <br>
                            <strong>SQL:</strong> {$error['sql1']}
                            <br>
                            <strong>Params:</strong> {$error['params']}
                            <br>
                            <strong>Other info:</strong> {$error['sql0']}
                        </td>
                        <td style="text-align:center;">
                            {$error['timer']}
                        </td>
                    </tr>
                    {foreachelse}
                        <tr>
                            <td colspan=4 class="empty">
                                {t}There is no SQL errors registered in database.{/t}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>

                {if $pagination->_totalItems > 0}
                <tfoot>
                    <tr class="pagination">
                        <td colspan="4">{$pagination->links}</td>
                    </tr>
                </tfoot>
                {/if}

             </table>

         </div>


        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div>
{dialogo script="print"}

{/block}
