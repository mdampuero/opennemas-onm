{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_market.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="content"}
<div ng-controller="MarketCheckoutCtrl"{if !empty($billing)} ng-init="billing = {json_encode($billing)|clear_json}"{/if}>
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
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <h5>{t}Checkout{/t}</h5>
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
    <div class="content">
      <div class="row ng-cloak">
        <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 p-r-30">
          <div class="grid simple">
            <div class="grid-body">
              <h4>{t}Cart{/t}</h4>
              <ul class="cart-list">
                <li class="clearfix" ng-repeat="item in cart">
                  <img class="img-responsive pull-left" ng-src="/assets/images/market/[%item.thumbnail%]">
                  <div class="p-l-100">
                    <h5>[% item.name %]</h5>
                    <p class="description">[% item.description %]</p>
                    <div class="text-right p-r-15">
                      <div class="price">
                        <h3 class="no-margin">
                          <strong>[% item.price.month %]</strong><small>€ / {t}month{/t}</small>
                        </h3>
                      </div>
                    </div>
                  </div>
                  <i class="fa fa-times pull-left" ng-click="removeFromCart(item)"></i>
                </li>
              </ul>
              <hr class="m-r-15">
              <div class="p-r-15 text-right">
                <h3 class="no-margin">
                  <span class="p-r-15 uppercase">{t}Total{/t}:</span>
                  <strong>[% total %]</strong><small>€ / {t}month{/t}</small>
                </h3>
              </div>
              <div class="billing-info">
                <h4>
                  {t}Billing Info{/t}
                  <button class="btn btn-link" ng-click="edit = 1" ng-show="!edit">({t}Edit{/t})</button>
                </h4>
                <div class="ng-cloak p-b-30" ng-show="edit || !billing">
                  <h5 class="m-t-20">{t}Contact information{/t}</h5>
                  <div class="row">
                    <div class="form-group col-sm-6">
                      <input class="form-control" id="contact-name" ng-model="billing.name" placeholder="{t}Contact name{/t}" required="required" type="text">
                    </div>
                    <div class="form-group col-sm-6">
                      <input class="form-control" id="company-name" ng-model="billing.company" placeholder="{t}Company name{/t}" required="required" type="text">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-sm-6">
                      <input class="form-control" id="contact-email" ng-model="billing.email" placeholder="{t}Email{/t}" required="required" type="text">
                    </div>
                    <div class="form-group col-sm-6">
                      <input class="form-control" id="phone" ng-model="billing.phone" placeholder="{t}Phone number{/t}" required="required" type="text">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-sm-6">
                      <label for="vat"></label>
                      <input class="form-control" id="vat" ng-model="billing.vat" placeholder="{t}VAT{/t}" required="required" type="text">
                    </div>
                  </div>
                  <h5 class="m-t-20">{t}Address{/t}</h5>
                  <div class="row">
                    <div class="form-group col-sm-8">
                      <input class="form-control" id="address" ng-model="billing.address" placeholder="{t}Address{/t}" required="required" type="text">
                    </div>
                    <div class="form-group col-sm-4">
                      <input class="form-control" id="postal-code" ng-model="billing.postal_code" placeholder="{t}Postal code{/t}" required="required" type="text">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-sm-4">
                      <input class="form-control" id="city" ng-model="billing.city" placeholder="{t}City{/t}" required="required" type="text">
                    </div>
                    <div class="form-group col-sm-4">
                      <input class="form-control" id="state" ng-model="billing.state" placeholder="{t}State{/t}" required="required" type="text">
                    </div>
                    <div class="form-group col-sm-4">
                      <input class="form-control" id="country" ng-model="billing.country" placeholder="{t}Country{/t}" required="required" type="text">
                    </div>
                  </div>
                </div>
                <div class="ng-cloak p-b-30" ng-show="!edit">
                  <div class="p-b-10">
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
                  <div>
                    <h5 class="m-t-20">{t}Address{/t}</h5>
                    <p>[% billing.address %]</p>
                    <p>[% billing.postal_code %], [% billing.city %], [% billing.state %]</p>
                    <p>[% billing.country %]</p>
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
                <button class="btn btn-large btn-success text-center" ng-click="confirm()">
                  {t}Confirm{/t}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-checkout">
    <div class="modal-body">
      <div class="text-center">
        <i class="fa fa-heart fa-3x"></i>
        <h3 class="p-b-30">{t}Nice{/t}!</h3>
        <p class="p-b-15">
        {t}Thank you! We have received your request and will get back to you as soon as possible. You will receive a confirmation e-mail too.{/t}
        </p>
        <p class="p-b-30">
        {t escape=off}Meanwhile, you can go to your <a href="{url name='admin_client_info_page'}">My newspaper</a> and check your active features, navigate our help to familiarize with your news tool, or check some awesome videos to see how easy are to manage Opennemas.{/t}
        </p>
        <p class="p-b-15">
        {t}
        Oh!, it would be a good time to share with your friends your newspaper's improvements
        {/t}
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
    <div class="modal-footer">
      <button class="btn btn-success uppercase" ng-click="confirm()" type="button">
        <i class="fa fa-circle-o-notch fa-spin" ng-show="saving"></i>
        {t}Confirm{/t}
      </button>
    </div>
  </script>
{/block}
