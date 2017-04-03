<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_instances_list') %]">
              <i class="fa fa-cubes"></i>
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
            <button class="btn btn-loading btn-success text-uppercase" ng-click="save();" ng-disabled="saving" ng-if="!instance.id">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
            <button class="btn btn-loading btn-success text-uppercase" ng-click="update();" ng-disabled="saving" ng-if="instance.id">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="template">
  <form name="instanceForm" novalidate>
    <div class="row">
      <div class="col-md-8">
        <div class="grid simple">
          <div class="grid-body instance-form">
            <div class="row">
              <div class="col-xlg-2 col-lg-4 col-md-5 col-sm-4" ng-if="instance.id">
                <dl ng-if="instance.id">
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
                    <span ng-if="settings.contact_ip">[% settings.contact_ip %]</span>
                    <span ng-if="!settings.contact_ip">{t}Not defined{/t}</span>
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
                    [% template.languages[settings.site_language] %]
                  </dd>
                  <dt>
                    <h5><i class="fa fa-globe"></i> {t}Time Zone{/t}</h5>
                  </dt>
                  <dd>
                    [% template.timezones[settings.time_zone] ? template.timezones[settings.time_zone] : settings.time_zone %]
                  </dd>
                </dl>
              </div>
              <div ng-class="{ 'col-xlg-10 col-lg-8 col-md-7 col-sm-8': instance.id, 'col-sm-12': !instance.id }">
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
                  <label class="form-label" for="template">{t}Template{/t}</label>
                  <div class="controls">
                    <select id="template" ng-model="instance.settings.TEMPLATE_USER">
                      <option ng-if="security.canEnable(theme.uuid)" ng-repeat="theme in template.themes | orderBy: 'name'" value="[% theme.uuid %]">[% theme.name %]</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">
                    {t}Contact mail{/t}
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && instanceForm.contact_mail.$invalid }">
                    <input class="form-control" id="contact_mail" name="contact_mail" ng-model="instance.contact_mail" required type="email">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="country">{t}Country{/t}</label>
                  <div class="controls">
                    <select class="form-control" id="country" name="country" ng-model="instance.country" placeholder="{t}Country{/t}" required>
                      <option value="">{t}Select a country{/t}...</option>
                      <option value="[% country.id %]" ng-repeat="country in template.countries" ng-selected="[% instance.country === country.id %]">[% country.name %]</option>
                    </select>
                  </div>
                </div>
                <div class="form-group" ng-if="security.hasPermission('MASTER')">
                  <label class="form-label" for="owner_id">
                    {t}Owner{/t}
                  </label>
                  <div class="controls">
                    <select id="owner_id" name="owner_id" ng-model="instance.owner_id" ng-options="value.id as value.name for value in template.users"></select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">{t}Status{/t}</label>
                  <div class="controls">
                    <div class="col-sm-6">
                      <div class="checkbox">
                        <input type="checkbox" id="activated" ng-model="instance.activated" ng-true-value="1" ng-false-value="0"  ng-checked="instance.activated == 1"/>
                        <label class="form-label" for="activated">{t}Activated{/t}</label>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="checkbox">
                        <input type="checkbox" id="blocked" ng-model="instance.blocked" ng-true-value="1" ng-false-value="0"  ng-checked="instance.blocked == 1"/>
                        <label class="form-label" for="blocked">{t}Blocked{/t}</label>
                        <div class="help m-t-5">{t}Backend access blocked for instance users{/t}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 col-xs-12">
        <div class="grid simple">
          <div class="grid-title">
            <h4>
              {t}Domains{/t}
              <span ng-show="instance.domains.length == 0">*</span>
            </h4>
          </div>
          <div class="grid-body instance-domain-list">
            <div class="form-group" ng-if="instance.domains.length > 0">
              <div class="radio">
                <input id="domain0" ng-model="instance.main_domain" type="radio" value="0">
                <label class="form-label" for="domain0">{t}No main domain{/t}</label>
              </div>
            </div>
            <div class="form-group" ng-repeat="domain in instance.domains track by $index">
              <div class="radio radio-input radio-primary">
                <input id="domain[% $index + 1 %]" ng-model="instance.main_domain" type="radio" value="[% $index + 1 %]" class="blue">
                <label class="form-label" for="domain[% $index + 1 %]">
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
            <div class="form-group">
              <div class="controls">
                <div class="input-group new-domain" ng-class="{ 'error-control': formValidated && instance.domains.length == 0 }">
                  <input class="form-control " name="new-domain" ng-model="new_domain" type="text">
                  <div class="input-group-btn">
                    <button class="btn btn-default" ng-click="addDomain();" type="button"><i class="fa fa-plus"></i> {t}Add{/t}</button>
                  </div>
                </div>
                <div class="new-domain">
                  <span class="error" ng-show="formValidated && instance.domains.length == 0">
                    <label for="new-domain" class="error">{t}Instance domains cannot be empty.{/t}</label>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group" ng-if="security.hasPermission('MASTER')">
              <label class="form-label">{t}Domain expire date:{/t}</label>
              <div class="controls">
                <input class="form-control" datetime-picker="domainPicker" ng-model="instance.domain_expire" type="text">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 col-sm-12" ng-if="security.hasPermission('CLIENT_LIST')">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Billing{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa" ng-class="{ 'fa-search': !loading, 'fa-circle-o-notch fa-spin': loading }"></i>
                </span>
                <input class="form-control" ng-model="criteria.name" placeholder="{t}Search by name or street{/t}" type="text" typeahead-on-select="selectClient($item, $model, $label)" typeahead-template-url="client" typeahead-wait-ms="500" uib-typeahead="client.id for client in getClients($viewValue)">
              </div>
            </div>
            <div ng-if="client || instance.client_id">
              <div class="row p-b-15">
                <h4>{t}Contact information{/t}</h4>
                <div class="col-sm-6">
                  <strong>{t}Name{/t}:</strong>
                  [% client.last_name%], [% client.first_name %]
                </div>
                <div class="col-sm-6" ng-if="client.company">
                  <strong>{t}Company{/t}:</strong> [% client.company %]
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-6">
                  <strong>{t}Email{/t}:</strong> [% client.email %]
                </div>
                <div class="col-sm-6">
                  <strong>{t}Phone{/t}:</strong> [% client.phone %]
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-6">
                  <strong>{t}VAT number{/t}:</strong> [% client.vat_number %]
                </div>
              </div>
              <div class="row p-b-15">
                <h4>{t}Address{/t}</h4>
                <div class="col-sm-8">
                  <strong>{t}Address{/t}:</strong> [% client.address %]
                </div>
                <div class="col-sm-4">
                  <strong>{t}Postal code{/t}:</strong> [% client.postal_code %]
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-4">
                  <strong>{t}City{/t}:</strong> [% client.city %]
                </div>
                <div class="col-sm-4">
                  <strong>{t}State{/t}:</strong> [% client.state %]
                </div>
                <div class="col-sm-4">
                  <strong>{t}Country{/t}:</strong> [% template.countries[client.country] %]
                </div>
              </div>
              <div class="row p-t-15">
                <div class="col-sm-4 col-sm-offset-4">
                  <button class="btn btn-danger btn-block" ng-click="instance.client = null">
                    <h4 class="text-white">
                      <i class="fa fa-times"></i>
                      {t}Remove{/t}
                    </h4>
                  </button>
                </div>
              </div>
            </div>
            <div class="m-b-20 p-b-100 p-t-50 text-center" ng-if="!client && !client_id">
              <i class="fa fa-search fa-4x m-t-30"></i>
              <h3>{t}There is no client linked to this instance{/t}</h3>
              <h4>{t}Try to search a client by name or street{/t}</h4>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Last purchases{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="ng-cloak" ng-if="!template.purchases || template.purchases.length === 0">
              <div class="p-t-100 p-b-100 text-center">
                <i class="fa fa-stack fa-3x m-t-20">
                  <i class="fa fa-shopping-cart fa-stack-1x"></i>
                  <i class="fa fa-ban fa-stack-2x"></i>
                </i>
                <h3 class="m-b-50">{t}There are no purchases for now.{/t}</h3>
              </div>
            </div>
            <div class="table-wrapper ng-cloak" ng-if="template.purchases && template.purchases.length > 0">
              <table class="table">
                <thead>
                  <tr>
                    <th class="pointer" width="100">
                      {t}Date{/t}
                    </th>
                    <th class="pointer text-left">
                      {t}Description{/t}
                    </th>
                    <th class="text-center" width="100">
                      {t}Method{/t}
                    </th>
                    <th class="text-right pointer" width="120">
                      {t}Total{/t}
                    </th>
                    <th width="150">
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="item in template.purchases">
                    <td>
                      [% item.updated | moment : 'YYYY-MM-DD' : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </td>
                    <td class="text-left">
                      <div style="max-width: 350px; overflow: hidden; text-overflow: ellipsis;">
                        <span ng-repeat="i in item.details">[% i.description + ($index === item.details.length - 1 ? '' : ', ') %]</span>
                      </div>
                    </td>
                    <td class="text-center">
                      <i class="fa" ng-class="{ 'fa-paypal': item.method === 'PayPalAccount', 'fa-credit-card': item.method === 'CreditCard' }" ng-if="item.total !== 0 && item.method"></i>
                      <span ng-if="item.total === 0">-</span>
                    </td>
                    <td class="text-right">
                      [% item.total | number : 2 %] €
                    </td>
                    <td>
                      <a ng-href="[% routing.ngGenerate('manager_purchase_show', { id: item.id }) %]" title="{t}Show{/t}">
                        {t}View purchase{/t}
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="text-center" ng-if="template.purchases.length > 0">
                <a class="bold text-uppercase" ng-href="[% routing.ngGenerate('manager_purchases_list', { oql: 'instance_id=' + instance.id + ' limit 25' }) %]">{t}More{/t}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Modules{/t}</h4>
          </div>
          <div class="grid-body no-padding">
            <div class="p-l-15 p-r-15 p-t-15">
              <div class="checkbox check-default check-title">
                <input id="checkbox-all" ng-model="selected.all" ng-change="toggleAll()" ng-disabled="!security.canEnable('BASIC_PACK') || !security.canEnable('PROFESSIONAL_PACK') || !security.canEnable('ADVANCED_PACK') || !security.canEnable('EXPERT_PACK')" type="checkbox">
                <label for="checkbox-all">
                  <h5>{t}Select all{/t}</h5>
                </label>
              </div>
            </div>
            <div class="table-wrapper p-b-15 p-l-15 p-r-15">
              <div class="instance-plan" ng-repeat="puuid in packs">
                <div class="checkbox check-default check-title">
                  <input id="checkbox-[% puuid %]" ng-model="selected.plan[puuid]" ng-change="togglePlan(puuid)" ng-disabled="!security.canEnable(puuid)" type="checkbox">
                  <label for="checkbox-[% puuid %]">
                    <h5>[% template.modules[map[puuid]] ? template.modules[map[puuid]].name : '{t}Other{/t}' %]</h5>
                  </label>
                </div>
                <div class="m-b-5" ng-repeat="muuid in modulesByPack[puuid]">
                  <div class="checkbox check-default">
                    <input id="checkbox-[% muuid %]" checklist-model="instance.activated_modules" checklist-value="muuid" ng-disabled="!security.canEnable(puuid) && !security.canEnable(muuid)" type="checkbox">
                    <label for="checkbox-[% muuid %]">
                      [% template.modules[map[muuid]].name %]
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Themes{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="row">
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 m-b-5" ng-repeat="theme in template.themes | orderBy: 'name'">
                <div class="checkbox check-default">
                  <input id="checkbox-[% theme.uuid %]" checklist-model="instance.purchased" checklist-value="theme.uuid" ng-click="toggleChanges(theme)" ng-disabled="!security.canEnable(theme.uuid)" type="checkbox">
                  <label for="checkbox-[% theme.uuid %]">
                    [% theme.name %]
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-12" ng-show="security.hasPermission('MASTER')">
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
              <label class="form-label" for="template">{t}Minimum password level{/t}</label>
              <div class="controls">
                <select ng-model="settings.pass_level">
                  <option value="-1">{t}Default{/t}</option>
                  <option value="0">{t}Weak{/t}</option>
                  <option value="1">{t}Good{/t}</option>
                  <option value="2">{t}Strong{/t}</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="max-mailing" class="form-label">{t}Maximun number of emails sent by month{/t}</label>
              <div class="controls">
                <input id="max-mailing" ng-model="settings.max_mailing" type="text">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" ng-if="security.hasPermission('MASTER')">
        <div class="grid simple">
          <div class="grid-title">
            <h4>External services</h4>
          </div>
          <div class="grid-body">
            <div class="form-group">
              <label class="form-label" for="piwik-page-id">{t}Piwik Statistics{/t} - {t}Page ID:{/t}</label>
              <div class="controls">
                <input class="form-control" id="piwik-page-id" ng-model="settings.piwik.page_id" type="text">
                <div class="help-block">
                  {t escape=off}You can get your Piwik Site information from <a href="https://piwik.openhost.es/admin">our Piwik server</a>.{/t}
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="piwik-server-url">{t}Piwik Statistics{/t} - {t}Server url{/t}</label>
              <div class="controls">
                <input class="form-control" id="piwik-server-url" ng-model="settings.piwik.server_url" type="text">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script type="text/ng-template" id="client">
  <a>
    [% match.model.last_name %], [% match.model.first_name %] ([% match.model.email %])
  </a>
</script>
