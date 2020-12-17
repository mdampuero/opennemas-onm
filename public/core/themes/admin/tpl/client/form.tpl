<form name="clientForm" ng-controller="ClientCtrl">
  <div class="row" ng-show="!client.country">
    <h5>{t}Where are you from?{/t}</h5>
    <div class="form-group col-sm-4">
      <div class="input-with-icon right">
        <select class="form-control" id="country" name="country" ng-model="client.country" placeholder="{t}Country{/t}" required>
          <option value="">{t}Select a country{/t}...</option>
          <option value="[% key %]" ng-repeat="(key,value) in countries" ng-selected="[% client.country === key %]">[% value %]</option>
        </select>
      </div>
    </div>
  </div>
  <div class="ng-cloak" ng-show="client.country">
    <h5 class="m-t-20">{t}Contact information{/t}</h5>
    <div class="row">
      <div class="form-group col-sm-4" ng-class="{ 'has-error': clientForm.first_name.$dirty && clientForm.first_name.$invalid, 'has-success': clientForm.first_name.$dirty && clientForm.first_name.$valid }">
        <div class="input-with-icon right">
          <i class="fa fa-check text-success" ng-if="clientForm.first_name.$dirty && clientForm.first_name.$valid"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.first_name.$dirty && clientForm.first_name.$invalid" uib-tooltip="{t}This field is required{/t}"></i>
          <input class="form-control" id="first_name" name="first_name" ng-model="client.first_name" placeholder="{t}First name{/t}" required type="text">
        </div>
      </div>
      <div class="form-group col-sm-4" ng-class="{ 'has-error': clientForm.last_name.$dirty && clientForm.last_name.$invalid, 'has-success': clientForm.last_name.$dirty && clientForm.last_name.$valid }">
        <div class="input-with-icon right">
          <i class="fa fa-check text-success" ng-if="clientForm.last_name.$dirty && clientForm.last_name.$valid"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.last_name.$dirty && clientForm.last_name.$invalid" uib-tooltip="{t}This field is required{/t}"></i>
          <input class="form-control" id="last_name" name="last_name" ng-model="client.last_name" placeholder="{t}Last name{/t}" required type="text">
        </div>
      </div>
      <div class="form-group col-sm-4">
        <div class="input-with-icon right">
          <input class="form-control" id="company" name="company" ng-model="client.company" placeholder="{t}Company name{/t}" type="text">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-sm-6" ng-class="{ 'has-error': clientForm.email.$dirty && clientForm.email.$invalid, 'has-success': clientForm.email.$dirty && clientForm.email.$valid }">
        <div class="input-with-icon right">
          <i class="fa fa-check text-success" ng-if="clientForm.email.$dirty && clientForm.email.$valid"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.email.$dirty && clientForm.email.$invalid && clientForm.email.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.email.$dirty && clientForm.email.$invalid && !clientForm.email.$error.required" uib-tooltip="{t}This is not a valid email{/t}"></i>
          <input class="form-control" id="email" name="email" ng-model="client.email" ng-pattern="emailPattern" placeholder="{t}Email{/t}" required type="email">
        </div>
      </div>
      <div class="form-group col-sm-6">
        <div class="input-with-icon right">
          <input class="form-control" id="phone" name="phone" ng-model="client.phone" placeholder="{t}Phone number{/t}" type="text">
        </div>
      </div>
    </div>
    <div class="row" ng-if="isVatNumberRequired()">
      <div class="form-group col-sm-6" ng-class="{ 'has-error': clientForm.vat_number.$invalid || (clientForm.vat_number.$dirty && !validVatNuber), 'has-success': clientForm.vat_number.$dirty && clientForm.vat_number.$valid && validVatNumber }">
        <div class="input-with-icon right">
          <i class="fa fa-check text-success" ng-if="clientForm.vat_number.$dirty && clientForm.vat_number.$valid && validvat_number"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.vat_number.$invalid && clientForm.vat_number.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.vat_number.$invalid && clientForm.vat_number.$error.vat_number || (clientForm.vat_number.$dirty && !validVatNumber)" uib-tooltip="{t}This is not a valid vat_number identification number{/t}"></i>
          <input class="form-control" id="vat_number" name="vat_number" ng-model="client.vat_number" placeholder="{t}Vat identification number{/t}" ng-required="isVatNumberRequired()" type="text">
        </div>
        <div class="help m-t-5">
          <a href="https://en.wikipedia.org/wiki/VAT_identification_number" target="_blank">{t}What is a VAT identification number?{/t}</a>
        </div>
      </div>
    </div>
    <h5 class="m-t-20">{t}Address{/t}</h5>
    <div class="row">
      <div class="form-group col-sm-8" ng-class="{ 'has-error': clientForm.address.$dirty && clientForm.address.$invalid, 'has-success': clientForm.address.$dirty && clientForm.address.$valid }">
        <div class="input-with-icon right">
          <i class="fa fa-check text-success" ng-if="clientForm.address.$dirty && clientForm.address.$valid"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.address.$dirty && clientForm.address.$invalid && clientForm.address.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.address.$dirty && clientForm.address.$invalid && clientForm.address.$error.address" uib-tooltip="{t}This is not a valid address{/t}"></i>
          <input class="form-control" id="address" name="address" ng-model="client.address" placeholder="{t}Address{/t}" required type="text">
        </div>
      </div>
      <div class="form-group col-sm-4" ng-class="{ 'has-error': clientForm.postal_code.$dirty && clientForm.postal_code.$invalid, 'has-success': clientForm.postal_code.$dirty && clientForm.postal_code.$valid }">
        <div class="input-with-icon right">
          <i class="fa fa-check text-success" ng-if="clientForm.postal_code.$dirty && clientForm.postal_code.$valid"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.postal_code.$dirty && clientForm.postal_code.$invalid && clientForm.postal_code.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.postal_code.$dirty && clientForm.postal_code.$invalid && clientForm.postal_code.$error.postal_code" uib-tooltip="{t}This is not a valid postal_code{/t}"></i>
          <input class="form-control" id="postal_code" name="postal_code" ng-model="client.postal_code" placeholder="{t}Postal code{/t}" required type="text">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-sm-4" ng-class="{ 'has-error': clientForm.city.$dirty && clientForm.city.$invalid, 'has-success': clientForm.city.$dirty && clientForm.city.$valid }">
        <div class="input-with-icon right">
          <i class="fa fa-check text-success" ng-if="clientForm.city.$dirty && clientForm.city.$valid"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.city.$dirty && clientForm.city.$invalid && clientForm.city.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.city.$dirty && clientForm.city.$invalid && clientForm.city.$error.city" uib-tooltip="{t}This is not a valid city{/t}"></i>
          <input class="form-control" id="city" name="city" ng-model="client.city" placeholder="{t}City{/t}" required type="text">
        </div>
      </div>
      <div class="form-group col-sm-4" ng-class="{ 'has-error': clientForm.state.$dirty && clientForm.state.$invalid, 'has-success': clientForm.state.$dirty && clientForm.state.$valid }">
        <div class="input-with-icon right">
          <i class="fa fa-check text-success" ng-if="clientForm.state.$dirty && clientForm.state.$valid"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.state.$dirty && clientForm.state.$invalid && clientForm.state.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
          <i class="fa fa-times text-danger" ng-if="clientForm.state.$dirty && clientForm.state.$invalid && clientForm.state.$error.state" uib-tooltip="{t}This is not a valid state{/t}"></i>
          <input class="form-control no-animate" id="state" name="state" ng-if="client.country !== 'ES'" ng-model="client.state" placeholder="{t}State/Province{/t}" required type="text">
          <select class="form-control no-animate" id="state" name="state" ng-if="client.country === 'ES'" ng-model="client.state">
            <option value="">{t}Select a province{/t}...</option>
            <option ng-repeat="province in provinces" value="[% province %]">[% province %]</option>
          </select>
        </div>
      </div>
      <div class="form-group col-sm-4" ng-class="{ 'has-error': clientForm.country.$invalid, 'has-success': clientForm.country.$dirty && clientForm.country.$valid }">
        <div class="input-with-icon right">
          <select class="form-control" id="country" name="country" ng-model="client.country" placeholder="{t}Country{/t}" required>
            <option value="">{t}Select a country{/t}...</option>
            <option value="[% key %]" ng-repeat="(key,value) in countries" ng-selected="[% client.country === key %]">[% value %]</option>
          </select>
        </div>
      </div>
    </div>
    {if !$modal}
      <div class="row m-t-50 ng-cloak">
        <div class="col-sm-4 m-t-15">
          <button class="btn btn-block btn-loading btn-white" ng-click="previous()" ng-disabled="loading">
            <h4 class="text-uppercase">
              {t}Previous{/t}
            </h4>
          </button>
        </div>
        <div class="col-sm-4 col-sm-offset-4 m-t-15">
          <button class="btn btn-block btn-loading btn-success" ng-click="confirm()" ng-disabled="clientForm.$invalid || !validVatNumber || loading">
            <i class="fa fa-circle-o-notch fa-spin m-t-15 pull-left" ng-if="loading"></i>
            <h4 class="text-uppercase text-white">
              {t}Next{/t}
            </h4>
          </button>
        </div>
      </div>
    {else}
      <div class="row">
        <div class="col-xs-6 col-xs-offset-6">
          <button class="btn btn-block btn-loading btn-success" ng-click="confirm()" ng-disabled="clientForm.$invalid || !validVatNumber || loading">
            <h4 class="bold text-uppercase text-white">
              <i class="fa fa-circle-o-notch fa-spin" ng-if="loading"></i>
              {t}Next{/t}
            </h4>
          </button>
        </div>
      </div>
    {/if}
  </div>
</form>
