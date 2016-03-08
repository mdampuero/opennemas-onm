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
            <button class="btn btn-loading btn-success text-uppercase" ng-click="!client.id ? save() : update()" ng-disabled="saving">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <form name="clientForm" novalidate>
    <div class="grid simple">
      <div class="grid-body client-form">
        <h4>{t}Contact information{/t}</h4>
        <div class="row">
          <div class="form-group col-lg-4 col-sm-6">
            <label class="form-label" for="first-name">{t}First name{/t}</label>
            <div class="controls">
              <input class="form-control" id="first-name" name="first-name" ng-model="client.first_name" placeholder="{t}First name{/t}" required="required" type="text">
            </div>
          </div>
          <div class="form-group col-lg-4 col-sm-6">
            <label class="form-label" for="last-name">{t}Last name{/t}</label>
            <div class="controls">
              <input class="form-control" id="last-name" name="last-name" ng-model="client.last_name" placeholder="{t}Last name{/t}" type="text">
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
            <div class="controls">
              <input class="form-control" id="email" name="email" ng-model="client.email" placeholder="{t}Email{/t}" type="text">
            </div>
          </div>
          <div class="form-group col-lg-4 col-sm-6">
            <label class="form-label" for="phone">{t}Phone{/t}</label>
            <div class="controls">
              <input class="form-control" id="phone" name="phone" ng-model="client.phone" placeholder="{t}Phone{/t}" type="text">
            </div>
          </div>
        </div>
        <h4>{t}Address{/t}</h4>
        <div class="row">
          <div class="form-group col-sm-9">
            <label class="form-label" for="address">{t}Address{/t}</label>
            <div class="controls">
              <input class="form-control" id="address" name="address" ng-model="client.address" placeholder="{t}Address{/t}" type="text">
            </div>
          </div>
          <div class="form-group col-sm-3">
            <label class="form-label" for="postal-code">{t}Postal code{/t}</label>
            <div class="controls">
              <input class="form-control" id="postal-code" name="postal-code" ng-model="client.postal_code" placeholder="{t}Postal code{/t}" type="text">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-sm-6">
            <label class="form-label" for="city">{t}City{/t}</label>
            <div class="controls">
              <input class="form-control" id="city" name="city" ng-model="client.city" placeholder="{t}City{/t}" type="text">
            </div>
          </div>
          <div class="form-group col-sm-6">
            <label class="form-label" for="state">{t}State{/t}</label>
            <div class="controls">
              <input class="form-control" id="state" name="state" ng-model="client.state" placeholder="{t}State{/t}" type="text">
            </div>
          </div>
          <div class="form-group col-sm-6">
            <labelclass="form-label" for="country">{t}Country{/t}</label>
            <div class="controls">
              <select class="form-control" id="country" name="country" ng-model="client.country"placeholder="{t}Country{/t}">
                <option value="">{t}Select a country{/t}...</option>
                <option value="[% key %]" ng-repeat="(key,value) in extra.countries" ng-selected="[% billing.country === value %]">[% value %]</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
