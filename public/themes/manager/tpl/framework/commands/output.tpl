<div class="content">
    <div class="page-title clearfix">
        <h3 class="pull-left">
            <i class="fa fa-cubes"></i>
            <span>{t}Application command output{/t}</span>
        </h3>
        <ul class="breadcrumb pull-right">
            <li>
                <p>{t}YOU ARE HERE{/t}</p>
            </li>
            <li>
                <a href="#">{t}Dashboard{/t}</a>
            </li>
            <li>
                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_framework_commands') %]">{t}Instances{/t}</a>
            </li>
        </ul>
    </div>
    <div class="wrapper-table-block">
        <h4 class="command-name"><strong>{t}Command name:{/t}</strong> "[% name %]"</h4>

        <pre class="command-output">[% output %]</pre>

        <a class="btn btn-primary" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_framework_commands') %]">{t}Go back{/t}</a>
    </div>
</div>
