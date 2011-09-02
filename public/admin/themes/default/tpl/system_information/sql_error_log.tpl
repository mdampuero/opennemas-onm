{extends file="base/admin.tpl"}
{block name="footer-js" append}
{/block}
{block name="header-css" append}
<style type="text/css">
.errorexplanation{
    padding:6px 0;
}
.number {
    padding:0 5px;
}
.errorexplanation .title {
    font-size:1.12em;
    margin-bottom:3px;
    display:block;
}
</style>
{/block}

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

            <table class="adminheading">
                <tr>
                    <th nowrap>{$total_errors} registered SQL errors</th>
                    <th scope="col" style="text-align:right;">
                        <form method="GET" action="{$smarty.server.PHP_SELF}">
                            <input type="text" name="search" value="{$search}">
                            <button type="submit">{t}Search{/t}</button>
                        </form>
                    </th>
                </tr>
            </table>

            <table class="adminlist">

                <thead>
                   <tr>
                        <th scope="col">#</th>
                        <th style="width:120px; text-align:center;">{t}Date{/t}</th>
                        <th >{t}Error explanation{/t}</th>
                        <th style="width:80px; text-align:center;">{t}Execution time{/t}</th>
                   </tr>
                </thead>
                <tbody>
                    {foreach from=$errors item=error name=errors}
                    <tr {cycle values="class=row0,class=row1"} colspan=3>
                        <td class="number">
                            {$smarty.foreach.errors.iteration+$elements_page}
                        </td>
                        <td style="text-align:center;">
                            {$error['created']}
                        </td>
                        <td class="errorexplanation">
                            <span class="title">{$error['tracer']}</span>
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
                            <td>
                                {t}There is no SQL errors registered in database.{/t}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>

                <tfoot>
                    {if count($errors) gt 0}
                    <tr class="pagination">
                        <td colspan="9" align="center">{$pagination->links}</td>
                    </tr>
                    {/if}
                </tfoot>

             </table>

         </div>


        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div>
{dialogo script="print"}

{/block}
