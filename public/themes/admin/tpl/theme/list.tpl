-{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_market.less,
    @AdminTheme/less/_themes.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="content"}
  <div ng-controller="ThemeListCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=backend_theme_list}">
                  <i class="fa fa-magic"></i>
                  {t}Themes{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks ng-cloak visible-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks dropdown ng-cloak visible-xs">
              <div data-toggle="dropdown">
                <span ng-if="!type || type == 'free'">{t}Free{/t}</span>
                <span ng-if="type == 'exclusive'">{t}Exclusive{/t}</span>
                <span ng-if="type == 'installed'">{t}My themes{/t}</span>
                <span class="caret"></span>
              </div>
              <ul class="dropdown-menu">
                <li ng-click="type = 'free'">
                  <a href="#">
                    <i class="fa fa-check m-r-5"></i>
                    {t}Free{/t}
                  </a>
                </li>
                <li ng-click="type = 'module'">
                  <a href="#">
                    <i class="fa fa-usd m-r-5"></i>
                    {t}Exclusive{/t}
                  </a>
                </li>
                <li ng-click="type = 'pack'">
                  <a href="#">
                    <i class="fa fa-star-o m-r-5"></i>
                    {t}My themes{/t}
                  </a>
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
    <div class="page-navbar filters-navbar hidden-xs">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks module-filter" ng-click="type = 'free'">
              <button class="btn btn-block" ng-class="{ 'btn-success': type == 'free', 'btn-white': type != 'free' }">
                <i class="fa fa-check m-r-5"></i>
                {t}Free{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks module-filter no-padding">
              <button class="btn btn-block" ng-class="{ 'btn-success': type == 'exclusive', 'btn-white': type != 'exclusive' }" ng-click="type = 'exclusive'">
                <i class="fa fa-usd m-r-5"></i>
                {t}Exclusive{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks module-filter">
              <button class="btn btn-block" ng-class="{ 'btn-success': type == 'installed', 'btn-white': type != 'installed' }" ng-click="type = 'installed'">
                <i class="fa fa-star-o m-r-5"></i>
                {t}My themes{/t}
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
      <div>
        <h4 class="ng-cloak" ng-show="!loading  && items.length == 0">{t}No themes available{/t}</h4>
        <div class="row clearfix ng-cloak" ng-show="!loading && items.length > 0">
          <div class="col-vlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-12 module-wrapper" ng-repeat="item in items" ng-include="'item'">
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="item">
      {include file="theme/_item.tpl"}
    </script>
    <script type="text/ng-template" id="modal-details">
      {include file="theme/_details.tpl"}
    </script>
  </div>
{/block}
