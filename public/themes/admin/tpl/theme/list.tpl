-{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @AdminTheme/less/_store.less,
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
                <span ng-if="!type || type == 'available'">{t}Available{/t}</span>
                <span ng-if="type == 'exclusive'">{t}Exclusive{/t}</span>
                <span ng-if="type == 'purchased'">{t}My themes{/t}</span>
                <span class="caret"></span>
              </div>
              <ul class="dropdown-menu">
                <li ng-click="type = 'available'">
                  <a href="#">
                    <i class="fa fa-check m-r-5"></i>
                    {t}Available{/t}
                  </a>
                </li>
                <li ng-click="type = 'exclusive'">
                  <a href="#">
                    <i class="fa fa-usd m-r-5"></i>
                    {t}Exclusive{/t}
                  </a>
                </li>
                <li ng-click="type = 'purchased'">
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
                <div class="dropdown-menu dropdown-menu-right">
                  <div class="shopping-cart-placeholder" ng-if="!cart || cart.length == 0">
                    <h5 class="text-center">
                      {t}Your shopping cart is empty{/t}
                    </h5>
                  </div>
                  <div class="shopping-cart-placeholder" ng-if="cart.length > 0">
                    <scrollable>
                      <ul class="cart-list">
                        <li class="clearfix" ng-repeat="item in cart track by $index | orderBy: name">
                          <img class="img-responsive pull-left" ng-if="item.thumbnail" ng-src="/assets/images/store/[%item.thumbnail%]">
                          <img class="img-responsive pull-left" ng-if="item.screenshots.length > 0" ng-src="[% '/asset/scale,1024,768' + item.path + '/' + item.screenshots[0] %]">
                          <img class="img-responsive pull-left" ng-if="!item.thumbnail && (!item.screenshots || item.screenshots.length == 0)" src="http://placehold.it/1024x768">
                          <span class="pull-left">
                            <h5>[% item.name %]</h5>
                            <div class="description" ng-bind-html="item.description[lang]"></div>
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
    <div class="page-navbar filters-navbar hidden-xs">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks module-filter" ng-click="type = 'available'">
              <button class="btn btn-block" ng-class="{ 'btn-success': type == 'available', 'btn-white': type != 'available' }">
                <i class="fa fa-check m-r-5"></i>
                {t}Available{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks module-filter no-padding">
              <button class="btn btn-block" ng-class="{ 'btn-success': type == 'exclusive', 'btn-white': type != 'exclusive' }" ng-click="type = 'exclusive'">
                <i class="fa fa-pencil m-r-5"></i>
                {t}Exclusive{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks module-filter">
              <button class="btn btn-block" ng-class="{ 'btn-success': type == 'purchased', 'btn-white': type != 'purchased' }" ng-click="type = 'purchased'">
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
        <h4 class="text-center ng-cloak" ng-show="!loading  && items.length == 0">
          {t}No themes available{/t}
        </h4>
      </div>
      <div>
        <div class="theme-list-message clearfix ng-cloak" ng-if="!loading && items.length > 0">
          <div class="p-b-15 text-center">
            <div ng-if="type == 'available'">
              <h4>{t}Change the look of your newspaper in just few clicks!{/t}</h4>
              <h5>{t}All our Themes have been designed by media professionals and they are all Mobile Responsive!{/t}</h5>
            </div>
            <div ng-if="type == 'exclusive'">
              <h4>{t}Add customizations and design to create your own theme based on one of out grids or ask us to develop an exclusive and completely new theme{/t}</h4>
              <h5>{t}We are open to any solution you prefer!{/t}</h5>
            </div>
            <div ng-if="type == 'purchased'">
              <h4>{t}Find here your purchased themes and decide which one to use today!{/t}</h4>
              <h5>{t}All purchased Themes are ready for activation{/t}</h5>
            </div>
          </div>
        </div>
        <div class="row clearfix ng-cloak" ng-show="type != 'exclusive' && !loading && items.length > 0">
          <div class="col-vlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-12" ng-repeat="item in items | filter: { name: criteria.name }" ng-include="'item'">
          </div>
        </div>
        <div class="row clearfix ng-cloak" ng-show="type == 'exclusive' && !loading && items.length > 0">
          <div class="col-vlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-12" ng-repeat="item in items | filter: { name: criteria.name }">
            <div class="item-wrapper" ng-include="'exclusive-item'"></div>
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="exclusive-item">
      {include file="theme/_exclusive_item.tpl"}
    </script>
    <script type="text/ng-template" id="item">
      {include file="theme/_item.tpl"}
    </script>
    <script type="text/ng-template" id="modal-details">
      {include file="theme/_details.tpl"}
    </script>
  </div>
{/block}
