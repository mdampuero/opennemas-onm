{extends file="base/admin.tpl"}

{block name="content"}
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        {t}SQL error log{/t}
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" title="{t}Refresh the list for getting newest error list{/t}" href="{url name=admin_databaseerrors}">
                            <span class="fa fa-refresh"></span> <span class="hidden-xs">{t}Refresh list{/t}</span>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <a class="btn btn-primary" href="{url name="admin_databaseerrors_purge"}">
                          <span class="fa fa-trash-o"></span> {t}Clean{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="m-r-10 input-prepend inside search-form no-boarder">
                    <form method="GET" action="{url name="admin_databaseerrors"}" class="form-inline">
                        <div class="input-append">
                            <input type="search" name="search" value="{$search}" class="input-medium" placeholder="{t}Filter{/t}">
                        </div>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="content">
    {render_messages}

    <div class="grid simple">
        <div class="grid-body {if count($errors) != 0} no-padding{/if}">


            <div class="table-wrapper ng-cloak">
                {if count($errors) > 0}

                <table class="table table-hover">
                    <thead>
                       <tr>
                            <th class="center nowrap" style="width:120px; text-align:center;">{t}Creation date{/t}</th>
                            <th >{t}Error explanation{/t}</th>
                            <th class="center" style="width:120px; text-align:center;">{t}Execution time{/t}</th>
                       </tr>
                    </thead>
                    <tbody>
                        {foreach $errors as $error}
                        <tr>
                            <td class="center">{$error['created']}</td>
                            <td>
                                <strong>{$error['tracer']}</strong><br>
                                <strong>SQL:</strong> {$error['sql1']}<br>
                                <strong>Params:</strong> {$error['params']}<br>
                                <strong>Other info:</strong> {$error['sql0']}
                            </td>
                            <td class="center">{$error['timer']}</td>
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
                {else}
                <div class="center">
                  <h5>{t}There is no SQL errors registered in database.{/t}</h5>
                  <p>{t}Come back later.{/t}</p>
                </div>
                {/if}
            </div>
        </div>
    </div>

</div>
{/block}
