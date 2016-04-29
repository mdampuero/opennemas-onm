{extends file="domain_management/list.tpl"}

{block name="header-css" append}
  {stylesheets src="@AdminTheme/less/_store.less,
    @AdminTheme/less/_domain.less,
    @AdminTheme/less/_checkout.less" filters="cssrewrite,less"}
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
  <div class="content" ng-controller="DomainManagementCtrl" ng-init="description='{if $create}{t}Domain registration + mapping{/t}{else}{t}Domain mapping{/t}{/if}';{if !empty($client)}clientValid = true; client = {json_encode($client)|clear_json}; {/if}{if $create}create = 1;{/if}clientToken = '{$token}';countries = {json_encode($countries)|clear_json};taxes = {json_encode($taxes)|clear_json}">
    <div class="row">
      <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
        <div class="form-wizard-steps clearfix m-b-15 ng-cloak">
          <ul class="form-wizard wizard-steps wizard-steps-5">
            <li class="text-center" ng-class="{ 'active': step == 1 }">
              <span class="step">1</span>
              <h5 class="m-t-15">{t}Domains{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 2 }">
              <span class="step">2</span>
              <h5 class="m-t-15">{t}Billing information{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 3 }">
              <span class="step">[% client ? '2' : '3' %]</span>
              <h5 class="m-t-15">{t}Payment{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 4 }">
              <span class="step">[% client ? '3' : '4' %]</span>
              <h5 class="m-t-15">{t}Check{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 5 }">
              <span class="step">[% client ? '4' : '5' %]</span>
              <h5 class="m-t-15">{t}Finish{/t}</h5>
            </li>
          </ul>
        </div>
        <div class="fake-form-wizard-steps ng-cloak">
          <div class="fake-wizard-steps fake-wizard-steps-5 fake-wizard-steps-active-[% step %] text-center">
            <div class="step">
              <i class="fa fa-truck fa-flip-horizontal fa-lg"></i>
            </div>
          </div>
        </div>
        <div class="grid simple ng-hide" ng-show="step == 1">
          <div class="grid-body clearfix">
            <div>
              <h4 class="semi-bold">{t}Domains{/t}</h4>
              <p class="m-b-15 m-t-15">
                {if !$create}
                  {t}I have an existing domain and I want to redirect it to my Opennemas digital newspaper.{/t}
                {else}
                  {t}I do not have my own domain and I want to create one and redirect it to my Opennemas digital newspaper{/t}
                {/if}
              </p>
              <div class="row">
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
              <div ng-if="cart.length > 0">
                <h5 class="m-t-30 ng-cloak semi-bold uppercase">
                  {t}Requested domains{/t}
                </h5>
                {include file="store/_cart.tpl"}
              </div>
              <div class="row m-t-50 ng-cloak" ng-if="cart.length > 0">
                <div class="col-sm-4 col-sm-offset-4">
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
              {include file='client/form.tpl'}
            </div>
          </div>
        </div>
        <div class="grid simple ng-hide" ng-show="step == 3">
          <div class="grid-body">
            <h4 class="semi-bold">{t}Payment{/t}</h4>
            <p class="m-t-15">
              {t}Select the payment method.{/t}
              {t}You'll have a chance to review your order before it's placed.{/t}
            </p>
            <p class="m-b-15 m-t-50 text-center">
              <strong>
                {t}Any problem with payment?{/t}
                <a href="#" ng-click="open('payment-help')">
                  {t}Click here for a quick tips.{/t}
                </a>
              </strong>
            </p>
            <form id="braintree-form">
              <div class="braintree">
                <div id="braintree-container"></div>
                <div class="row m-t-40 ng-cloak">
                  <div class="col-sm-4 m-t-15">
                    <button class="btn btn-block btn-loading btn-white" ng-click="previous()" ng-disabled="paymentLoading" type="button">
                      <h4 class="text-uppercase">{t}Previous{/t}</h4>
                    </button>
                  </div>
                  <div class="col-sm-4 col-sm-offset-4 m-t-15">
                    <button class="btn btn-block btn-loading btn-success" ng-disabled="paymentLoading" type="submit">
                      <i class="fa fa-circle-o-notch fa-spin m-t-15" ng-if="paymentLoading"></i>
                      <h4 class="text-uppercase text-white">{t}Next{/t}</h4>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="grid simple ng-hide" ng-show="step == 4">
          <div class="grid-body">
            <div class="ng-cloak">
              <h4 class="semi-bold">{t}Purchase summary{/t}</h4>
              {include file='invoice/_preview.tpl'}
              <div class="text-center p-t-30" ng-show="payment.nonce">
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
              </div>
              <div class="row m-t-40 ng-cloak" ng-show="payment.nonce">
                <div class="col-sm-4 m-t-15">
                  <button class="btn btn-block btn-loading btn-white" ng-click="previous()" ng-disabled="loading">
                    <h4 class="text-uppercase">
                      {t}Previous{/t}
                    </h4>
                  </button>
                </div>
                <div class="col-sm-4 col-sm-offset-4 m-t-15">
                  <button class="btn btn-block btn-loading btn-success" ng-click="confirm()" ng-disabled="domains.length === 0 || !terms || !client || !payment || loading">
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
        <div class="grid simple ng-hide" ng-show="step == 5">
          <div class="grid-body">
            <div class="ng-cloak p-b-30 p-l-30 p-r-30 p-t-30 text-center">
              <i class="fa fa-heart fa-3x"></i>
              <h3 class="p-b-30">{t}Thank you for your purchase!{/t}</h3>
              <p class="p-b-15">
              {t}Check your email, we have sent you an email with the invoice and purchase details. We will review your purchase and activate your domain.{/t}
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
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="payment-help">
    {include file="store/modal/_payment.tpl"}
  </script>
{/block}
