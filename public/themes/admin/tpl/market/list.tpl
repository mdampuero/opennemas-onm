-{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_market.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="content"}
  <div ng-controller="MarketListCtrl" ng-init="plans = {json_encode($plans)|clear_json}">
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
            <li class="quicklinks ng-cloak visible-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks dropdown ng-cloak visible-xs">
              <div data-toggle="dropdown">
                <span ng-if="!type">{t}All{/t}</span>
                <span ng-if="type == 'module'">{t}Modules{/t}</span>
                <span ng-if="type == 'pack'">{t}Packs{/t}</span>
                <span ng-if="type == 'service'">{t}Services{/t}</span>
                <span ng-if="type == 'theme'">{t}Themes{/t}</span>
                <span class="caret"></span>
              </div>
              <ul class="dropdown-menu">
                <li ng-click="type = undefined">
                  <a href="#">{t}All{/t}</a>
                </li>
                <li ng-click="type = 'module'">
                  <a href="#">{t}Modules{/t}</a>
                </li>
                <li ng-click="type = 'pack'">
                  <a href="#">{t}Packs{/t}</a>
                </li>
                <li ng-click="type = 'service'">
                  <a href="#">{t}Services{/t}</a>
                </li>
                <li ng-click="type = 'theme'">
                  <a href="#">{t}Themes{/t}</a>
                </li>
              </ul>
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
                    <button class="btn btn-block btn-white" ng-click="checkout()" ng-disabled="!cart || cart.length == 0">
                      <i class="fa fa-shopping-cart"></i>
                      {t}Checkout{/t}
                    </button>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page-navbar filters-navbar hidden-xs">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks hidden-xs">
              <button class="btn" ng-class="{ 'btn-primary': !type, 'btn-white': type }" ng-click="type = undefined">
                <i class="fa fa-lg fa-th"></i>
                {t}All{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks module-filter" ng-click="type = 'pack'">
              <button class="btn btn-block" ng-class="{ 'btn-primary': type == 'pack', 'btn-white': type != 'pack' }">
                <i class="fa fa-lg fa-dropbox"></i>
                {t}Packs{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks module-filter no-padding">
              <button class="btn btn-block" ng-class="{ 'btn-primary': type == 'module', 'btn-white': type != 'module' }" ng-click="type = 'module'">
                <i class="fa fa-lg fa-cube"></i>
                {t}Modules{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks module-filter">
              <button class="btn btn-block" ng-class="{ 'btn-primary': type == 'theme', 'btn-white': type != 'theme' }" ng-click="type = 'theme'">
                <i class="fa fa-lg fa-eye"></i>
                {t}Themes{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks module-filter no-padding">
              <button class="btn btn-block" ng-class="{ 'btn-primary': type == 'service', 'btn-white': type != 'service' }" ng-click="type = 'service'">
                <i class="fa fa-lg fa-support"></i>
                {t}Services{/t}
              </button>
            </li>
          </ul>
          <ul class="hidden-xs nav quick-section pull-right">
            <li class="quicklinks">
              <div class="input-group" style="width: 200px">
                <input name="name" ng-model="criteria.name" placeholder="{t}Search by name{/t}" type="text"/>
                <span class="input-group-addon">
                  <span class="fa fa-search fa-lg"></span>
                </span>
              </div>
            </li>
            {*<li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="pagination.epp">
                <ui-select-match>
                  <strong>{t}View{/t}:</strong> [% $select.selected %]
                </ui-select-match>
                <ui-select-choices repeat="item in views  | filter: $select.search | orderBy: name">
                  <div ng-bind-html="item | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>*}
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!loading && items.length == 0">
        <div class="center">
          <h4>{t}Unable to find any module that matches your search.{/t}</h4>
          <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
        </div>
      </div>
      <div class="infinite-row" ng-if="type != 'module'">
        <h4 class="ng-cloak" ng-show="!loading  && allActivated(purchased)">{t}No items available to purchase{/t}</h4>
        <h4 class="ng-cloak" ng-show="!loading">{t}Available{/t}</h4>
        <div class="infinite-row clearfix ng-cloak" ng-show="!loading && !allActivated(available) && available && available.length > 0">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 module-wrapper" ng-repeat="item in available = (items | filter: criteria | filter: { type: type } | orderBy: name)" ng-if="!isActivated(item)" ng-include="'item'">
          </div>
        </div>
        <h4 class="ng-cloak" ng-show="!loading && !allDeactivated(purchased)">{t}Purchased{/t}</h4>
        <div class="infinite-row clearfix ng-cloak" ng-show="!loading && purchased && purchased.length > 0">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 module-wrapper" ng-repeat="item in purchased = (items | filter: criteria | filter: { type: type } | orderBy: name)" ng-if="isActivated(item)" ng-include="'item'">
          </div>
        </div>
      </div>
      <div ng-if="type == 'module'">
        <h4 class="ng-cloak" ng-show="!loading && allActivated(available['PROFESSIONAL']) && allActivated(available['SILVER']) && allActivated(available['GOLD']) && allActivated(available['OTHER'])">{t}No items available to purchase{/t}</h4>
        <div class="infinite-row" ng-repeat="plan in plans">
          <h3 class="ng-cloak" ng-show="!loading && !allActivated(available[plan.id])">[% plan.name %]</h3>
          <h4 class="ng-cloak" ng-show="!loading && !allActivated(available[plan.id])">{t}Available{/t}</h4>
          <div class="infinite-row clearfix ng-cloak" ng-show="!loading && !allActivated(available[plan.id]) && available[plan.id] && available[plan.id].length > 0">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 module-wrapper" ng-repeat="item in available[plan.id] = (items | filter: criteria | filter: { type: type } | filter: { plan: plan.id } | orderBy: name)" ng-if="!isActivated(item)" ng-include="'item'">
            </div>
          </div>
          <!-- <h4 class="ng-cloak" ng-show="!loading  && !allDeactivated(purchased[plan.id])">{t}Purchased{/t}</h4>
          <div class="infinite-row clearfix ng-cloak" ng-show="!loading && purchased[plan.id] && purchased[plan.id].length > 0">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 module-wrapper" ng-repeat="item in purchased[plan.id] = (items | filter: criteria | filter: { type: type } | filter: { plan: plan.id } | orderBy: name)" ng-if="isActivated(item)" ng-include="'item'">
            </div>
          </div> -->
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="item">
      <div class="grid simple module-grid" ng-click="xsOnly($event, showDetails, item);">
        <div class="grid-body no-padding">
          <div class="clearfix">
            <div class="col-xs-4 col-sm-4 module-image-wrapper" ng-click="showDetails(item)">
              <img class="module-image pull-left" ng-src="/assets/images/market/[%item.thumbnail%]">
              <div class="module-icon">
                <i class="fa fa-lg" ng-class="{ 'fa-cube': item.type == 'module', 'fa-dropbox': item.type == 'pack', 'fa-support': item.type == 'service', 'fa-eye': item.type == 'theme'}"></i>
              </div>
            </div>
            <div class="module-body col-xs-8 col-sm-8">
              <div class="module-info-wrapper">
                <h5 class="name pointer" ng-click="showDetails(item)">
                  <strong>[% item.name %]</strong>
                </h5>
                <div class="description" ng-click="showDetails(item)" ng-bind-html="item.description">
                </div>
              </div>
              <div class="text-right price">
                <h3 class="no-margin" ng-show="item.price">
                  <span ng-if="item.price.month">
                    <strong>[% item.price.month %]</strong>
                    <small> € / {t}month{/t}</small>
                  </span>
                  <span ng-if="!item.price.month && item.price.single">
                    <strong>[% item.price.single %]</strong>
                    <small> € </small>
                  </span>
                  <span ng-if="item.price.month == 0"><strong>{t}Free{/t}</strong></span>
                </h3>
              </div>
            </div>
          </div>
          <div class="module-tools clearfix">
            <div class="col-xs-12 col-sm-6">
              <button class="more-info btn btn-block btn-link" ng-click="showDetails(item);$event.stopPropagation()">
                {t}More info{/t}
            </div>
            <div class="col-xs-12 col-sm-6">
              <button class="add-to-cart btn btn-block" ng-class="{ 'btn-success': !isActivated(item) && !isInCart(item), 'btn-default': isActivated(item) || isInCart(item) }" ng-click="addToCart(item);$event.stopPropagation()" ng-disabled="isInCart(item) || isActivated(item)">
                <i class="fa fa-plus m-r-5" ng-if="!isActivated(item) && !isInCart(item)"></i>
                <span ng-if="!isActivated(item) && !isInCart(item)">{t}Add to cart{/t}</span>
                <span ng-if="!isActivated(item) && isInCart(item)">{t}Added to cart{/t}</span>
                <span ng-if="isActivated(item)">{t}Purchased{/t}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </script>
    <script type="text/ng-template" id="modal-checkout">
      {include file="market/modal/_checkout.tpl"}
    </script>
    <script type="text/ng-template" id="modal-details">
      {include file="market/modal/_details.tpl"}
    </script>
  </div>
{/block}
