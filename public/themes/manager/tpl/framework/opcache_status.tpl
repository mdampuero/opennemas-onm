<div class="content">
    <div class="title">
        <h2>{t}Zend Opcache status{/t}</h2>
    </div>

    <div class="opcache-stats">
        <div class="alert alert-block alert-error fade in" ng-if="serverData.not_supported_message">
            <button type="button" class="close" data-dismiss="alert"></button>
            <h4 class="alert-heading"><i class="icon-warning-sign"></i> Error!</h4>
            <p>[% serverData.not_supported_message %]</p>
        </div>
        <tabset class="tab-form" ng-if="!serverData.not_supported_message">
            <tab heading="{t}Status{/t}">

                <h4>{t}Opcache summary{/t}</h4>
                <div class="row">
                    <div google-chart chart="chartObjectMem" style="[% cssStyle %] min-height:300px" class="col-md-4"></div>
                    <div google-chart chart="chartObjectKeys" style="[% cssStyle %] min-height:300px" class="col-md-4"></div>
                    <div google-chart chart="chartObjectHits" style="[% cssStyle %] min-height:300px" class="col-md-4"></div>
                </div>

                <h4>{t}Detailed statistics{/t}</h4>

                <table class="table table-condensed table-stripped">
                    <tr ng-repeat="(key, value) in serverData.status_key_values">
                        <td>[% key %]</td>
                        <td>[% value %]</td>
                    </tr>
                </table>
            </tab>
            <tab heading="{t}Configuration{/t}">
                <table class="table table-condensed table-stripped">
                    <tr ng-repeat="(key, value) in serverData.directive_key_values">
                        <td>[% key %]</td>
                        <td>[% value %]</td>
                    </tr>
                </table>
            </tab>
            <tab heading="{t}Scripts{/t}">
                <table class="table table-condensed table-stripped">
                    <thead>
                        <tr>
                            <th width="70%">{t}Path{/t}</th>
                            <th width="20%">{t}Memory{/t}</th>
                            <th width="10%">{t}Hits{/t}</th>
                        </tr>
                    </thead>
                    <tr ng-repeat-start="dir in  serverData.files_key_values">
                        <td>[% dir.name %] ([% dir.count %])</td>
                        <td>[% dir.total_memory_consumption %]</td>
                        <td></td>
                    </tr>
                    <tr ng-repeat-end="(fileName, fileInfo) in dir.files">
                        <td>[% fileInfo.full_path %]</td>
                        <td>[% fileInfo.memory_consumption_human_readable %]</td>
                        <td>[% fileInfo.hits %]</td>
                    </tr>
                </table>
            </tab>
        </tabset>
    </div>
</div>
