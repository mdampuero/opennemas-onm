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
    <form name="instanceForm" novalidate>
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
        <div class="grid-body instance-form">
            <div class="row">
                <div ng-class="{ 'col-sm-3': instance.id, 'col-sm-12': !instance.id }" ng-if="instance.id">
                    <dl ng-if="instance.id">
                        <dt>
                            <h5><i class="fa fa-user"></i> {t}Owner email{/t}</h5>
                        </dt>
                        <dd>
                            [% instance.contact_mail %]
                        </dd>
                        <dt>
                            <h5><i class="fa fa-clock-o"></i> {t}Created at{/t}</h5>
                        </dt>
                        <dd>
                            [% instance.created %]
                        </dd>
                        <dt>
                            <h5><i class="fa fa-code"></i> {t}Created from{/t}</h5>
                        </dt>
                        <dd>
                            <span ng-if="instance.external.contact_ip">[% instance.external.contact_ip %]</span>
                            <span ng-if="!instance.external.contact_ip">{t}Not defined{/t}</span>
                        </dd>
                        <dt>
                            <h5><i class="fa fa-database"></i> {t}Media size{/t}</h5>
                        </dt>
                        <dd>
                            [% instance.media_size | number: 2 %] MB
                        </dd>
                    </dl>
                </div>
                <div ng-class="{ 'col-sm-9': instance.id, 'col-sm-12': !instance.id }">
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
                        <label for="template" class="form-label">{t}Activated{/t}</label>
                        <div class="controls">
                            <input type="checkbox" id="template" class="ios-switch bigswitch" ng-model="instance.activated" ng-true-value="1" ng-false-value="0" />
                            <div><div></div></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="grid simple">
                <div class="grid-title no-border">
                    <h4>{t}Domains{/t}</h4>
                </div>
                <div class="grid-body no-border instance-domain-list">
                    <div class="row form-group" ng-if="instance.domains.length > 0">
                        <div class="col-sm-12">
                            <div class="radio">
                                <input id="domain0" ng-model="instance.main_domain" type="radio" value="0">
                                <label for="domain0">{t}No main domain{/t}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group" ng-repeat="domain in instance.domains">
                        <div class="col-sm-12">
                            <div class="radio radio-input radio-primary">
                                <input id="domain[% $index + 1 %]" ng-model="instance.main_domain" type="radio" value="[% $index + 1 %]" class="blue">
                                <label for="domain[% $index + 1 %]">
                                    <div class="input-group">
                                        <input class="form-control" ng-model="instance.domains[$index]" type="text">
                                        <span class="input-group-btn">
                                            <button class="btn btn-danger" ng-click="removeDomain($index)" type="button">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="controls">
                            <div class="input-group new-domain">
                                <input class="form-control " ng-model="new_domain" type="text">
                                <div class="input-group-btn">
                                    <button class="btn btn-default" ng-click="addDomain();" type="button">{t}Add{/t}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{t}Domain expire date:{/t}</label>
                        <div class="controls">
                            <quick-datepicker icon-class="fa fa-clock-o" ng-model="instance.external.domain_expire" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="grid simple">
                <div class="grid-title no-border">
                    <h4>{t}Billing{/t}</h4>
                </div>
                <div class="grid-body no-border">
                    <div class="form-group">
                        <label class="form-label">{t}Last invoice date{/t}</label>
                        <div class="controls">
                            <quick-datepicker icon-class="fa fa-clock-o" ng-model="instance.external.last_invoice" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid simple">
        <div class="grid-title no-border">
            <h4>{t}Modules{/t}</h4>
        </div>
        <div class="grid-body no-border">
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox check-default  check-title">
                        <button class="btn" ng-click="selectAll()">{t}Select all{/t}</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 instance-plan-block" ng-repeat="planName in template.plans">
                    <div class="checkbox check-default check-title col-sm-12">
                        <input id="checkbox-[% planName %]" ng-model="selected.plan[planName]" ng-change="togglePlan(planName)" ng-checked="isPlanSelected(planName)" type="checkbox">
                        <label for="checkbox-[% planName %]">
                            <h5>Plan [% planName %]</h5>
                        </label>
                    </div>
                    <div class="checkbox check-default col-sm-4" ng-repeat="module in template.available_modules|filter:{ plan : planName}">
                        <input id="checkbox-[% module.id %]" checklist-model="instance.external.activated_modules" checklist-value="module.id" type="checkbox">
                        <label for="checkbox-[% module.id %]">
                            [% module.name %]
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="grid simple">
                <div class="grid-title no-border">
                    <h4>{t}Internal settings{/t}</h4>
                </div>
                <div class="grid-body no-border">
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
                            <input class="form-control" ng-model="instance.settings.BD_DATABASE" type="text">
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
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="grid simple">
                <div class="grid-title no-border">
                    <h4>External services</h4>
                </div>
                <div class="grid-body no-border">

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
    </form>
</div>
