{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="@AdminTheme/less/_store.less,
    @AdminTheme/less/_checkout.less" filters="cssrewrite,less"}
  {/stylesheets}
{/block}

{block name="content"}
<div ng-controller="StoreCheckoutCtrl" ng-init="{if !empty($client)}client = {json_encode($client)|clear_json}; {/if}countries = {json_encode($countries)|clear_json};taxes = {json_encode($taxes)|clear_json}">
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
              <div class="dropdown-menu dropdown-menu-right">
                <div class="shopping-cart-placeholder" ng-if="!cart || cart.length == 0">
                  <h5 class="text-center">
                    {t}Your shopping cart is empty{/t}
                  </h5>
                </div>
                <div class="shopping-cart-placeholder" ng-if="cart.length > 0">
                  <scrollable>
                  <ul class="cart-list">
                    <li class="clearfix" ng-repeat="item in cart | orderBy: name">
                      <img class="img-responsive pull-left" ng-src="[% '/asset/scale,300,300' + item.path + '/' + item.images[0] %]">
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
      <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 p-r-30" ng-show="step != 4 && (!cart || cart.length == 0)">
        <div class="text-center">
          <h1><i class="fa fa-shopping-cart"></i></h1>
          <h3>{t}Your shopping cart is empty{/t}</h3>
          {capture name="store_url"}{url name='admin_store_list'}{/capture}
          <h4>{t escape=off 1=$smarty.capture.store_url}Return to <a href="%1">store</a> and try again{/t}</h4>
        </div>
      </div>
      <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1" ng-show="(cart && cart.length > 0) || step == 4">
        <div class="form-wizard-steps clearfix m-b-15 ng-cloak">
          <ul class="wizard-steps form-wizard">
            <li class="text-center" ng-class="{ 'active': step == 1 }">
              <span class="step">1</span>
              <h5 class="m-t-15">{t}Cart{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 2 }" ng-if="!clientValid">
              <span class="step">2</span>
              <h5 class="m-t-15">{t}Billing information{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 3 }">
              <span class="step">[% client ? '3' : '4' %]</span>
              <h5 class="m-t-15">{t}Check{/t}</h5>
            </li>
            <li class="text-center" ng-class="{ 'active': step == 4 }">
              <span class="step">[% client ? '4' : '5' %]</span>
              <h5 class="m-t-15">{t}Finish{/t}</h5>
            </li>
          </ul>
        </div>
        <div class="fake-form-wizard-steps ng-cloak">
          <div class="fake-wizard-steps text-center fake-wizard-steps-active-[% step %]">
            <div class="step">
              <i class="fa fa-truck fa-flip-horizontal fa-lg"></i>
            </div>
          </div>
        </div>
        <div class="grid simple ng-hide" ng-show="step == 1">
          <div class="grid-body">
            <h4 class="semi-bold">{t}Cart{/t}</h4>
            {include file="store/_cart.tpl"}
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
        <div class="grid simple ng-hide" ng-show="step == 2">
          <div class="grid-body">
            <div class="ng-cloak">
              <h4 class="m-b-30 semi-bold">{t}Billing information{/t}</h4>
              <div ng-if="!client || !client.id">
                {include file='client/form.tpl'}
              </div>
              <div ng-if="client && client.id">
                <h4 class="semi-bold">[% client.last_name %], [% client.first_name %]</h4>
                <address>
                  <strong ng-if="client.company">[% client.company %]</strong><br>
                  [% client.address %]<br>
                  [% client.postal_code %], [% client.city %], [% client.state %]<br>
                  [% countries[client.country] %]<br>
                </address>
                <div class="row m-t-50 ng-cloak">
                  <div class="col-sm-4 m-t-15">
                    <button class="btn btn-block btn-loading btn-white" ng-click="previous()" ng-disabled="loading">
                      <h4 class="text-uppercase">
                        {t}Previous{/t}
                      </h4>
                    </button>
                  </div>
                  <div class="col-sm-4 col-sm-offset-4 m-t-15">
                    <button class="btn btn-block btn-loading btn-success" ng-click="next()" ng-disabled="loading">
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
              {include file='invoice/_preview.tpl'}
              <div class="text-center p-t-30">
                <div class="form-group">
                  <div class="checkbox">
                    <input id="terms" name="terms" ng-model="terms" type="checkbox">
                    <label class="no-margin text-left" for="terms">
                      {t escape=off}I have read and accept the <a href="http://help.opennemas.com/knowledgebase/articles/235348-condiciones-del-servicio-de-opennemas" target="_blank">Terms of Service</a>{/t}
                    </label>
                  </div>
                </div>
              </div>
              <div class="row m-t-40 ng-cloak">
                <div class="col-sm-4 m-t-15">
                  <button class="btn btn-block btn-loading btn-white" ng-click="previous()" ng-disabled="loading">
                    <h4 class="text-uppercase">
                      {t}Previous{/t}
                    </h4>
                  </button>
                </div>
                <div class="col-sm-4 col-sm-offset-4 m-t-15">
                  <button class="btn btn-block btn-loading btn-success" ng-click="confirm()" ng-disabled="cart.length === 0 || !terms || !client || loading">
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
        <div class="grid simple ng-hide" ng-show="step == 4">
          <div class="grid-body text-center">
            <div class="p-b-30 p-l-30 p-r-30 p-t-30 text-center">
              <i class="fa fa-heart fa-3x"></i>
              <h3 class="p-b-30 ">{t}Thank you for your purchase request!{/t}</h3>
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
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{/block}
