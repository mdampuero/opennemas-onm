{extends file="domain_management/list.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_market.less,
    @AdminTheme/less/_domain.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="content"}
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-indent fa-server fa-lg"></i>
              {t}Domain Mapping{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_domain_management}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content" ng-controller="DomainManagementCtrl" ng-init="{if !empty($billing)}billing = {json_encode($billing)|clear_json}; {/if}{if $create}create = 1;{/if}countries = {json_encode($countries)|clear_json}; vatTax = {$vatTax}">
    <div class="row">
      <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
        <div class="grid simple">
          <div class="grid-body clearfix">
            <div ng-show="step != 4">
              <h4 class="semi-bold">1. {t}Domains{/t}</h4>
              <p>
                {if !$create}
                  {t}I have an existing domain and I want to redirect it to my Opennemas digital newspaper.{/t}
                {else}
                  {t}I do not have my own domain and I want to create one and redirect it to my Opennemas digital newspaper{/t}
                {/if}
              </p>
              <div class="clearfix">
                <div class="input-group pull-left" style="width:80%;">
                  <span class="input-group-addon">www</span>
                  <input autofocus class="form-control" ng-model="domain" placeholder="{t}Enter a domain{/t}" type="text"  ng-keyup="mapByKeyPress($event)">
                  <span class="input-group-btn">
                    <span class="arrow"></span>
                    <button class="btn btn-success" ng-click="map()" ng-disabled="!isValid()">
                      <span ng-if="!loading">
                        {t}Map it{/t}
                      </span>
                      <div class="sk-three-bounce sk-inline sk-small ng-cloak" ng-if="loading">
                        <div class="sk-child sk-bounce1"></div>
                        <div class="sk-child sk-bounce2"></div>
                        <div class="sk-child sk-bounce3"></div>
                      </div>
                    </button>
                  </span>
                </div>
                <div class="pull-left">
                </div>
                <div class="pull-right">
                  <h4>
                    {if !$create}12.00{else}18.00{/if}
                    <small class="muted">€/{t}year{/t}</small>
                  </h4>
                </div>
              </div>
              <div ng-if="domains.length > 0">
                <h5 class="m-t-30 ng-cloak semi-bold uppercase">
                  {t}Requested domains{/t}
                </h5>
                <ul class="cart-list cart-list-big ng-cloak">
                  <li ng-repeat="domain in domains">
                    <div class="p-l-15">
                      <h5 class="no-overflow">{if $create}{t}Domain registration + mapping{/t}{else}{t}Domain mapping{/t}{/if}</h5>
                      <div class="clearfix">
                        <p class="description pull-left no-margin">[% domain %]</p>
                        <div class="text-right p-r-15 p-b-15">
                          <div class="price">
                            <h4 class="no-margin">
                              <strong>[% price %]</strong>
                              <small>€/year</small>
                            </h4>
                          </div>
                        </div>
                      </div>
                    </div>
                    <i class="fa fa-times pull-left" ng-click="removeFromList($index)"></i>
                  </li>
                </ul>
                <div class="ng-cloak text-right">
                  <div class="p-r-30 p-t-10">
                    <h4>
                      <span class="m-r-30 uppercase">{t}Total{/t}:</span>
                      <strong>[% subtotal %]</strong>
                      <small>€/{t}year{/t}</small>
                    </h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="m-t-30 ng-cloak" ng-show="domains.length > 0 && step != 4">
              <h4 class="semi-bold">2. {t}Billing information{/t}</h4>
              <p ng-if="!edit">{t escape=off}If you need to update this information please <a href="mailto:sales@openhost.es">contact us</a>.{/t}</p>
              <div class="ng-cloak p-b-30" ng-show="edit">
                <h5 class="m-t-20">{t}Contact information{/t}</h5>
                <form name="billingForm">
                  <div class="row">
                    <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.name.$invalid, 'has-success': billingForm.name.$dirty && billingForm.name.$valid }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.name.$dirty && billingForm.name.$valid"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.name.$invalid" tooltip="{t}This field is required{/t}"></i>
                        <input class="form-control" id="name" name="name" ng-model="billing.name" placeholder="{t}Contact name{/t}" required="required" type="text">
                      </div>
                    </div>
                    <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.company.$dirty && billingForm.company.$invalid, 'has-success': billingForm.company.$dirty && billingForm.company.$valid }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.company.$dirty && billingForm.name.$valid"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.company.$invalid" tooltip="{t}This field is required{/t}"></i>
                        <input class="form-control" id="company" name="company" ng-model="billing.company" placeholder="{t}Company name{/t}" type="text">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.email.$invalid, 'has-success': billingForm.email.$dirty && billingForm.email.$valid }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.email.$dirty && billingForm.email.$valid"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.email.$invalid && billingForm.email.$error.required" tooltip="{t}This field is required{/t}"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.email.$invalid && billingForm.email.$error.email" tooltip="{t}This is not a valid email{/t}"></i>
                        <input class="form-control" id="email" name="email" ng-model="billing.email" placeholder="{t}Email{/t}" required="required" type="email">
                      </div>
                    </div>
                    <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.phone.$invalid, 'has-success': billingForm.phone.$dirty && billingForm.phone.$valid }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.phone.$dirty && billingForm.phone.$valid"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.phone.$invalid && billingForm.phone.$error.required" tooltip="{t}This field is required{/t}"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.phone.$invalid && billingForm.phone.$error.pattern" tooltip="{t}This is not a valid phone{/t}"></i>
                        <input class="form-control" id="phone" name="phone" ng-model="billing.phone" pattern="[0-9]+" placeholder="{t}Phone number{/t}" required="required" type="text">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.vat.$invalid || (billingForm.vat.$dirty && !validVat), 'has-success': billingForm.vat.$dirty && billingForm.vat.$valid && validVat }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.vat.$dirty && billingForm.vat.$valid && validVat"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.vat.$invalid && billingForm.vat.$error.required" tooltip="{t}This field is required{/t}"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.vat.$invalid && billingForm.vat.$error.vat || (billingForm.vat.$dirty && !validVat)" tooltip="{t}This is not a valid vat{/t}"></i>
                        <input class="form-control" id="vat" name="vat" ng-model="billing.vat" placeholder="{t}VAT Number{/t}" ng-required="(billing.company != null && billing.company != '') || (billing.country == 'ES' && !validVat)" type="text">
                      </div>
                    </div>
                  </div>
                  <h5 class="m-t-20">{t}Address{/t}</h5>
                  <div class="row">
                    <div class="form-group col-sm-8" ng-class="{ 'has-error': billingForm.address.$invalid, 'has-success': billingForm.address.$dirty && billingForm.address.$valid }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.address.$dirty && billingForm.address.$valid"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.address.$invalid && billingForm.address.$error.required" tooltip="{t}This field is required{/t}"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.address.$invalid && billingForm.address.$error.address" tooltip="{t}This is not a valid address{/t}"></i>
                        <input class="form-control" id="address" name="address" ng-model="billing.address" placeholder="{t}Address{/t}" required="required" type="text">
                      </div>
                    </div>
                    <div class="form-group col-sm-4" ng-class="{ 'has-error': billingForm.postal_code.$invalid, 'has-success': billingForm.postal_code.$dirty && billingForm.postal_code.$valid }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.postal_code.$dirty && billingForm.postal_code.$valid"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.postal_code.$invalid && billingForm.postal_code.$error.required" tooltip="{t}This field is required{/t}"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.postal_code.$invalid && billingForm.postal_code.$error.postal_code" tooltip="{t}This is not a valid postal_code{/t}"></i>
                        <input class="form-control" id="postal_code" name="postal_code" ng-model="billing.postal_code" placeholder="{t}Postal code{/t}" required="required" type="text">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-sm-4" ng-class="{ 'has-error': billingForm.city.$invalid, 'has-success': billingForm.city.$dirty && billingForm.city.$valid }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.city.$dirty && billingForm.city.$valid"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.city.$invalid && billingForm.city.$error.required" tooltip="{t}This field is required{/t}"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.city.$invalid && billingForm.city.$error.city" tooltip="{t}This is not a valid city{/t}"></i>
                        <input class="form-control" id="city" name="city" ng-model="billing.city" placeholder="{t}City{/t}" required="required" type="text">
                      </div>
                    </div>
                    <div class="form-group col-sm-4" ng-class="{ 'has-error': billingForm.state.$invalid, 'has-success': billingForm.state.$dirty && billingForm.state.$valid }">
                      <div class="input-with-icon right">
                        <i class="fa fa-check text-success" ng-if="billingForm.state.$dirty && billingForm.state.$valid"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.state.$invalid && billingForm.state.$error.required" tooltip="{t}This field is required{/t}"></i>
                        <i class="fa fa-times text-danger" ng-if="billingForm.state.$invalid && billingForm.state.$error.state" tooltip="{t}This is not a valid state{/t}"></i>
                        <input class="form-control" id="state" name="state" ng-model="billing.state" placeholder="{t}State{/t}" required="required" type="text">
                      </div>
                    </div>
                    <div class="form-group col-sm-4" ng-class="{ 'has-error': billingForm.country.$invalid, 'has-success': billingForm.country.$dirty && billingForm.country.$valid }">
                      <div class="input-with-icon right">
                        <select class="form-control" id="country" name="country" ng-model="billing.country" placeholder="{t}Country{/t}" required="required">
                          <option value="">{t}Select a country{/t}...</option>
                          <option value="[% key %]" ng-repeat="(key,value) in countries">[% value %]</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div class="ng-cloak p-b-30 row" ng-show="!edit">
                <div class="col-sm-6 p-b-10">
                  <h5 class="m-t-20">{t}Contact information{/t}</h5>
                  <p>
                  [% billing.name %]
                  <span ng-if="billing.company_name">
                    ([% billing.company %])
                  </span>
                  </p>
                  <p>[% billing.vat %]</p>
                  <p>[% billing.email %]</p>
                  <p>[% billing.phone %]</p>
                </div>
                <div class="col-sm-6">
                  <h5 class="m-t-20">{t}Address{/t}</h5>
                  <p>[% billing.address %]</p>
                  <p>[% billing.postal_code %], [% billing.city %], [% billing.state %]</p>
                  <p>[% countries[billing.country]%]</p>
                </div>
              </div>
            </div>
            <div class="p-t-15 ng-cloak" ng-show="step != 4 && domains.length > 0">
              <h4 class="semi-bold">3. {t}Purchase summary{/t}</h4>
              <div ng-show="billingForm.$valid">
                <div class="p-t-5 pull-left">
                  <h4 class="semi-bold">[% billing.name %]</h4>
                  <address>
                    <strong ng-if="billing.company">[% billing.company %]</strong><br>
                    [% billing.address %]<br>
                    [% billing.postal_code %], [% billing.city %], [% billing.state %]<br>
                    [% countries[billing.country] %]<br>
                  </address>
                </div>
                <div class="pull-right">
                  <img alt="" class="invoice-logo p-b-15" height="50" src="/assets/images/logos/opennemas-powered-horizontal.png">
                  <address>
                    <strong>Openhost, S.L.</strong><br>
                    Progreso 64, 4º A<br>
                    32003, Ourense, Ourense<br>
                    [% countries['ES']%]<br>
                  </address>
                </div>
                <div class="clearfix"></div>
                <table class="m-t-30 table table-invoice">
                  <thead>
                    <tr>
                      <th class="text-left uppercase">{t}Description{/t}</th>
                      <th style="width:140px" class="text-right uppercase">{t}Unit price{/t}</th>
                      <th style="width:90px" class="text-right uppercase">{t}Total{/t}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr ng-repeat="domain in domains">
                      <td>{if $create}{t}Domain registration + mapping{/t}{else}{t}Domain mapping{/t}{/if}: [% domain %]</td>
                      <td class="text-right">[% price %] €</td>
                      <td class="text-right">[% price %] €</td>
                    </tr>
                    <tr>
                      <td rowspan="3"></td>
                      <td class="text-right"><strong>Subtotal</strong></td>
                      <td class="text-right">[% subtotal %] €</td>
                    </tr>
                    <tr>
                      <td class="text-right no-border"><strong>{t}VAT{/t} ([% vatTax %]%)</strong></td>
                      <td class="text-right">[% vat %] €</td>
                    </tr>
                    <tr>
                      <td class="text-right no-border"><div class="well well-small green"><strong>Total</strong></div></td>
                      <td class="text-right"><strong>[% total %] €</strong></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <p class="p-b-30 text-danger" ng-show="billingForm.$invalid">
                {t}You have to complete your billing information to complete the purchase.{/t}
              </p>
            </div>
            <div class="ng-cloak" ng-show="domains.length > 0 && step != 4">
              <h4 class="semi-bold">4. {if $create}{t}Terms of create a new domain{/t}{else}{t}Terms of redirection{/t}{/if}</h4>
              {if $create}
              <ul>
                <li>{t}Payment: the registration and redirection service requires payment in advance. This amount cannot be divided into several payments.{/t}</li>
                <li>{t}Identity: when buying a new domain, the identity of the domains would be the one of your entity. The administrative identity, technical contact, billing contact name would be Openhost, SL{/t}</li>
                <li>{t}Availability: please inform us of the name of the domain that you wish to register and we will let you know if it is available. May be one or more domains.{/t}</li>
                <li>{t}The service of registering and redirecting the domain is valid for one year, so you will be notified prior to the renewal date.{/t}</li>
                <li>{t}Following the purchase of the domain, if the user wants to transfer it to another provider, which may only be transferred to another from the 6th month after the date of registration of the domain.{/t}</li>
              </ul>
              {else}
              <ul>
                <li>{t}Payment: the redirection service requires payment in advance. The amount cannot be divided into several payments. {/t}</li>
                <li>{t}Identity: the identity of the domains would be the customer's identity because the domain does not undergo any change. {/t}</li>
                <li>{t}The service domain redirection is valid for one year, so you will be notified prior to the renewal date. {/t}</li>
                <li>{t}The customer must make changes to the DNS zone for your domain registration www. This change has nothing to do with the Opennemas platform or company Openhost, SL (company that maintains the service). {/t}</li>
                <li>{t}If redirection does not work through no fault of the platform, ie by malfunction of the DNS servers the client, Openhost, SL area will have nothing to do with the damage caused to the hours of service failure. {/t}</li>
              </ul>
              {/if}
              <div class="text-center p-t-30">
                <div class="form-group">
                  <div class="checkbox">
                    <input id="terms" name="terms" ng-model="terms" type="checkbox">
                    <label class="no-margin text-left" for="terms">
                      {if $create}
                      {t}I have read and accept the Terms of creating a new domain{/t}
                      {else}
                      {t}I have read and accept the Terms of redirection{/t}
                      {/if}
                    </label>
                  </div>
                </div>
                <button class="btn btn-large btn-success text-center" ng-click="confirm()" ng-disabled="domains.length === 0 || billingForm.$invalid || !terms">
                  {t}Confirm{/t}
                </button>
              </div>
            </div>
          </div>
          <div class="ng-cloak p-b-30 p-l-30 p-r-30 p-t-30 text-center" ng-show="step == 4">
            <i class="fa fa-heart fa-3x"></i>
            <h3 class="p-b-30">{t}Thank you for your request!{/t}</h3>
            <p class="p-b-15">
            {t}In the next 24 hours you will receive an email with payment instructions and the invoice.{/t}
            </p>
            <p class="p-b-15">
            {capture name="client_info_url"}{url name=admin_client_info_page}{/capture}
            {t escape=off 1=$smarty.capture.client_info_url}Meanwhile, you can go to your <a href="%1">My newspaper</a> and check your active features, navigate to <a href="http://help.opennemas.com">our help</a> or check out <a href="http://youtube.com/opennemas">our videos</a> to see how easy is to manage Opennemas.{/t}
            </p>
            <p class="p-b-10">
            {t}Oh!, it would be a good time to share with your friends your newspaper's improvements{/t}
            </p>
            <div>
              <a href="http://www.facebook.com" target="_blank">
                <i class="fa fa-lg fa-facebook m-r-30"></i>
              </a>
              <a href="http://twitter.com" target="_blank">
                <i class="fa fa-lg fa-twitter m-r-30"></i>
              </a>
              <a href="https://plus.google.com/" target="_blank">
                <i class="fa fa-lg fa-google-plus m-r-30"></i>
              </a>
              <a href="https://www.linkedin.com/" target="_blank">
                <i class="fa fa-lg fa-linkedin"></i>
              </a>
            </div>
            <h4 class="m-t-30">
              {t}Have a wonderful day!{/t}
              </p>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}
