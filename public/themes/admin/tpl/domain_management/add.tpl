{extends file="domain_management/list.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_store.less,
    @AdminTheme/less/_domain.less,
    @AdminTheme/less/_braintree.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
{/block}
{block name="footer-js" append}
  {javascripts}
    <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
    <script type="text/javascript">
      $(document).on('keydown', function (e) {
        if (e.which === 8 && !$(e.target).is('input, textarea')) {
          window.onbeforeunload = function() {
            return "{t}You are leaving the current page.{/t}";
          }
        }
      });

      $(document).on('click', function (e) {
        window.onbeforeunload = null;
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-indent fa-server"></i>
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
  <div class="content" ng-controller="DomainManagementCtrl" ng-init="{if !empty($client)}client = {json_encode($client)|clear_json}; {/if}{if $create}create = 1;{/if}clientToken = '{$token}';countries = {json_encode($countries)|clear_json};taxes = {json_encode($taxes)|clear_json}">
    <div class="row">
      <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
        <div class="form-wizard-steps clearfix m-b-15 ng-cloak">
          <ul class="wizard-steps form-wizard" ng-class="{ 'wizard-steps-3': client }">
            <li class="text-center" ng-class="{ 'active': step == 1 }">
              <span class="step">1</span>
              <h5 class="m-t-15">{t}Domains{/t}</h5>
            </li>
            <li class="col-xs-3 text-center" ng-class="{ 'active': step == 2 }" ng-if="!client">
              <span class="step">2</span>
              <h5 class="m-t-15">{t}Billing information{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 3 }">
              <span class="step">[% client ? '2' : '3' %]</span>
              <h5 class="m-t-15">{t}Check & payment{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 4 }">
              <span class="step">[% client ? '3' : '4' %]</span>
              <h5 class="m-t-15">{t}Finish{/t}</h5>
            </li>
          </ul>
        </div>
        <div class="fake-form-wizard-steps ng-cloak">
          <div class="fake-wizard-steps text-center" ng-class="{ 'col-xs-3': !client, 'col-xs-4': client, 'col-xs-offset-3': !client && step == 2, 'col-xs-offset-6': !client && step == 3, 'col-xs-offset-9': !client && step == 4, 'col-xs-offset-4': client && step == 3, 'col-xs-offset-8': client && step == 4 }">
            <div class="step">
              <i class="fa fa-truck fa-flip-horizontal fa-lg"></i>
            </div>
          </div>
        </div>
        <div class="grid simple ng-hide" ng-show="step == 1">
          <div class="grid-body clearfix">
            <div>
              <h4 class="semi-bold">{t}Domains{/t}</h4>
              <h5>
                {if !$create}
                  {t}I have an existing domain and I want to redirect it to my Opennemas digital newspaper.{/t}
                {else}
                  {t}I do not have my own domain and I want to create one and redirect it to my Opennemas digital newspaper{/t}
                {/if}
              </h5>
              <div class="row m-t-15">
                <div class="col-sm-9">
                  <div class="input-group">
                    <span class="input-group-addon">www.</span>
                    <input autofocus class="form-control uib-typeahead" ng-keyup="mapByKeyPress($event)" ng-model="domain" placeholder="{t}Enter a domain{/t}" uib-typeahead="domain for domain in getSuggestions($viewValue) | filter: $viewValue" type="text">
                    <span class="input-group-btn">
                      <button class="btn btn-success" ng-click="map()" ng-disabled="!isValid() || !domain">
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
                </div>
                <div class="col-sm-3">
                  <h4 class="text-right">
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
                              <small>€/{t}year{/t}</small>
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
              <div class="row m-t-50 ng-cloak" ng-if="domains.length > 0">
                <div class="col-sm-6 col-sm-offset-3">
                  <button class="btn btn-block btn-success" ng-click="next()">
                    <h4 class="text-uppercase text-white">
                      {t}Next{/t}
                    </h4>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="grid simple ng-hide" ng-show="step == 2">
          <div class="grid-body">
            <div class="ng-cloak">
              <h4 class="m-b-30 semi-bold">{t}Billing information{/t}</h4>
              <div ng-show="!client.country">
                <h5>{t}Where are you from?{/t}</h5>
                <div class="form-group col-sm-4">
                  <div class="input-with-icon right">
                    <select class="form-control" id="country" name="country" ng-model="client.country" placeholder="{t}Country{/t}" required="required">
                      <option value="">{t}Select a country{/t}...</option>
                      <option value="[% value %]" ng-repeat="(key,value) in countries" ng-selected="[% billing.country === value %]">[% key %]</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="ng-cloak p-b-30" ng-show="client.country">
                {include file='client/form.tpl'}
                <div class="row m-t-50 ng-cloak">
                  <div class="col-sm-6 col-sm-offset-3">
                    <button class="btn btn-block btn-loading btn-success" ng-click="saveClient()">
                      <i class="fa fa-circle-o-notch fa-spin m-t-15 pull-left" ng-if="loading"></i>
                      <h4 class="text-uppercase text-white">
                        {t}Next{/t}
                      </h4>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="grid simple ng-hide" ng-show="step == 3">
          <div class="grid-body">
            <div class="ng-cloak">
              <h4 class="semi-bold">{t}Purchase summary{/t}</h4>
              <div class="p-t-5 pull-left">
                <h4 class="semi-bold">[% client.first_name %]</h4>
                <address>
                  <strong ng-if="client.company">[% client.company %]</strong><br>
                  [% client.address %]<br>
                  [% client.postal_code %], [% client.city %], [% client.state %]<br>
                  [% countries[client.country] %]<br>
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
              <div class="table-wrapper">
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
                      <td rowspan="3">
                      </td>
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
              <div class="braintree clearfix ng-cloak">
                <div class="braintree-payment-buttons clearfix">
                  <h5>Pay with</h5>
                  <div id="paypal-container" ng-class="{ 'pull-left': !payment }" ng-show="(!payment && !nonce) || (nonce && payment == 'paypal')"></div>
                  <button class="btn btn-info btn-credit-card no-animate pull-left m-l-15" ng-click="payment = 'card'" ng-show="!payment && !nonce" type="button">
                    <i class="fa fa-credit-card fa-lg m-r-5"></i>
                    <strong>{t}Credit card{/t}</strong>
                  </button>
                </div>
                <div class="braintree-fake-method" ng-show="payment === 'card' && nonce">
                  <i class="fa fa-credit-card"></i>
                  <strong>{t}Credit card{/t}</strong>
                  <span class="btn btn-link" ng-click="nonce = null; payment = null;">
                    {t}Cancel{/t}
                  </span>
                </div>
                <form class="col-md-8 col-md-offset-2" id="checkout" method="post" action="/checkout" ng-hide="nonce || payment !== 'card'">
                  <p class="text-danger" ng-if="error">[% error %]</p>
                  <div class="form-group">
                    <label class="form-label" for="card-number">Card Number</label>
                    <div class="form-control" id="card-number"></div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-4">
                      <label class="form-label" for="cvv">CVV</label>
                      <div id="cvv" class="form-control"></div>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label" for="expiration-date">Expiration Date</label>
                      <div id="expiration-date" class="form-control"></div>
                    </div>
                  </div>
                  <div class="text-center">
                    <button class="btn btn-link" id="submit" ng-click="payment = null" type="button">
                      <i class="fa fa-times m-r-5"></i>{t}Cancel{/t}
                    </button>
                    <button class="btn btn-info btn-loading" id="submit" ng-click="toggleCardLoading()" type="submit">
                      <i class="fa" ng-class="{ 'fa-check': !cardLoading, 'fa-circle-o-notch fa-spin': cardLoading }"></i>
                      {t}Confirm{/t}
                    </button>
                  </div>
                </form>
              </div>
              <div class="row m-t-50 ng-cloak" ng-show="nonce">
                <div class="col-sm-6 col-sm-offset-3">
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
                    <button class="btn btn-block btn-loading btn-success" ng-click="confirm()" ng-disabled="domains.length === 0 || !terms || !client.id || !nonce || loading">
                      <i class="fa fa-circle-o-notch fa-spin m-t-15" ng-if="loading"></i>
                      <h4 class="text-uppercase text-white">
                        {t}Confirm{/t}
                      </h4>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="grid simple ng-hide" ng-show="step == 4">
          <div class="grid-body">
            <div class="ng-cloak p-b-30 p-l-30 p-r-30 p-t-30 text-center">
              <i class="fa fa-heart fa-3x"></i>
              <h3 class="p-b-30">{t}Thank you for your purchase!{/t}</h3>
              <p class="p-b-15">
              {t}In the next 24 hours you will receive an email with payment instructions and the invoice.{/t}
              </p>
              <p class="p-b-15">
              {t escape=off}Meanwhile, you can go to your <a href="{url name=admin_client_info_page}">My newspaper</a> and check your active features, navigate to <a href="http://help.opennemas.com">our help</a> or check out <a href="http://youtube.com/opennemas">our videos</a> to see how easy is to manage Opennemas.{/t}
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
{/block}
