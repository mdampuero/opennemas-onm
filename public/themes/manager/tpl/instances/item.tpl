<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <a ng-href="[% routing.ngGenerate('manager_instances_list') %]">
                            <i class="fa fa-cubes fa-lg"></i>
                            {t}Instances{/t}
                        </a>
                    </h4>
                </li>
                <li class="quicklinks seperate">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <h5>
                        <span ng-if="!instance.id">{t}New instance{/t}</span>
                        <span ng-if="instance.id">{t}Edit instance{/t}</span>
                    </h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_instances_list') %]">
                            <i class="fa fa-reply"></i>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <button class="btn btn-primary" ng-click="save();" ng-disabled="saving" ng-if="!instance.id">
                            <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                        </button>
                        <button class="btn btn-primary" ng-click="update();" ng-disabled="saving" ng-if="instance.id">
                            <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <form name="instanceForm" novalidate>
        <div class="row">
            <div class="col-sm-12">
                <div class="grid simple">
                    <div class="grid-title">
                        <h4>
                            <span class="semi-bold" ng-if="instance.id">
                                [% instance.name %]
                            </span>
                            <span class="semi-bold" ng-if="!instance.id">
                                {t}New instance{/t}
                            </span>
                        </h4>
                    </div>
                    <div class="grid-body instance-form">
                        <div class="row">
                            <div ng-class="{ 'col-sm-4': instance.id, 'col-sm-12': !instance.id }" ng-if="instance.id">
                                <dl ng-if="instance.id">
                                    <dt>
                                        <h5><i class="fa fa-user"></i> {t}Owner email{/t}</h5>
                                    </dt>
                                    <dd>
                                        <a href="mailto:[% instance.contact_mail %]">[% instance.contact_mail %]</a>
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
                                    <dt>
                                        <h5><i class="fa fa-flag-checkered"></i> {t}Language{/t}</h5>
                                    </dt>
                                    <dd>
                                        [% template.languages[instance.external.site_language] %]
                                    </dd>
                                    <dt>
                                        <h5><i class="fa fa-globe"></i> {t}Time Zone{/t}</h5>
                                    </dt>
                                    <dd>
                                        [% template.timezones[instance.external.time_zone] %]
                                    </dd>
                                </dl>
                            </div>
                            <div ng-class="{ 'col-sm-8': instance.id, 'col-sm-12': !instance.id }">
                                <div class="form-group">
                                    <label class="form-label">
                                        {t}Site name{/t}
                                        <span ng-show="instanceForm.name.$invalid">*</span>
                                    </label>
                                    <span class="help">{t}(Human readable name){/t}</span>
                                    <div class="controls" ng-class="{ 'error-control': formValidated && instanceForm.name.$invalid }">
                                        <input class="form-control" id="name" name="name" ng-model="instance.name" required type="text">
                                    </div>
                                    <span class="error" ng-show="formValidated && instanceForm.name.$invalid">
                                        <label for="name" class="error">{t}This field is required{/t}</label>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="template" class="form-label">{t}Template{/t}</label>
                                    <div class="controls">
                                        <select id="template" ng-model="instance.settings.TEMPLATE_USER" ng-options="key as value.name for (key,value) in template.templates"></select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        {t}Contact mail{/t}
                                    </label>
                                    <div class="controls" ng-class="{ 'error-control': formValidated && instanceForm.contact_mail.$invalid }">
                                        <input class="form-control" id="contact_mail" name="contact_mail" ng-model="instance.contact_mail" required type="text">
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
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="grid simple">
                    <div class="grid-title">
                        <h4>
                            {t}Domains{/t}
                            <span ng-show="instance.domains.length == 0">*</span>
                        </h4>
                    </div>
                    <div class="grid-body instance-domain-list">
                        <div class="row form-group" ng-if="instance.domains.length > 0">
                            <div class="col-sm-12">
                                <div class="radio">
                                    <input id="domain0" ng-model="instance.main_domain" type="radio" value="0">
                                    <label for="domain0">{t}No main domain{/t}</label>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group" ng-repeat="domain in instance.domains track by $index">
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
                                <div class="input-group new-domain" ng-class="{ 'error-control': formValidated && instance.domains.length == 0 }">
                                    <input class="form-control " name="new-domain" ng-model="new_domain" type="text">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" ng-click="addDomain();" type="button">{t}Add{/t}</button>
                                    </div>
                                </div>
                                <div class="new-domain">
                                    <span class="error" ng-show="formValidated && instance.domains.length == 0">
                                        <label for="new-domain" class="error">{t}Instance domains cannot be empty.{/t}</label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{t}Domain expire date:{/t}</label>
                            <div class="controls">
                                <quick-datepicker icon-class="fa fa-clock-o" ng-model="instance.domain_expire" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="grid simple">
                    <div class="grid-title">
                        <h4>{t}Billing{/t}</h4>
                    </div>
                    <div class="grid-body">
                        <div class="form-group">
                            <label class="form-label">{t}Last invoice date{/t}</label>
                            <div class="controls">
                                <quick-datepicker icon-class="fa fa-clock-o" ng-model="instance.external.last_invoice" placeholder="{t}Click to set date{/t}"></quick-datepicker>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="grid simple">
                    <div class="grid-title">
                        <h4>
                            {t}Support{/t}
                            <i>
                                <a href="http://help.opennemas.com/knowledgebase/articles/463594-precios-opennemas-servicio-de-desarrollo" target="_blank">
                                    &nbsp;&nbsp;+ info
                                </a>
                            </i>
                        </h4>
                    </div>
                    <div class="grid-body support-list">
                        <div class="form-group">
                            <label class="form-label">{t}Development plan{/t}</label>
                            <div class="controls" ng-init="initializeSupportPlan()">
                                <div class="radio col-sm-6" ng-repeat="support in template.available_modules|filter:{ plan : 'Support'}" >
                                    <input id="[% support.id %]" ng-change="updateSupport(support.id)" ng-model="instance.support_plan" type="radio" value="[% support.id %]">
                                    <label for="[% support.id %]">
                                        [% support.name %]
                                    </label>
                                    <span class="help muted" ng-if="support.description">( [% support.description %] )</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="grid simple">
                    <div class="grid-title">
                        <h4>{t}Modules{/t}</h4>
                    </div>
                    <div class="grid-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox check-default check-title">
                                    <button class="btn" ng-click="selectAll()">{t}Select all{/t}</button>
                                </div>
                            </div>
                        </div>
                        <div class="row" ng-repeat="planName in template.plans">
                            <div class="col-sm-12 instance-plan-block">
                              <div class="col-sm-12 m-b-10 m-t-10">
                                <div class="checkbox check-default check-title col-sm-12">
                                    <input id="checkbox-[% planName %]" ng-model="selected.plan[planName]" ng-change="togglePlan(planName)" ng-checked="isPlanSelected(planName)" type="checkbox">
                                    <label for="checkbox-[% planName %]">
                                        <h5>Plan [% planName %]</h5>
                                    </label>
                                </div>
                              </div>
                              <div class="col-sm-4 m-b-5" ng-repeat="module in template.available_modules|filter:{ plan : planName}">
                                <div class="checkbox check-default">
                                    <input id="checkbox-[% module.id %]" ng-click="toggleChanges(module)" checklist-model="instance.activated_modules" checklist-value="module.id" type="checkbox">
                                    <label for="checkbox-[% module.id %]">
                                        [% module.name %]
                                        <span class="text-error" ng-if="instance.changes_in_modules.indexOf(module.id) != -1 && instance.activated_modules.indexOf(module.id) == -1 ">
                                            ({t}pending activation{/t})
                                        </span>
                                        <span class="text-error" ng-if="instance.changes_in_modules.indexOf(module.id) != -1 && instance.activated_modules.indexOf(module.id) != -1 ">
                                            ({t}pending deactivation{/t})
                                        </span>
                                    </label>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="grid simple">
                    <div class="grid-title">
                        <h4>{t}Internal settings{/t}</h4>
                    </div>
                    <div class="grid-body">
                        <div class="form-group">
                            <label class="form-label">{t}Internal name{/t}</label>
                            <span class="help">{t}Alphanumeric, without spaces{/t}</span>
                            <div class="controls" ng-class="{ 'error-control': formValidated && instanceForm.internal_name.$invalid }">
                                <input class="form-control" id="internal_name" name="internal_name" ng-model="instance.internal_name" ng-required="instance.id" type="text">
                            </div>
                            <span class="error" ng-show="formValidated && instanceForm.internal_name.$invalid">
                                <label for="internal_name" class="error">{t}This field is required{/t}</label>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="template">{t}Database{/t}</label>
                            <div class="controls" ng-class="{ 'error-control': formValidated && instanceForm.database.$invalid }">
                                <input class="form-control" id="database" name="database" ng-model="instance.settings.BD_DATABASE" ng-required="instance.id" type="text">
                            </div>
                            <span class="error" ng-show="formValidated && instanceForm.database.$invalid">
                                <label for="database" class="error">{t}This field is required{/t}</label>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="activated">{t}Maximun activated users{/t}</label>
                            <span class="help">{t}0 for unlimited users{/t}</span>
                            <div class="controls">
                                <input type="number" id="max_users" ng-model="instance.external.max_users" min="0">
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
                            <label for="max-mailing" class="form-label">{t}Maximun number of emails sent by month{/t}</label>
                            <div class="controls">
                                <input type="number" id="max-mailing" ng-model="instance.external.max_mailing">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="grid simple">
                    <div class="grid-title">
                        <h4>External services</h4>
                    </div>
                    <div class="grid-body">
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
