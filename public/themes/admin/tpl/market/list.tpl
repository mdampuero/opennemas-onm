{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_market.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="content"}
  <div ng-controller="MarketListCtrl">
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
                          <img ng-if="item.type == 'module'" class="img-responsive pull-left" ng-src="/assets/images/market/generic-modules.jpg">
                          <img ng-if="item.type == 'pack'" class="img-responsive pull-left" ng-src="/assets/images/market/generic-pack.jpg">
                          <img ng-if="item.type == 'service'" class="img-responsive pull-left" ng-src="/assets/images/market/generic-service-support.jpg">
                          <img ng-if="item.type == 'theme'" class="img-responsive pull-left" ng-src="/assets/images/market/generic-pack.jpg">
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
    <div class="page-navbar filters-navbar">
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
            {*<li class="quicklinks module-filter">
              <button class="btn btn-block" ng-class="{ 'btn-primary': type == 'theme', 'btn-white': type != 'theme' }" ng-click="type = 'theme'">
                <i class="fa fa-lg fa-eye"></i>
                {t}Themes{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>*}
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
      {render_messages}
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!loading && items.length == 0">
        <div class="center">
          <h4>{t}Unable to find any menu that matches your search.{/t}</h4>
          <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
        </div>
      </div>
      <h3 class="ng-cloak" ng-show="!loading">{t}Available{/t}</h3>
      <div class="infinite-row clearfix ng-cloak" ng-show="!loading && !allActivated(available) && available && available.length > 0">
        <div class="col-md-3 col-sm-4 col-xs-12 module-wrapper" ng-repeat="item in available = (items | filter: criteria | filter: { type: type } | orderBy: name)" ng-if="!isActivated(item)" ng-include="'item'">
        </div>
      </div>
      <div class="text-center ng-cloak" ng-show="!loading && allActivated(available)">
        <h4>{t}No items available to purchase{/t}</h4>
      </div>
      <h3 class="ng-cloak" ng-show="!loading">{t}Purchased{/t}</h3>
      <div class="infinite-row clearfix ng-cloak" ng-show="!loading && purchased && purchased.length > 0">
        <div class="col-md-3 col-sm-4 col-xs-12 module-wrapper" ng-repeat="item in purchased = (items | filter: criteria | filter: { type: type } | orderBy: name)" ng-if="isActivated(item)" ng-include="'item'">
        </div>
      </div>
      <div class="text-center ng-cloak" ng-show="!loading && allDeactivated(purchased)">
        <h4>{t}No items purchased{/t}</h4>
      </div>
    </div>
    <script type="text/ng-template" id="item">
      <div class="grid simple module-grid" ng-click="xsOnly($event, showDetails, item);">
        <div class="grid-body no-padding">
          <div ng-if="item.type == 'module'" class="module-header pointer" style="background-image: url(/assets/images/market/generic-modules.jpg);">
            <h5 class="name pointer" ng-click="showDetails(item)">
              <strong>[% item.name %]</strong>
            </h5>
          </div>
          <div ng-if="item.type == 'pack'" class="module-header pointer" style="background-image: url(/assets/images/market/generic-pack.jpg);">
            <h5 class="name pointer" ng-click="showDetails(item)">
              <strong>[% item.name %]</strong>
            </h5>
          </div>
          <div ng-if="item.type == 'service'" class="module-header pointer" style="background-image: url(/assets/images/market/generic-service-support.jpg);">
            <h5 class="name pointer" ng-click="showDetails(item)">
              <strong>[% item.name %]</strong>
            </h5>
          </div>
          <div ng-if="item.type == 'theme'" class="module-header pointer" style="background-image: url(/assets/images/market/generic-pack.jpg);">
            <h5 class="name pointer" ng-click="showDetails(item)">
              <strong>[% item.name %]</strong>
            </h5>
          </div>
          <div class="module-body">
            <div class="module-icon">
              <i class="fa fa-lg" ng-class="{ 'fa-cube': item.type == 'module', 'fa-dropbox': item.type == 'pack', 'fa-support': item.type == 'service', 'fa-eye': item.type == 'theme'}"></i>
            </div>
            <p class="description">
              [% item.description | limitTo: 140 %]
              [% item.description.length > 140 ? '...' : '' %]
            </p>
            <div class="text-right p-t-5">
              <div class="price">
                <h3 class="no-margin" ng-show="item.price">
                  <span ng-if="item.price.month !== 0"><strong>[% item.price.month %]</strong><small> € / {t}month{/t}</small></span>
                  <span ng-if="item.price.month == 0"><strong><strong>{t}Free{/t}</strong></span>
                </h3>
              </div>
            </div>
            <hr class="hidden-xs">
            <button class="btn btn-block btn-link hidden-xs" ng-click="showDetails(item);$event.stopPropagation()">
              {t}More info{/t}
            </button>
            <button class="btn btn-block hidden-xs" ng-class="{ 'btn-success': !isActivated(item), 'btn-default': isActivated(item) }" ng-click="addToCart(item);$event.stopPropagation()" ng-disabled="isInCart(item) || isActivated(item)">
              <i class="fa fa-plus m-r-5" ng-if="!isActivated(item)"></i>
              <span ng-if="!isActivated(item)">{t}Add to cart{/t}</span>
              <span ng-if="isActivated(item)">{t}Purchased{/t}</span>
            </button>
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
    <script type="text/ng-template" id="modal-success">
      {include file="market/modal/_success.tpl"}
    </script>
  </div>
{/block}
