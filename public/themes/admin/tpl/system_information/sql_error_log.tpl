{extends file="base/admin.tpl"}
{block name="content"}
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}SQL error log{/t}
                        </h4>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <ul class="old-button">
                <li>
                    <a class="admin_add" href="{url name="admin_databaseerrors_purge"}">
                        <img alt="{t}Clean{/t}" src="{$params.IMAGE_DIR}/editclear.png"><br>
                        {t}Clean{/t}
                    </a>
                </li>
                <li>
                    <a title="{t}Refresh the list for getting newest error list{/t}" href="{url name=admin_databaseerrors}">
                        <img alt="{t}Refresh list{/t}" src="{$params.IMAGE_DIR}/template_manager/refresh48x48.png"><br>
                        {t}Refresh list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            <div class="pull-left">
                {$total_errors} registered SQL errors
            </div>
            <div class="pull-right">
                <form method="GET" action="{$smarty.server.PHP_SELF}" class="form-inline">
                    <div class="input-append">
                        <input type="search" name="search" value="{$search}" class="input-medium" placeholder="{t}Filter{/t}">
                        <button type="submit" class="btn"><i class="icon-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-hover table-condensed">

            <thead>
               <tr>
                    <th scope="col" style="width:5px;">#</th>
                    <th class="center" style="width:120px; text-align:center;">{t}Creation date{/t}</th>
                    <th >{t}Error explanation{/t}</th>
                    <th class="center" style="width:120px; text-align:center;">{t}Execution time{/t}</th>
               </tr>
            </thead>
            <tbody>
                {foreach from=$errors item=error name=errors}
                <tr>
                    <td class="center">{$smarty.foreach.errors.iteration+$elements_page}</td>
                    <td class="center">{$error['created']}</td>
                    <td>
                        <strong>{$error['tracer']}</strong><br>
                        <strong>SQL:</strong> {$error['sql1']}<br>
                        <strong>Params:</strong> {$error['params']}<br>
                        <strong>Other info:</strong> {$error['sql0']}
                    </td>
                    <td class="center">{$error['timer']}</td>
                </tr>
                {foreachelse}
                    <tr>
                        <td colspan=4 class="empty">
                            {t}There is no SQL errors registered in database.{/t}
                        </td>
                    </tr>
                {/foreach}
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" class="center">
                        <div class="pagination">
                            {$pagination->links}
                        </div>
                    </td>
                </tr>
            </tfoot>

         </table>

    </div>
{/block}
