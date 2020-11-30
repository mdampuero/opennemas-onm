  <div class="error-body modal-body module-dialog">
    <div class="row">
      <div class="col-xs-4">
        <img class="img-responsive m-b-15" ng-src="[% template.item.images[0] %]">
        <div class="module-icon">
          <i class="fa fa-lg" ng-class="{ 'fa-cube': template.item.type == 'module', 'fa-dropbox': template.item.type == 'pack', 'fa-thumbs-o-up': template.item.type == 'partner', 'fa-support': template.item.type == 'service', 'fa-eye': template.item.type == 'theme'}"></i>
        </div>
      </div>
      <div class="col-xs-8">
        <button aria-hidden="true" class="close" data-dismiss="modal" ng-click="close()" type="button">
          <i class="fa fa-times"></i>
        </button>
        <h3 class="no-margin module-name">
          <strong>[% template.item.name %]</strong>
        </h3>
        <p class="p-t-15 pull-left">{t}by{/t} <span ng-bind-html="template.item.author"></span></p>
        <div class="text-right pull-right p-t-15">
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
          <button class="btn btn-default uppercase m-t-15" ng-click="confirm()" ng-if="!template.inCart && !template.activated && template.item.price.month !== 0" type="button">
            {t}Add to cart{/t}
          </button>
        </div>
      </div>
    </div>
    <hr class="inverted">
    <div class="description" ng-bind-html="template.item.description" ng-if="!template.item.about"></div>
    <div class="description" ng-bind-html="template.item.about" ng-if="template.item.about"></div>
    <hr class="inverted">
    {*<h4 class="text-center uppercase">
      {t}Screenshots and videos{/t}
    </h4>
    <div class="clearfix row">
      <div class="col-xs-6"><img class="img-responsive" src="//placehold.it/300x300" alt=""></div>
      <div class="col-xs-6"><img class="img-responsive" src="//placehold.it/300x300" alt=""></div>
    </div>*}
    <div class="row p-t-15">
      <div class="col-xs-4 text-left hidden">
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half-o"></i>
        <i class="fa fa-star-o"></i>
      </div>
      <div class="pull-right p-r-15 text-right">
        {t}Last updated{/t}: [% template.item.updated | moment %]
      </div>
    </div>
  </div>
