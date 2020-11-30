<div class="grid simple module-grid" ng-click="xsOnly($event, showDetails, item);">
  <div class="grid-body no-padding">
    <div class="clearfix">
      <div class="col-xs-4 col-sm-4 module-image-wrapper" ng-click="showDetails(item)">
        <img class="module-image pull-left" ng-src="[% item.images[0] %]">
        <div class="module-icon">
          <i class="fa fa-lg" ng-class="{ 'fa-cube': item.type == 'module', 'fa-dropbox': item.type == 'pack', 'fa-thumbs-o-up': item.type == 'partner', 'fa-support': item.type == 'service', 'fa-eye': item.type == 'theme'}"></i>
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
            <div ng-repeat="price in item.price">
              <span ng-if="price.value && price.type !== 'item'">
                <strong>[% price.value %]</strong>
                <small ng-if="price.type === 'monthly'">€/{t}month{/t}</small>
                <small ng-if="price.type === 'yearly'">€/{t}year{/t}</small>
                <small ng-if="price.type === 'single'">€</small>
                <small ng-if="price.type === 'item'">€/{t}item{/t}</small>
              </span>
              <span ng-if="price.value == 0"><strong>{t}Free{/t}</strong></span>
            </div>
          </h3>
        </div>
      </div>
    </div>
    <div class="module-tools row clearfix">
      <div class="col-sm-6">
        <button class="more-info btn btn-block btn-link" ng-click="showDetails(item);$event.stopPropagation()">
          {t}More info{/t}
      </div>
      <div class="col-xs-12 col-sm-6">
        <button class="add-to-cart fly-to-cart btn btn-block" ng-class="{ 'btn-success': !isActivated(item) && !isInCart(item), 'btn-default': isActivated(item) || isInCart(item) }" ng-click="addToCart(item);$event.stopPropagation()" ng-disabled="isInCart(item) || isActivated(item)">
          <i class="fa fa-plus m-r-5" ng-if="!isActivated(item) && !isInCart(item)"></i>
          <span ng-if="!isActivated(item) && !isInCart(item)">{t}Add to cart{/t}</span>
          <span ng-if="!isActivated(item) && isInCart(item)">{t}Added to cart{/t}</span>
          <span ng-if="isActivated(item)">{t}Purchased{/t}</span>
        </button>
      </div>
    </div>
  </div>
</div>
