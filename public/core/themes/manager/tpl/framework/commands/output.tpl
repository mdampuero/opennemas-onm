<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <a ng-href="[% routing.ngGenerate('manager_commands') %]">
                            <i class="fa fa-cubes fa-lg"></i>
                            {t}Commands{/t}
                        </a>
                    </h4>
                </li>
                <li class="quicklinks seperate">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <h5>
                        {t}Command output{/t}
                    </h5>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="content">
    <div class="wrapper-table-block">
        <h4 class="command-name"><strong>{t}Command name:{/t}</strong> "[% name %]"</h4>

        <pre class="command-output">[% output %]</pre>

        <a class="btn btn-primary" ng-href="[% routing.ngGenerate('manager_commands') %]">{t}Go back{/t}</a>
    </div>
</div>
