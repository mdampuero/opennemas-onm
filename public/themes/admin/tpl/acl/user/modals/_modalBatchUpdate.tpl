<div ng-init="(!template.backend_access || template.extra.billing.name) ? template.step = 2 : template.step = 1">
  <div ng-if="template.step != 2">
    <form name="billingForm" ng-init="template.checkPhone(template);template.checkVat(template)">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
        <h4 class="modal-title">
          {t}Billing information{/t}
        </h4>
      </div>
      <div class="modal-body">
        <p>{t}Activate the access to Backend means to add a User to your platform and has a cost of 12€/month.{/t} {t escape=off}If you have questions please have a look to our help article and/or contact us at <a href="mailto:sales@openhost.es">sales@openhost.es</a>{/t}</p>
        <p>{t}Please, fill billing information below, we will send you an invoice for all activated users at the end of the month.{/t} </p>
        <div class="row">
          <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.name.$invalid, 'has-success': billingForm.name.$dirty && billingForm.name.$valid }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.name.$dirty && billingForm.name.$valid"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.name.$invalid" uib-tooltip="{t}This field is required{/t}"></i>
              <input class="form-control" id="name" name="name" ng-model="template.extra.billing.name" placeholder="{t}Contact name{/t}" required="required" type="text">
            </div>
          </div>
          <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.company.$dirty && billingForm.company.$invalid, 'has-success': billingForm.company.$dirty && billingForm.company.$valid }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.company.$dirty && billingForm.name.$valid"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.company.$invalid" uib-tooltip="{t}This field is required{/t}"></i>
              <input class="form-control" id="company" name="company" ng-model="template.extra.billing.company" placeholder="{t}Company name{/t}" type="text">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.email.$invalid, 'has-success': billingForm.email.$dirty && billingForm.email.$valid }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.email.$dirty && billingForm.email.$valid"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.email.$invalid && billingForm.email.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.email.$invalid && billingForm.email.$error.email" uib-tooltip="{t}This is not a valid email{/t}"></i>
              <input class="form-control" id="email" name="email" ng-model="template.extra.billing.email" placeholder="{t}Email{/t}" required="required" type="email">
            </div>
          </div>
          <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.phone.$invalid || !template.validPhone, 'has-success': billingForm.phone.$dirty && billingForm.phone.$valid && template.validPhone }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.phone.$dirty && billingForm.phone.$valid && template.validPhone"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.phone.$invalid && billingForm.phone.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
              <i class="fa fa-times text-danger" ng-if="!template.validPhone" uib-tooltip="{t}This is not a valid phone{/t}"></i>
              <input class="form-control" id="phone" name="phone" ng-change="template.checkPhone(template)" ng-model="template.extra.billing.phone" placeholder="{t}Phone number{/t}" required="required" type="text">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.vat.$invalid || (billingForm.vat.$dirty && !template.validVat), 'has-success': billingForm.vat.$dirty && billingForm.vat.$valid && template.validVat }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.vat.$dirty && billingForm.vat.$valid && template.validVat"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.vat.$invalid && billingForm.vat.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.vat.$invalid && billingForm.vat.$error.vat || (billingForm.vat.$dirty && !template.validVat)" uib-tooltip="{t}This is not a valid vat{/t}"></i>
              <input class="form-control" id="vat" name="vat"  ng-change="template.checkVat(template)" ng-model="template.extra.billing.vat" placeholder="{t}VAT Number{/t}" ng-required="(template.extra.billing.company != null && template.extra.billing.company != '') || (template.extra.billing.country == 'ES' && !template.validVat)" type="text">
            </div>
          </div>
        </div>
        <h5 class="m-t-20">{t}Address{/t}</h5>
        <div class="row">
          <div class="form-group col-sm-8" ng-class="{ 'has-error': billingForm.address.$invalid, 'has-success': billingForm.address.$dirty && billingForm.address.$valid }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.address.$dirty && billingForm.address.$valid"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.address.$invalid && billingForm.address.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.address.$invalid && billingForm.address.$error.address" uib-tooltip="{t}This is not a valid address{/t}"></i>
              <input class="form-control" id="address" name="address" ng-model="template.extra.billing.address" placeholder="{t}Address{/t}" required="required" type="text">
            </div>
          </div>
          <div class="form-group col-sm-4" ng-class="{ 'has-error': billingForm.postal_code.$invalid, 'has-success': billingForm.postal_code.$dirty && billingForm.postal_code.$valid }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.postal_code.$dirty && billingForm.postal_code.$valid"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.postal_code.$invalid && billingForm.postal_code.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.postal_code.$invalid && billingForm.postal_code.$error.postal_code" uib-tooltip="{t}This is not a valid postal_code{/t}"></i>
              <input class="form-control" id="postal_code" name="postal_code" ng-model="template.extra.billing.postal_code" placeholder="{t}Postal code{/t}" required="required" type="text">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-sm-4" ng-class="{ 'has-error': billingForm.city.$invalid, 'has-success': billingForm.city.$dirty && billingForm.city.$valid }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.city.$dirty && billingForm.city.$valid"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.city.$invalid && billingForm.city.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.city.$invalid && billingForm.city.$error.city" uib-tooltip="{t}This is not a valid city{/t}"></i>
              <input class="form-control" id="city" name="city" ng-model="template.extra.billing.city" placeholder="{t}City{/t}" required="required" type="text">
            </div>
          </div>
          <div class="form-group col-sm-4" ng-class="{ 'has-error': billingForm.state.$invalid, 'has-success': billingForm.state.$dirty && billingForm.state.$valid }">
            <div class="input-with-icon right">
              <i class="fa fa-check text-success" ng-if="billingForm.state.$dirty && billingForm.state.$valid"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.state.$invalid && billingForm.state.$error.required" uib-tooltip="{t}This field is required{/t}"></i>
              <i class="fa fa-times text-danger" ng-if="billingForm.state.$invalid && billingForm.state.$error.state" uib-tooltip="{t}This is not a valid state{/t}"></i>
              <input class="form-control" id="state" name="state" ng-model="template.extra.billing.state" placeholder="{t}State{/t}" required="required" type="text">
            </div>
          </div>
          <div class="form-group col-sm-4" ng-class="{ 'has-error': billingForm.country.$invalid, 'has-success': billingForm.country.$dirty && billingForm.country.$valid }">
            <div class="input-with-icon right">
              <select class="form-control" id="country" name="country" ng-change="template.checkPhone(template);template.checkVat(template)" ng-model="template.extra.billing.country" placeholder="{t}Country{/t}" required="required">
                <option value="">{t}Select a country{/t}...</option>
                <option value="[% value %]" ng-repeat="(key,value) in template.extra.countries | orderBy" ng-selected="[% template.extra.billing.country === value %]">[% key %]</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" ng-click="template.saveBilling(template)" ng-disabled="billingForm.$invalid || !template.validPhone || !template.validVat" type="button">
          {t}Next{/t}
        </button>
      </div>
    </form>
  </div>
  <div ng-if="template.step === 2">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
      <h4 class="modal-title">
        {t}Update selected items{/t}
      </h4>
    </div>
    <div class="modal-body" ng-init="template.value == 1 && template.backend_access ? terms = false : terms = true">
      <p ng-if="template.value == 1 && template.backend_access">{t}Activate the access to Backend means to add a User to your platform and has a cost of 12€/month.{/t} {t escape=off}If you have questions please have a look to our help article and/or contact us at <a href="mailto:sales@openhost.es">sales@openhost.es</a>{/t}</p>
      <p ng-if="template.name == 'create'">{t escape=off}Are you sure you want to create a new user?{/t}</p>
      <p ng-if="template.name == 'update'">{t escape=off}Are you sure you want to update the user?{/t}</p>
      <p ng-if="template.name == 'activated' && template.value == 0">{t escape=off}Are you sure you want to disable [% template.selected.contents.length %] user(s)?{/t}</p>
      <p ng-if="template.name == 'activated' && template.value == 1">{t escape=off}Are you sure you want to enable [% template.selected.contents.length %] user(s)?{/t}</p>
      <p class="text-danger" ng-show="template.name == 'activated' && template.value == 1">

      <div class="checkbox" ng-show="template.value == 1 && template.backend_access">
        <input id="terms" name="terms" ng-model="terms" type="checkbox">
        <label for="terms">{t}I understand and accept the charges.{/t}</label>
      </div>
    </div>
    <div class="modal-footer">
      <span class="loading" ng-if="deleting == 1"></span>
      <button class="btn btn-primary" ng-click="confirm()" ng-disabled="template.backend_access && !terms" type="button">
        <span ng-show="template.name != 'create' && template.name != 'update'">{t}Yes, update all{/t}</span>
        <span ng-show="template.name == 'create'">{t}Yes, save it{/t}</span>
        <span ng-show="template.name == 'update'">{t}Yes, update it{/t}</span>
      </button>
      <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
    </div>
  </div>
</div>
