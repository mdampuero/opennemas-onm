  <div class="error-body modal-body">
    <div class="row">
      <div class="col-xs-4">
        <img ng-if="template.item.type == 'module'" class="img-responsive m-b-15" ng-src="/assets/images/market/generic-modules.jpg">
        <img ng-if="template.item.type == 'pack'" class="img-responsive m-b-15" ng-src="/assets/images/market/generic-pack.jpg">
        <img ng-if="template.item.type == 'service'" class="img-responsive m-b-15" ng-src="/assets/images/market/generic-service-support.jpg">
        <img ng-if="template.item.type == 'theme'" class="img-responsive m-b-15" ng-src="/assets/images/market/generic-pack.jpg">
        <div class="module-icon">
          <i class="fa fa-dropbox fa-lg"></i>
        </div>
      </div>
      <div class="col-xs-8">
        <button aria-hidden="true" class="close" data-dismiss="modal" ng-click="close()" type="button">
          <i class="fa fa-times"></i>
        </button>
        <h3 class="no-margin">
          <strong>[% template.item.name %]</strong>
        </h3>
        <p class="p-t-15">{t}Creator:{/t} [% template.item.author ? template.item.author : 'Opennemas' %]</p>
        <div class="text-right p-t-15">
          <div class="price" ng-if="template.item.price.month">
            <h3 class="no-margin">
              <strong>[% template.item.price.month %]</strong>
              <small>€</small>
            </h3>
            <h5 class="no-margin">{t}month{/t}</h5>
          </div>
          <div class="price p-t-15" ng-if="template.item.price.usage">
            <h3 class="no-margin">
              <strong>[% template.item.price.usage.price %]</strong>
              <small>€</small>
            </h3>
            <h5 class="no-margin">[% template.item.price.usage.items %] [% template.item.price.usage.type %]</h5>
          </div>
          <div class="btn btn-default uppercase m-t-15" ng-disabled="true" ng-if="template.activated">
            {t}Purchased{/t}
          </div>
          <button class="btn btn-default uppercase m-t-15" ng-click="confirm()" ng-if="!template.inCart && !template.activated " type="button">
            {t}Add to cart{/t}
          </button>
        </div>
      </div>
    </div>
    <hr class="inverted">
    <div class="description" ng-bind-html="template.item.description" ng-if="!template.item.long_description"></div>
    <div class="description" ng-bind-html="template.item.long_description" ng-if="template.item.long_description"></div>
    <hr class="inverted">
    {*<h4 class="text-center uppercase">
      {t}Screenshots and videos{/t}
    </h4>
    <div class="clearfix infinite-row">
      <div class="col-xs-6"><img class="img-responsive" src="http://placehold.it/300x300" alt=""></div>
      <div class="col-xs-6"><img class="img-responsive" src="http://placehold.it/300x300" alt=""></div>
    </div>*}
    <div class="row p-t-15">
      <div class="col-xs-4 text-left">
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half-o"></i>
        <i class="fa fa-star-o"></i>
      </div>
      <div class="col-xs-8 text-right">
        {t}Last updated{/t}: [% template.item.updated | moment %]
      </div>
    </div>
  </div>
