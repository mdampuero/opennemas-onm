{extends file="base/base.tpl"}

{block name="header-css" append}
<style type="text/css">
    .deploy {
        border-radius:20px;
    }
</style>
{/block}
{block name="footer-js" append}
<script type="text/javascript">
    $(function() {
        $().button('loading')
    });
</script>
{/block}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Application commands{/t}</h2>
        </div>
    </div>
</div>
<div class="wrapper-content">
    <table class="table table-hover table-stripped ">
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
                                    {foreach $instances as $instance}
                                    <option value="{$instance}">{$instance}</option>
                                    {/foreach}
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
            {foreach $commands as $command}
            <tr>
                <td>{$command->getName()}</td>
                <td>{$command->getDescription()}</td>
                <td></td>
                <td class="right">
                    <div class="btn-group">
                        <a class="btn btn-danger" href="{url name=manager_framework_command_execute command=$command->getName()}">
                            <i class="icon icon-cog"></i>
                            {t}Execute{/t}
                        </a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{/block}
