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
            <h3 class="pull-right">
                <small class="text-right" ng-if="instance.id">
                    <i class="fa fa-user"></i>
                    [% instance.contact_mail %]
                    |
                    <i class="fa fa-clock-o"></i>
                    [% instance.created %]
                    |
                    <i class="fa fa-code"></i>
                    [% instance.external.contact_ip %]
                    |
                    <i class="fa fa-database"></i>
                    [% instance.media_size | number: 2 %] MB
                </small>
            </h3>
        </div>
        <div class="grid-body">
            <div class="row">
                <div class="col-sm-12">
                    <h4>General information</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="form-group">
                        <label class="form-label">{t}Site name{/t}</label>
                        <span class="help">{t}(Human readable name){/t}</span>
                        <div class="controls">
                            <input class="form-control" ng-model="instance.name" type="text">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">{t}Internal name{/t}</label>
                        <span class="help">{t}Alphanumeric, without spaces{/t}</span>
                        <div class="controls">
                            <input class="form-control" ng-model="instance.internal_name" type="text">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label" for="template">{t}Database{/t}</label>
                        <div class="controls">
                            <input class="form-control" ng-model="instance.settings.BD_DATABASE" type="text">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">{t}Last invoice date{/t}</label>
                        <div class="controls">
                            <quick-datepicker icon-class="fa fa-clock-o" ng-model="instace.external.last_invoice" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h4>Domains</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="form-group">
                        <label for="domains" class="form-label">{t}Domains{/t}</label>
                        <div class="controls">
                            <tags-input ng-model="domains" placeholder="{t}Add a domain{/t}" add-on-enter="true" add-on-space="true" add-on-comma="true" min-tags="1" on-tag-added="cleanDomains();" on-tag-removed="cleanDomains();">
                            </tags-input>
                        </div>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="domains" class="form-label">{t}Main domain{/t}</label>
                        <div class="controls">
                            <select name="main-domain" id="main-domain" ng-model="instance.main_domain">
                                <option value="0">{t}None{/t}</option>
                                <option value="[% $index + 1 %]" ng-repeat="domain in instance.domains" ng-selected="instance.main_domain == ($index + 1)">[% domain %]</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{t}Domain expire date:{/t}</label>
                        <div class="controls">
                            <quick-datepicker icon-class="fa fa-clock-o" ng-model="instace.external.domain_expire" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4>{t}Internals{/t}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
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
                        </div>
                        <div class="col-sm-6">
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
                                    <input  id="max-mailing" ng-model="instance.external.max_mailing" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4>External services</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
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
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-center">
                    <button class="btn btn-primary btn-large" ng-click="save();" ng-disabled="true" ng-if="!instance.id">
                        <i class="fa fa-save"></i> {t}Save{/t}
                    </button>
                    <button class="btn btn-primary btn-large" ng-click="update();" ng-disabled="true" ng-if="instance.id">
                        <i class="fa fa-save"></i> {t}Save{/t}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
