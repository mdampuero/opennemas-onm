<div ng-controller="CartCtrl">
  <ul class="cart-list cart-list-big ng-cloak">
    <li ng-repeat="item in cart track by $index">
      <img class="img-responsive pull-left" ng-if="item.images && item.images.length > 0" ng-src="[% '/asset/scale,300,300' + item.path + '/' + item.images[0] %]">
      <div ng-class="{ 'p-l-15': !item.images || item.images.length === 0, 'p-l-100': item.images && item.images.length > 0 }">
        <h5 class="no-overflow" ng-bind-html="item.name"></h5>
        <div class="clearfix">
          <p class="description pull-left no-margin" ng-bind-html="item.description" ng-if="item.name.indexOf(item.description) === -1"></p>
          <div class="text-right p-r-15 p-b-15">
            <div class="price">
              <h4 class="no-margin">
                <strong>[% getPrice($index).value | number : 2 %]</strong>
                <small ng-show="getPrice($index).type === 'yearly'">€/{t}year{/t}</small>
                <small ng-show="getPrice($index).type === 'monthly'">€/{t}month{/t}</small>
              </h4>
            </div>
          </div>
        </div>
      </div>
      <i class="fa fa-times pull-left" ng-click="cart.splice($index, 1)"></i>
    </li>
  </ul>
  <div class="ng-cloak text-right">
    <div class="p-r-30 p-t-10">
      <h4>
        <span class="m-r-30 uppercase">{t}Total{/t}:</span>
        <strong>[% getSubTotal() | number : 2 %]</strong>
        <small>€</small>
      </h4>
    </div>
  </div>
</div>
