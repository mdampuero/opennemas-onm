<div class="grid grid-hover pointer simple" ng-class="{ 'vertical green': isActive(item) }" ng-click="xsOnly($event, showDetails, item);">
  <div class="grid-title no-border no-padding"></div>
  <div class="grid-body" ng-click="showDetails(item);$event.stopPropagation()">
    <div class="p-b-50" ng-click="$event.stopPropagation()">
      <carousel class="carousel-minimal" ng-if="item.screenshots.length > 0">
        <slide ng-repeat="screenshot in item.screenshots">
          <img class="img-responsive" ng-click="showDetails(item)" ng-src="[% '/asset/scale,1024,768' + item.path + '/' + screenshot %]">
        </slide>
      </carousel>
      <carousel class="carousel-minimal" ng-if="!item.screenshots">
        <slide>
          <img class="img-responsive" ng-click="showDetails(item)" src="http://placehold.it/1024x768">
        </slide>
        <slide>
          <img class="img-responsive" ng-click="showDetails(item)" src="http://placehold.it/1024x768">
        </slide>
        <slide>
          <img class="img-responsive" ng-click="showDetails(item)" src="http://placehold.it/1024x768">
        </slide>
      </carousel>
    </div>
    <div class="clearfix p-t-5 p-b-10">
      <h4 class="uppercase pull-left">[% item.name %]</h4>

      <h4 class="text-right pull-right" ng-if="type !== 'purchased'">
        <span ng-if="item.price.month">
          <strong>[% item.price.month %]</strong>
          <small>€ / {t}month{/t}</small>
        </span>
        <span ng-if="!item.price.month && item.price.single">
          <strong>[% item.price.single %]</strong>
          <small>€</small>
        </span>
        <span class="semi-bold uppercase" ng-if="!isInCart(item) && !isPurchased(item) && (!item.price || item.price.month == 0)">
          {t}Free{/t}
        </span>
      </h4>
    </div>
    <div>
     <a ng-if="type !== 'purchased'" class="m-t-10 pull-left" href="[% item.parameters.preview_url %]" ng-click="$event.stopPropagation()" target="_blank" title="{t}Open live demo in a new tab{/t}">
        <h5 class="uppercase">
          <i class="fa fa-globe"></i>
          {t}Live demo{/t}
        </h5>
      </a>
      <button class="btn fly-to-cart pull-right" ng-class="{ 'btn-danger': isInCart(item), 'btn-success': !isInCart(item) }" ng-click="addToCart(item);$event.stopPropagation()" ng-disabled="isInCart(item)" ng-if="!isPurchased(item)">
        <h5>
          <span class="semi-bold text-white uppercase" ng-if="!isInCart(item) && !isPurchased(item)">
            <i class="fa fa-shopping-cart m-r-5"></i>
            {t}Add{/t}
          </span>
          <span class="semi-bold text-white uppercase" ng-if="isInCart(item)">
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
