<div class="content">
    <div class="page-title clearfix">
        <h3 class="pull-left">
            <i class="fa fa-cubes"></i>
            <span ng-if="!instance.id">{t}New instance{/t}</span>
            <span ng-if="instance.id">{t}Edit instance{/t}</span>
        </h3>
        <ul class="breadcrumb pull-right">
            <li>
                <p>{t}YOU ARE HERE{/t}</p>
            </li>
            <li>
                <a href="#">{t}Dashboard{/t}</a>
            </li>
            <li>
                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instances_list') %]">{t}Instances{/t}</a>
            </li>
            <li>
                <span class="active" ng-if="!instance.id">{t}New instance{/t}</span>
                <span class="active" ng-if="instance.id">{t}Edit instance{/t}</span>
            </li>
        </ul>
    </div>
    <div class="grid simple">
        <div class="grid-title clearfix">
            <h3 class="pull-left">
                <span class="semi-bold" ng-if="instance.id">
                    [% instance.name %]
                </span>
                <span class="semi-bold" ng-if="!instance.id">
                    {t}New instance{/t}
                </span>
            </h3>
            <div class="pull-right">
                <button class="btn btn-primary" ng-click="save();" ng-disabled="saving || instanceForm.$invalid || instance.domains.length == 0" ng-if="!instance.id">
                    <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
                <button class="btn btn-primary" ng-click="update();" ng-disabled="saving || instanceForm.$invalid || instance.domains.length == 0" ng-if="instance.id">
                    <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
            </div>
        </div>
        <div class="grid-body">
            <form name="instanceForm" novalidate>
                <div class="row">
                    <h3 ng-class="{ 'col-sm-3': instance.id, 'col-sm-12': !instance.id }" ng-if="instance.id">
                        <small ng-if="instance.id">
                            <p>
                                <i class="fa fa-user"></i>
                                [% instance.contact_mail %]
                            </p>
                            <p>
                                <i class="fa fa-clock-o"></i>
                                [% instance.created %]
                            </p>
                            <p>
                                <i class="fa fa-code"></i>
                                [% instance.external.contact_ip %]
                            </p>
                            <p>
                                <i class="fa fa-database"></i>
                                [% instance.media_size | number: 2 %] MB
                            </p>
                        </small>
                    </h3>
                    <div ng-class="{ 'col-sm-9': instance.id, 'col-sm-12': !instance.id }">
                        <h4>General information</h4>
                        <div class="form-group">
                            <label class="form-label">{t}Site name{/t}</label>
                            <span class="help">{t}(Human readable name){/t}</span>
                            <div class="controls">
                                <input class="form-control" ng-model="instance.name" required type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="template" class="form-label">{t}Template{/t}</label>
                            <div class="controls">
                                <select id="template" ng-model="instance.settings.TEMPLATE_USER" ng-options="key as value.name for (key,value) in template.templates"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{t}Last invoice date{/t}</label>
                            <div class="controls">
                                <quick-datepicker icon-class="fa fa-clock-o" ng-model="instace.external.last_invoice" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                            </div>
                        </div>
                        <h4>Domains</h4>
                        <div class="row form-group" ng-repeat="domain in instance.domains">
                            <div class="col-sm-11">
                                <div class="radio radio-input">
                                    <input id="domain[% $index + 1 %]" ng-model="instance.main_domain" type="radio" value="[% $index + 1 %]">
                                    <label for="domain[% $index + 1 %]">
                                        <input class="form-control" ng-model="instance.domains[$index]" type="text">
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <button class="btn btn-danger" ng-click="removeDomain($index)" type="button">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="domains" class="form-label">{t}Domains{/t}</label>
                            <div class="controls">
                                <div class="input-group">
                                    <input class="form-control" ng-model="new_domain" type="text">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" ng-click="addDomain();" type="button">{t}Add{/t}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{t}Domain expire date:{/t}</label>
                            <div class="controls">
                                <quick-datepicker icon-class="fa fa-clock-o" ng-model="instace.external.domain_expire" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                            </div>
                        </div>

                        <h4>Modules</h4>
                        <div class="form-group">
                            <label for="modules" class="form-label">{t}Modules{/t}</label>
                            <div class="controls">
                                <select id="modules" multiple ui-select2 ng-model="instance.external.modules" ng-options="key as value for (key,value) in template.available_modules"></select>
                            </div>
                        </div>

                        <h4>{t}Internals{/t}</h4>
                        <div class="form-group">
                            <label class="form-label">{t}Internal name{/t}</label>
                            <span class="help">{t}Alphanumeric, without spaces{/t}</span>
                            <div class="controls">
                                <input class="form-control" ng-model="instance.internal_name" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="template">{t}Database{/t}</label>
                            <div class="controls">
                                <input class="form-control" ng-model="instance.settings.BD_DATABASE" required type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="template">{t}Language{/t}</label>
                            <div class="controls">
                                <select ng-model="instance.external.site_language" ng-options="key as value for (key, value) in template.languages"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{t}Time Zone{/t}</label>
                            <div class="controls">
                                <select ng-model="instance.external.time_zone" ng-options="key as value for (key, value) in template.timezones"></select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="template">{t}Minimum password level{/t}</label>
                            <div class="controls">
                                <select ng-model="instance.external.pass_level">
                                    <option value="-1" >{t}Default{/t}</option>
                                    <option value="0" >{t}Weak{/t}</option>
                                    <option value="1" >{t}Good{/t}</option>
                                    <option value="2" >{t}Strong{/t}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="max-mailing" class="form-label">{t}Num Max emails sent by month{/t}</label>
                            <div class="controls">
                                <input id="max-mailing" ng-model="instance.external.max_mailing" type="text">
                            </div>
                        </div>

                        <h4>External services</h4>

                        <div class="form-group">
                            <label class="form-label" for="piwik-page-id">{t}Piwik Statistics{/t} - {t}Page ID:{/t}</label>
                            <div class="controls">
                                <input class="form-control" id="piwik-page-id" ng-model="instance.external.piwik.page_id" type="text">
                                <div class="help-block">
                                    {t escape=off}You can get your Piwik Site information from <a href="https://piwik.openhost.es/admin">our Piwik server</a>.{/t}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="piwik-server-url">{t}Piwik Statistics{/t} - {t}Server url{/t}</label>
                            <div class="controls">
                                <input class="form-control" id="piwik-server-url" ng-model="instance.external.piwik.server_url" type="text">
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
