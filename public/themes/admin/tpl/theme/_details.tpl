<div class="modal-header">
  <button aria-hidden="true" class="close" data-dismiss="modal" ng-click="close()" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="no-margin uppercase">[% template.item.name %]</h3>
</div>
<div class="modal-body">
  <div uib-carousel active="0" class="carousel-big" ng-if="template.item.images.length > 0">
    <div uib-slide index="$index" ng-repeat="screenshot in template.item.images">
      <img class="img-responsive" ng-src="[% '/asset/zoomcrop,1024,768' + template.item.path + '/' + screenshot %]">
    </div>
  </div>
  <div uib-carousel active="0" class="carousel-big" ng-if="!template.item.images">
    <div uib-slide index="0">
      <img class="img-responsive" src="//placehold.it/1024x768">
    </div>
  </div>
  <div class="row m-t-30">
    <div class="col-xs-6">
      <h5 class="semi-bold text-uppercase">
        {t}Author{/t}
      </h5>
      <p class="m-l-15">
        <a href="https://www.openhost.es" target="_blank">
          Openhost, S.L.
        </a>
      </p>
      <h5 class="semi-bold text-uppercase">
        {t}Exclusive{/t}
      </h5>
      <p class="m-l-15">
        {t}No{/t}
      </p>
    </div>
    <div class="col-xs-6">
      <h5 class="semi-bold text-uppercase">
        {t}Delivery time{/t}
      </h5>
      <p class="m-l-15" ng-if="!template.item.customize">
      {t}Immediately{/t}
      </p>
      <p class="m-l-15" ng-if="template.item.customize">
        <a href="mailto:{$app.container->getParameter('sales_email')}">
          {t}Contact us{/t}
        </a>
      </p>
      <h5 class="semi-bold text-uppercase">
        {t}Widgets{/t}
      </h5>
      <p class="m-l-15" ng-if="!template.item.customize">
        {t}Standard{/t}
      </p>
      <p class="m-l-15" ng-if="template.item.customize">
        <a href="mailto:{$app.container->getParameter('sales_email')}">
          {t}Contact us{/t}
        </a>
      </p>
    </div>
  </div>
  <div class="row m-t-15">
    <div class="col-md-12">
      <a class="p-t-10 pull-left" href="[% template.item.parameters.preview_url %]" ng-click="$event.stopPropagation()" ng-if="!template.isPurchased(template.item)" target="_blank">
        <h5 class="uppercase">
          <i class="fa fa-globe m-r-5"></i>{t}Demo{/t}
        </h5>
      </a>
      <button class="btn btn-price pull-right fly-to-cart" ng-class="{ 'btn-danger': template.isInCart(template.item), 'btn-success': !template.isInCart(template.item) }" ng-click="template.addToCart(template.item);$event.stopPropagation()" ng-disabled="template.isInCart(template.item)" ng-if="!template.isPurchased(template.item)" ng-mouseenter="template.hidePrice = true" ng-mouseleave="template.hidePrice = false">
        <h5 class="semi-bold text-uppercase text-white">
          <span ng-if="!template.hidePrice && !template.isInCart(template.item) && template.getPrice(template.item) && template.getPrice(template.item).value != 0">
            <strong>[% template.getPrice(template.item, template.item.priceType).value %]</strong>
            <span ng-if="['monthly', 'monthly_custom'].indexOf(template.getPrice(template.item, template.item.priceType).type) !== -1">
              €/{t}month{/t}
            </span>
            <span ng-if="['yearly', 'yearly_custom'].indexOf(template.getPrice(template.item, template.item.priceType).type) !== -1">
              €/{t}year{/t}
              </small>
              <span ng-if="['single', 'single_custom'].indexOf(template.getPrice(template.item, template.item.priceType).type) !== -1">
                €
              </span>
            </span>
          </span>
          <span ng-if="!template.hidePrice && !template.isInCart(template.item) && (!template.getPrice(template.item) || template.getPrice(template.item).value === 0)">
            {t}Free{/t}
          </span>
          <span ng-if="template.hidePrice && !template.isInCart(template.item) && !template.isPurchased(template.item)">
            <i class="fa fa-shopping-cart m-r-5"></i>
            {t}Add{/t}
          </span>
          <span ng-if="template.isInCart(template.item)">
            <i class="fa fa-shopping-cart m-r-5"></i>
            {t}In cart{/t}
          </span>
        </h5>
      </button>
      <button class="btn pull-right" ng-class="{ 'btn-white': template.isPurchased(template.item) && !template.isActive(template.item), 'btn-success': template.isActive(template.item) }" ng-click="template.enable(template.item)" ng-disabled="template.isActive(template.item)" ng-if="template.isPurchased(template.item)" style="width: 100px;">
        <h5 class="semi-bold uppercase" ng-if="template.isPurchased(template.item) && !template.isActive(template.item) && !template.item.loading">
          {t}Enable{/t}
        </h5>
        <h5 class="semi-bold text-white uppercase" ng-if="template.isPurchased(template.item) && template.isActive(template.item)">
          {t}Active{/t}
        </h5>
        <h5 class="semi-bold uppercase" ng-if="template.isPurchased(template.item) && !template.isActive(template.item) && template.item.loading">
          {t}Enabling{/t}...
        </h5>
      </button>
      <div class="checkbox m-r-15 p-t-15 pull-right" ng-if="!template.isPurchased(template.item)">
        <input id="custom" ng-change="template.toggleCustom(template.item)" ng-disabled="template.isInCart(template.item) || template.isPurchased(template.item)" ng-model="template.item.customize" type="checkbox">
        <label class="semi-bold" for="custom">
          {t}Custom{/t}
        </label>
      </div>
    </div>
  </div>
</div>
