{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_store.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
  <style>
  .grid.simple {
    font-size:14px;
  }
  </style>
{/block}

{block name="content"}
  <div ng-controller="StoreCheckoutCtrl" ng-init="{if !empty($billing)}billing = {json_encode($billing)|clear_json}; {/if}countries = {json_encode($countries)|clear_json};taxes = {json_encode($taxes)|clear_json}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=admin_store_list}">
                  <i class="fa fa-shopping-cart"></i>
                  {t}Store{/t}
                </a>
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
                        <img class="img-responsive pull-left" ng-src="/assets/images/store/[% item.thumbnail %]">
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
                    <a class="btn btn-block btn-white" href="{url name=admin_store_checkout}" ng-disabled="!cart || cart.length == 0">
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
            {capture name="store_url"}{url name='admin_store_list'}{/capture}
            <h4>{t escape=off 1=$smarty.capture.store_url}Return to <a href="%1">store</a> and try again{/t}</h4>
          </div>
          <div class="grid simple" ng-show="cart.length > 0 || step == 4">
            <div class="grid-body">
              <div ng-show="step != 4">
                <h4 class="semi-bold">1. {t}Cart{/t}</h4>
                <p>{t}You are about to order next items. In the next 24hours our sales team will send you payment and activation information. {/t}</p>
                <ul class="cart-list cart-list-big">
                  <li class="clearfix" ng-repeat="item in cart">
                    <img class="img-responsive pull-left" ng-if="item.thumbnail" ng-src="/assets/images/store/[%item.thumbnail%]">
                    <img class="img-responsive pull-left" ng-if="!item.thumbnail && item.images.length > 0" ng-src="[% '/asset/scale,300,300' + item.path + '/' + item.images[0] %]">
                    <img class="img-responsive pull-left" ng-if="!item.thunbnail && item.screenshots.length > 0 && item.type == 'theme'" ng-src="[% '/asset/scale,300,300' + item.path + '/' + item.screenshots[0] %]">
                    <img class="img-responsive pull-left" ng-if="!item.thumbnail && (!item.images || item.images.length == 0) && (!item.screenshots || item.screenshots.length == 0)" src="http://placehold.it/1024x768">
                    <div class="p-l-100">
                      <h5>[% item.name %]</h5>
                      <div class="description" ng-bind-html="item.description[lang] ? item.description[lang] : item.description"></div>
                      <div class="text-right p-r-15 p-b-15">
                        <div class="price">
                          <h4 class="no-margin">
                            <strong ng-if="item.price > 0">[% item.price.month %]</strong>
                            <strong ng-if="item.meta_price > 0">[% item.meta.price.month %]</strong>
                            <small> € / {t}month{/t}</small>
                          </h4>
                        </div>
                      </div>
                    </div>
                    <i class="fa fa-times pull-left" ng-click="removeFromCart(item)"></i>
                  </li>
                </ul>
                <hr>
                <div class="text-right">
                  <h4 class="p-r-30">
                    <span class="uppercase p-r-30">{t}Total{/t}</span>
                    <strong>[% subtotal %]</strong><small> € / {t}month{/t}</small>
                  </h4>
                </div>
              </div>
              <div class="p-t-15" ng-show="step != 4">
                <div class="billing-info">
                  <h4 class="semi-bold">
                    2. {t}Billing information{/t}
                  </h4>
                  <p>{t escape=off}If you need to update this information please <a href="mailto:sales@openhost.es">contact us</a>.{/t}</p>
                  <div class="ng-cloak p-b-30" ng-show="edit">
                    <h5 class="m-t-20">{t}Contact information{/t}</h5>
                    <form name="billingForm" id="formulario">
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
                        <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.phone.$invalid || !validPhone, 'has-success': billingForm.phone.$dirty && billingForm.phone.$valid && validPhone }">
                          <div class="input-with-icon right">
                            <i class="fa fa-check text-success" ng-if="billingForm.phone.$dirty && billingForm.phone.$valid && validPhone"></i>
                            <i class="fa fa-times text-danger" ng-if="!validPhone" tooltip="{t}This is not a valid phone{/t}"></i>
                            <input class="form-control" id="phone" name="phone" ng-model="billing.phone" placeholder="{t}Phone number{/t}" type="text">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-sm-6" ng-class="{ 'has-error': billingForm.vat.$invalid || (billingForm.vat.$dirty && !validVat), 'has-success': billingForm.vat.$dirty && billingForm.vat.$valid && validVat }">
                          <div class="input-with-icon right">
                            <i class="fa fa-check text-success" ng-if="billingForm.vat.$dirty && billingForm.vat.$valid && validVat"></i>
                            <i class="fa fa-times text-danger" ng-if="billingForm.vat.$invalid && billingForm.vat.$error.required" tooltip="{t}This field is required{/t}"></i>
                            <i class="fa fa-times text-danger" ng-if="billingForm.vat.$invalid && billingForm.vat.$error.vat || (billingForm.vat.$dirty && !validVat)" tooltip="{t}This is not a valid VAT identification number{/t}"></i>
                            <input class="form-control" id="vat" name="vat" ng-model="billing.vat" placeholder="{t}VAT identification number{/t}" ng-required="(billing.company != null && billing.company != '') || (billing.country == 'ES' && !validVat)" type="text">
                          </div>
                          <div class="help m-t-5">
                            <a href="https://en.wikipedia.org/wiki/VAT_identification_number" target="_blank">{t}What is a VAT identification number?{/t}</a>
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
                              <option value="[% value %]" ng-repeat="(key,value) in countries" ng-selected="[% billing.country === value %]">[% key %]</option>
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
              </div>
              <div class="p-t-15" ng-show="step != 4">
                <h4 class="semi-bold">3. {t}Purchase summary{/t}</h4>
                <p class="p-b-30 text-danger" ng-show="billingForm.$invalid">
                  {t}You have to complete your billing information to complete the purchase.{/t}
                </p>
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
                <div class="text-center">
                  <div class="form-group">
                    <div class="checkbox">
                      <input id="terms" name="terms" ng-model="terms" type="checkbox">
                      <label class="no-margin text-left" for="terms">
                        {t escape=off}I have read and accept the <a href="http://help.opennemas.com/knowledgebase/articles/235348-condiciones-del-servicio-de-opennemas" target="_blank">Terms of Service</a>{/t}
                      </label>
                    </div>
                  </div>
                  <button class="btn btn-large btn-success text-center" ng-click="confirm()" ng-disabled="billingForm.$invalid || !terms || !validVat">
                    {t}Confirm{/t}
                  </button>
                </div>
              </div>
              <div class="p-b-30 p-l-30 p-r-30 p-t-30 text-center" ng-show="step == 4">
                <i class="fa fa-heart fa-3x"></i>
                <h3 class="p-b-30">{t}Thank you for your purchase request!{/t}</h3>
                <p class="p-b-15">
                  {t}In the next 24 hours you will receive an email with payment instructions and invoice.{/t}
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
    </div>
  </div>
{/block}
