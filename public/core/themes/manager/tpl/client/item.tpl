<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_clients_list') %]">
              <i class="fa fa-user"></i>
              {t}Clients{/t}
            </a>
          </h4>
        </li>
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            <span ng-if="!client.id">{t}New client{/t}</span>
            <span ng-if="client.id">{t}Edit client{/t}</span>
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_clients_list') %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-loading btn-success text-uppercase" ng-click="!client.id ? save() : update()" ng-disabled="clientForm.$invalid || saving">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="extra">
  <form name="clientForm" novalidate>
    <div class="grid simple">
      <div class="grid-body client-form">
        <div class="row" ng-if="security.hasPermission('MASTER')">
          <div class="form-group col-lg-4 col-sm-6">
            <h4>{t}Owner{/t}</h4>
            <div class="controls">
              <select ng-model="client.owner_id" ng-options="value.id as value.name for value in extra.users"></select>
              <label class="help m-t-5">{t}Partner who can see this client information{/t}</label>
            </div>
          </div>
          <div class="col-lg-6 col-lg-offset-2 col-sm-6" ng-if="client.id">
            <h4>{t}View on{/t}</h4>
            <div class="row">
              <div class="col-sm-6">
                <a class="btn btn-block btn-white text-uppercase" ng-href="[% extra.braintree.url %]/merchants/[% extra.braintree.merchant_id %]/customers/[% client.id %]" target="_blank">
                  <strong>Braintree</strong>
                </a>
              </div>
              <div class="col-sm-6">
                <a class="btn btn-block btn-white text-uppercase text-success" ng-href="[% extra.freshbooks.url %]/showUser?userid=[% client.id %]" target="_blank">
                  <strong>Freshbooks</strong>
                </a>
              </div>
            </div>
          </div>
        </div>
        <h4>{t}Contact information{/t}</h4>
        <div class="row">
          <div class="form-group col-lg-4 col-sm-6">
            <label class="form-label" for="first-name">{t}First name{/t}</label>
            <div class="controls input-with-icon right">
              <input class="form-control" id="first-name" name="first_name" ng-model="client.first_name" placeholder="{t}First name{/t}" required type="text">
              <span class="icon right">
                <span class="fa fa-check text-success" ng-if="clientForm.first_name.$dirty && clientForm.first_name.$valid"></span>
                <span class="fa fa-asterisk" ng-if="!clientForm.first_name.$dirty && clientForm.first_name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="clientForm.first_name.$dirty && clientForm.first_name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
            </div>
          </div>
          <div class="form-group col-lg-4 col-sm-6">
            <label class="form-label" for="last-name">{t}Last name{/t}</label>
            <div class="controls input-with-icon right">
              <input class="form-control" id="last-name" name="last_name" ng-model="client.last_name" placeholder="{t}Last name{/t}" required type="text">
              <span class="icon right">
                <span class="fa fa-check text-success" ng-if="clientForm.last_name.$dirty && clientForm.last_name.$valid"></span>
                <span class="fa fa-asterisk" ng-if="!clientForm.last_name.$dirty && clientForm.last_name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="clientForm.last_name.$dirty && clientForm.last_name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
            </div>
          </div>
          <div class="form-group col-lg-4 col-sm-6">
            <label class="form-label" for="company">{t}Company{/t}</label>
            <div class="controls">
              <input class="form-control" id="company" name="company" ng-model="client.company" placeholder="{t}Company{/t}" type="text">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-lg-4 col-sm-6">
            <label class="form-label" for="vat-number">{t}VAT No.{/t}</label>
            <div class="controls">
              <input class="form-control" id="vat-number" name="vat-number" ng-model="client.vat_number" placeholder="{t}VAT No.{/t}" type="text">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-lg-4">
            <label class="form-label" for="email">{t}Email{/t}</label>
            <div class="controls input-with-icon right">
              <input class="form-control" id="email" name="email" ng-model="client.email" ng-pattern="emailPattern" placeholder="{t}Email{/t}" required type="email">
              <span class="icon right">
                <span class="fa fa-check text-success" ng-if="clientForm.email.$dirty && clientForm.email.$valid"></span>
                <span class="fa fa-asterisk" ng-if="!clientForm.email.$dirty && clientForm.email.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="clientForm.email.$dirty && clientForm.email.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
            </div>
          </div>
          <div class="form-group col-lg-4 col-sm-6">
            <label class="form-label" for="phone">{t}Phone{/t}</label>
            <div class="controls">
              <input class="form-control" id="phone" name="phone" ng-model="client.phone" placeholder="{t}Phone{/t}" type="tel">
            </div>
          </div>
        </div>
        <h4>{t}Address{/t}</h4>
        <div class="row">
          <div class="form-group col-sm-9">
            <label class="form-label" for="address">{t}Address{/t}</label>
            <div class="controls input-with-icon right">
              <input class="form-control" id="address" name="address" ng-model="client.address" placeholder="{t}Address{/t}" required type="text">
              <span class="icon right">
                <span class="fa fa-check text-success" ng-if="clientForm.address.$dirty && clientForm.address.$valid"></span>
                <span class="fa fa-asterisk" ng-if="!clientForm.address.$dirty && clientForm.address.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="clientForm.address.$dirty && clientForm.address.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
            </div>
          </div>
          <div class="form-group col-sm-3">
            <label class="form-label" for="postal-code">{t}Postal code{/t}</label>
            <div class="controls input-with-icon right">
              <input class="form-control" id="postal-code" name="postal_code" ng-model="client.postal_code" placeholder="{t}Postal code{/t}" required type="text">
              <span class="icon right">
                <span class="fa fa-check text-success" ng-if="clientForm.postal_code.$dirty && clientForm.postal_code.$valid"></span>
                <span class="fa fa-asterisk" ng-if="!clientForm.postal_code.$dirty && clientForm.postal_code.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="clientForm.postal_code.$dirty && clientForm.postal_code.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-sm-6">
            <label class="form-label" for="city">{t}City{/t}*</label>
            <div class="controls input-with-icon right">
              <input class="form-control" id="city" name="city" ng-model="client.city" placeholder="{t}City{/t}" required type="text">
              <span class="icon right">
                <span class="fa fa-check text-success" ng-if="clientForm.city.$dirty && clientForm.city.$valid"></span>
                <span class="fa fa-asterisk" ng-if="!clientForm.city.$dirty && clientForm.city.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="clientForm.city.$dirty && clientForm.city.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
            </div>
          </div>
          <div class="form-group col-sm-6">
            <label class="form-label" for="state">{t}State{/t}</label>
            <div class="controls input-with-icon">
              <input class="form-control no-animate" id="state" name="state" ng-if="client.country !== 'ES'" ng-model="client.state" placeholder="{t}State{/t}" required="required" type="text">
              <select class="form-control no-animate" id="state" name="state" ng-if="client.country === 'ES'" ng-model="client.state">
                <option value="">{t}Select a province{/t}...</option>
                <option ng-repeat="province in extra.provinces" value="[% province %]">[% province %]</option>
              </select>
              <span class="icon right">
                <span class="fa fa-check text-success" ng-if="clientForm.state.$dirty && clientForm.state.$valid"></span>
                <span class="fa fa-asterisk" ng-if="!clientForm.state.$dirty && clientForm.state.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="clientForm.state.$dirty && clientForm.state.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
            </div>
          </div>
          <div class="form-group col-sm-6">
            <labelclass="form-label" for="country">{t}Country{/t}</label>
          <div class="controls">
            <select class="form-control" id="country" name="country" ng-model="client.country" placeholder="{t}Country{/t}" required>
              <option value="">{t}Select a country{/t}...</option>
              <option value="[% key %]" ng-repeat="(key,value) in extra.countries" ng-selected="[% client.country === value %]">[% value %]</option>
            </select>
          </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
