<div class="content">

    <div class="page-title clearfix">
        <h3 class="pull-left">
            <i class="fa fa-code"></i> {t}Commands{/t}
        </h3>
        <ul class="breadcrumb pull-right">
            <li>
                <p>{t}YOU ARE HERE{/t}</p>
            </li>
            <li>
                <a href="#">{t}Dashboard{/t}</a>
            </li>
            <li>
                <a href="#/instances" class="active">{t}Commands{/t}</a>
            </li>
        </ul>
    </div>

    <div class="grid simple">
        <div class="grid-body">
            <table class="table no-margin no-padding">
                <thead>
                    <tr>
                        <th>{t}Command name{/t}</th>
                        <th>{t}Description{/t}</th>
                        <th>{t}Params{/t}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>app:deploy</td>
                        <td>
                            Deploys the application to the latest version
                            <div class="help-block">
                                This could take a while. Please do not interrupt this execution while running.
                            </div>
                        </td>
                        <td></td>
                        <td class="right">
                            <div class="btn-group">
                                <a href="{url name=manager_framework_command_execute command="app:deploy"}" class="btn btn-danger deploy btn-huge" data-loading-text="Loading...">
                                    <i class="icon-cloud-download icon-2x"></i>
                                    {t}Deploy{/t}
                                </a>
                            </div>
                        </td>
                    </tr>
                    <form action="{url name=manager_framework_command_execute}" method="GET">
                    <tr>
                        <td>
                            clean:smarty-cache
                        </td>
                        <td>
                            Cleans the cache for an specific instance

                            <div class="help-block">
                                Select the desired instance where clean cache and compile files in. <br>Select 'All' for cleaning all the cache/compile files.
                            </div>
                        </td>
                        <td>
                            <div class="control-group">
                                <div class="control">
                                    <select name="params[instance]">
                                        <option value="">All</option>
                                        <option value="{$instance}" ng-repeat="instance in instances">[% instance %]</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="command" value="clean:smarty-cache">
                        </td>
                        <td class="right">
                            <button type="submit" class="btn btn-danger"><i class="icon-trash"></i> {t}Execute{/t}</button>
                        </td>
                    </tr>
                    </form>
                    <tr ng-repeat="command in commands">
                        <td>[% command.name %]</td>
                        <td>[% command.description %]</td>
                        <td></td>
                        <td class="right">
                            <div class="btn-group">
                                <a class="btn btn-danger" href="">
                                    <i class="icon icon-cog"></i>
                                    {t}Execute{/t}
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="wrapper-table-block">

    </div><!-- .wrapper-table-block -->
</div>
