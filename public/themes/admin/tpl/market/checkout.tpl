{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_market.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="content"}
  <div ng-controller="MarketCheckoutCtrl" ng-init="{if !empty($billing)}billing = {json_encode($billing)|clear_json}; {/if}countries = {json_encode($countries)|clear_json}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-shopping-cart"></i>
                {t}Market{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks shopping-cart dropdown">
                <div class="p-10" data-toggle="dropdown">
                  <span class="hidden-xs p-l-5 p-r-5">
                    {t}Cart{/t}
                  </span>
                  <span>
                    <i class="fa fa-shopping-cart fa-lg p-r-10"></i>
                    <span class="ng-cloak cart-orb animated" ng-class="{ 'bounceIn': bounce, 'pulse': pulse }" ng-if="cart.length > 0">
                      [% cart.length %]
                    </span>
                  </span>
                </div>
                <div class="dropdown-menu on-right">
                  <div class="shopping-cart-placeholder" ng-if="!cart || cart.length == 0">
                    <h5 class="text-center">
                      {t}Your shopping cart is empty{/t}
                    </h5>
                  </div>
                  <div class="shopping-cart-placeholder" ng-if="cart.length > 0">
                    <scrollable>
                    <ul class="cart-list">
                      <li class="clearfix" ng-repeat="item in cart | orderBy: name">
                        <img class="img-responsive pull-left" ng-src="/assets/images/market/[% item.thumbnail %]">
                        <span class="pull-left">
                          <h5>[% item.name %]</h5>
                          <p class="description">[% item.description %]</p>
                        </span>
                        <i class="fa fa-times pull-left" ng-click="removeFromCart(item, $event)"></i>
                      </li>
                    </ul>
                    </scrollable>
                  </div>
                  <div class="p-r-10 p-t-15">
                    <a class="btn btn-block btn-white" href="{url name=admin_market_checkout}" ng-disabled="!cart || cart.length == 0">
                      <i class="fa fa-shopping-cart"></i>
                      {t}Checkout{/t}
                    </a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content checkout-wizard">
      <div class="row ng-cloak">
        <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 p-r-30">
          <div class="text-center" ng-show="step != 4 && (!cart || cart.length == 0)">
            <h1><i class="fa fa-shopping-cart"></i></h1>
            <h3>{t}Your shopping cart is empty{/t}</h3>
            {capture name="market_url"}{url name='admin_market_list'}{/capture}
            <h4>{t escape=off 1=$smarty.capture.market_url}Return to <a href="%1">market</a> and try again{/t}</h4>
          </div>
          <div class="grid simple" ng-show="cart.length > 0 || step == 4">
            <div class="grid-body">
              <div class="clearfix form-wizard-steps p-b-30 p-t-15" ng-show="step != 4">
                <ul class="wizard-steps form-wizard wizard-steps-market">
                  <li class="pointer" ng-class="{ 'active': step === 1 }" ng-click="setStep(1)">
                    <span class="step">1</span>
                    <span class="title">{t}Cart{/t}</span>
                  </li>
                  <li class="pointer" ng-class="{ 'active': step === 2 }" ng-click="setStep(2)">
                    <span class="step">2</span>
                    <span class="title">{t}Billing information{/t}</span>
                  </li>
                  <li ng-class="{ 'active': step === 3 }" ng-click="stetStep(3)">
                    <span class="step">3</span>
                    <span class="title">{t}Confirmation{/t}</span>
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="tab-content transparent p-t-30" ng-if="step != 4">
                <div class="tab-pane" ng-class="{ 'active': step == 1 }">
                  <h5 class="p-b-20">{t}You are about to purchase the next items:{/t}</h5>
                  <ul class="cart-list no-margin">
                    <li class="clearfix" ng-repeat="item in cart">
                      <img class="img-responsive pull-left" ng-src="/assets/images/market/[%item.thumbnail%]">
                      <div class="p-l-100">
                        <h5>[% item.name %]</h5>
                        <p class="description">[% item.description %]</p>
                        <div class="text-right p-r-15 p-b-15">
                          <div class="price">
                            <h4 class="no-margin">
                              <strong>[% item.price.month %]</strong><small>€ / {t}month{/t}</small>
                            </h4>
                          </div>
                        </div>
                      </div>
                      <i class="fa fa-times pull-left" ng-click="removeFromCart(item)"></i>
                    </li>
                  </ul>
                  <hr>
                  <div class="text-right clearfix">
                    <table align=right class="clearfix checkout-summary">
                      <tbody>
                        <tr>
                          <td><h5 class="uppercase">{t}Total{/t}</h5></td>
                          <td><h5><strong>[% total %]</strong><small>€ / {t}month{/t}</small></h5></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="text-center p-t-30">
                    <button class="btn btn-large btn-success text-center" ng-click="setStep(2)">
                      {t}Next{/t}
                    </button>
                  </div>
                </div>
                <div class="tab-pane" ng-class="{ 'active': step == 2 }" >
                  <div class="billing-info">
                    <h4 class="semi-bold">
                      {t}Billing information{/t}
                      <button class="btn btn-link" ng-click="edit = 1" ng-show="!edit && billing.name">({t}Edit{/t})</button>
                    </h4>
                    <div class="ng-cloak p-b-30" ng-show="edit || !billing.name">
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
                    {*<h4>{t}Payment Info{/t}</h4>
                    <div class="form-group">
                      <label for="contact-name">{t}Card number{/t}</label>
                      <input class="form-control" id="contact-name" required="required" type="text">
                    </div>
                    <div class="form-group">
                      <label for="expire-date">{t}Expire date{/t}</label>
                      <input class="form-control" id="expire-date" required="required" type="text">
                    </div>*}
                  </div>
                  <div class="text-center p-t-50">
                    <button class="btn btn-large btn-success text-center" ng-click="setStep(3)" ng-disabled="!validVat || billingForm.$invalid">
                      {t}Next{/t}
                    </button>
                  </div>
                </div>
                <div class="tab-pane" ng-class="{ 'active': step == 3 }">
                  <div class="pull-left">
                    <img alt="" class="invoice-logo p-b-15" height="80" src="/assets/images/logos/opennemas-powered.png">
                    <address>
                      <strong>Openhost, S.L.</strong><br>
                      Progreso 64, 4º A<br>
                      32003, Ourense, Ourense<br>
                      [% countries['ES']%]<br>
                      <abbr title="Phone">P:</abbr> 988980045
                    </address>
                  </div>
                  <div class="clearfix"></div>
                  <br>
                  <br>
                  <br>
                  <div class="row p-b-30">
                    <div class="col-md-12">
                      <h4 class="semi-bold">[% billing.name %]</h4>
                      <address>
                        <strong ng-if="billing.company">[% billing.company %]</strong><br>
                        [% billing.address %]<br>
                        [% billing.postal_code %], [% billing.city %], [% countries[billing.country] %]
                        [% billing.country %]<br>
                        <abbr title="Phone">P:</abbr> [% billing.phone %]
                      </address>
                    </div>
                  </div>
                  <table class="table table-invoice">
                    <thead>
                      <tr>
                        <th class="text-left uppercase">{t}Description{/t}</th>
                        <th style="width:140px" class="text-right uppercase">{t}Unit price{/t}</th>
                        <th style="width:90px" class="text-right uppercase">{t}Total{/t}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr ng-repeat="item in cart">
                        <td>[% item.name %]</td>
                        <td class="text-right">[% item.price.month %] €</td>
                        <td class="text-right">[% item.price.month %] €</td>
                      </tr>
                      <tr>
                        <td rowspan="3"></td>
                        <td class="text-right"><strong>Subtotal</strong></td>
                        <td class="text-right">[% subtotal %] €</td>
                      </tr>
                      <tr>
                        <td class="text-right no-border"><strong>{t}VAT{/t} ([% vatTax %])</strong></td>
                        <td class="text-right">[% vat %] €</td>
                      </tr>
                      <tr>
                        <td class="text-right no-border"><div class="well well-small green"><strong>Total</strong></div></td>
                        <td class="text-right"><strong>[% total %] €</strong></td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="text-center">
                    <div class="form-group">
                      <div class="checkbox">
                        <input id="terms" name="terms" ng-model="terms" type="checkbox">
                        <label class="no-margin text-left" for="terms"></label>
                        {t escape=off}I have read and accept the <a href="http://help.opennemas.com/knowledgebase/articles/235348-condiciones-del-servicio-de-opennemas" target="_blank">Terms of Service</a>{/t}
                      </div>
                    </div>
                    <button class="btn btn-large btn-success text-center" ng-click="confirm()" ng-disabled="billingForm.$invalid || !terms">
                      {t}Confirm{/t}
                    </button>
                  </div>
                </div>
              </div>
              <div class="text-center" ng-if="step == 4">
                <i class="fa fa-heart fa-3x"></i>
                <h3 class="p-b-30">{t}Nice{/t}!</h3>
                <p class="p-b-15">
                {t}Thank you! We have received your request and will get back to you as soon as possible. You will receive a confirmation e-mail too.{/t}
                </p>
                <p class="p-b-30">
                {t escape=off}Meanwhile, you can go to your <a href="{url name='admin_client_info_page'}">My newspaper</a> and check your active features, navigate our help to familiarize with your news tool, or check some awesome videos to see how easy are to manage Opennemas.{/t}
                </p>
                <p class="p-b-15">
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
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}
