<div class="grid grid-hover pointer simple" ng-class="{ 'vertical green': isActive(item) }" ng-click="xsOnly($event, showDetails, item);">
  <div class="grid-title no-border no-padding"></div>
  <div class="grid-body" ng-click="showDetails(item);$event.stopPropagation()">
    <div class="p-b-50" ng-click="$event.stopPropagation()">
      <div uib-carousel active="a"  class="carousel-minimal" ng-if="item.images.length > 0">
        <div uib-slide ng-repeat="screenshot in item.images track by $index" index="$index">
          <img class="img-responsive" ng-click="showDetails(item)" ng-src="[% '/asset/zoomcrop,1024,768' + item.path + '/' + screenshot %]">
        </div>
      </div>
      <img class="img-responsive" ng-click="showDetails(item)" ng-if="!item.images" src="//placehold.it/1024x768">
    </div>
    <div class="clearfix p-t-5 p-b-10">
      <h4 class="uppercase pull-left">[% item.name %]</h4>
    </div>
    <div>
      <a ng-if="type !== 'purchased'" class="m-t-10 pull-left" href="[% item.parameters.preview_url %]" ng-click="$event.stopPropagation()" target="_blank">
        <h5 class="uppercase">
          <i class="fa fa-globe"></i>
          {t}Demo{/t}
        </h5>
      </a>
      <a ng-if="type === 'purchased' && item.parameters.guideline_file !== undefined" class="m-t-10 pull-left" ng-href="[% item.path %][% item.parameters.guideline_file %]" ng-click="$event.stopPropagation()" target="_blank" title="{t}Download the style guide for this theme{/t}">
        <h5 class="uppercase">
          <i class="fa fa-download"></i>
          {t}Download style guide{/t}
        </h5>
      </a>
      <button class="btn btn-price fly-to-cart pull-right" ng-class="{ 'btn-danger': isInCart(item), 'btn-success': !isInCart(item) }" ng-click="addToCart(item);$event.stopPropagation()" ng-disabled="isInCart(item)" ng-if="!isPurchased(item)" ng-mouseenter="item.hidePrice = !item.hidePrice" ng-mouseleave="item.hidePrice = !item.hidePrice">
        <h5 class="semi-bold text-uppercase text-white">
          <span ng-if="!item.hidePrice && !isInCart(item) && getPrice(item) && getPrice(item).value != 0">
            <strong>[% getPrice(item, item.priceType).value %]</strong>
            <span ng-if="['monthly', 'monthly_custom'].indexOf(getPrice(item, item.priceType).type) !== -1">
              €/{t}month{/t}
            </span>
            <span ng-if="['yearly', 'yearly_custom'].indexOf(getPrice(item, item.priceType).type) !== -1">
              €/{t}year{/t}
              </small>
              <span ng-if="['single', 'single_custom'].indexOf(getPrice(item, item.priceType).type) !== -1">
                €
              </span>
            </span>
          </span>
          <span ng-if="!item.hidePrice && !isInCart(item) && (!getPrice(item) || getPrice(item).value === 0)">
            {t}Free{/t}
          </span>
          <span ng-if="item.hidePrice && !isInCart(item) && !isPurchased(item)">
            <i class="fa fa-shopping-cart m-r-5"></i>
            {t}Add{/t}
          </span>
          <span ng-if="isInCart(item)">
            <i class="fa fa-shopping-cart m-r-5"></i>
            {t}In cart{/t}
          </span>
        </h5>
      </button>
      <button class="btn pull-right" ng-class="{ 'btn-white': isPurchased(item) && !isActive(item), 'btn-success': isActive(item) }" ng-click="$event.stopPropagation();enable(item)" ng-disabled="isActive(item)" ng-if="isPurchased(item)" style="width: 100px;">
        <h5 class="semi-bold uppercase" ng-if="!isActive(item) && !item.loading">{t}Enable{/t}</h5>
        <h5 class="semi-bold uppercase" ng-if="isPurchased(item) && !isActive(item) && item.loading">{t}Enabling{/t}...</h5>
        <h5 class="semi-bold text-white uppercase" ng-if="isPurchased(item) && isActive(item)">{t}Active{/t}</h5>
      </button>
    </div>
  </div>
</div>
