<div class="modal-header">
  <button aria-hidden="true" class="close" data-dismiss="modal" ng-click="close()" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="no-margin uppercase">[% template.item.name %]</h3>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-sm-7">
      <div class="p-r-15">
        <h5 class="text-center uppercase">{t}Screenshots{/t}</h5>
        <carousel>
          <slide>
          <img class="img-responsive" src="http://placehold.it/640x480">
          </slide>
          <slide>
          <img class="img-responsive" src="http://placehold.it/640x480">
          </slide>
          <slide>
          <img class="img-responsive" src="http://placehold.it/640x480">
          </slide>
        </carousel>
      </div>
    </div>
    <div class="col-sm-5">
      <h5 class="text-center uppercase">{t}Description{/t}</h5>
      <div ng-bind-html="template.item.description[template.lang]"></div>
    </div>
  </div>
</div>
<hr class="inverted no-margin">
<div class="modal-footer">
  <a class="btn btn-link pull-left" href="#" ng-click="$event.stopPropagation()" target="_blank">
    <h5 class="uppercase">
      <i class="fa fa-globe"></i>
      {t}Go to preview{/t}</h5>
  </a>
  <button class="btn pull-right" ng-class="{ 'btn-white': !add, 'btn-success': add }" ng-if="!template.isPurchased(template.item)" ng-mouseover="add = 1" ng-mouseleave="add = 0" style="width: 100px;">
    <h5 ng-class="{ 'text-white': add }">
      <span ng-if="!add && (template.item.price.month)">
        <strong>[% template.item.price.month.value %]</strong>
        <small> € / {t}month{/t}</small>
      </span>
      <span ng-if="!add && (!template.item.price.month && template.item.price.single)">
        <strong>[% template.item.price.single.value %]</strong>
        <small> € </small>
      </span>
      <span class="semi-bold uppercase" ng-if="!add && (!template.item.price || template.item.price.month == 0)">
        {t}Free{/t}
      </span>
      <span class="semi-bold uppercase" ng-if="add">
        <i class="fa fa-shopping-cart m-r-5"></i>
        {t}Add{/t}
      </span>
    </h5>
  </button>
  <button class="btn btn-info pull-right" ng-class="{ 'btn-info': template.isPurchased(template.item), 'btn-success': template.isActive(template.item) }" ng-click="template.enable(template.item)" ng-disabled="template.isActive(template.item)" ng-if="template.isPurchased(template.item)" style="width: 100px;">
    <h5 class="semi-bold text-white uppercase" ng-if="template.isPurchased(template.item) && !template.isActive(template.item) && !template.item.loading">{t}Enable{/t}</h5>
    <h5 class="semi-bold text-white uppercase" ng-if="template.isPurchased(template.item) && template.isActive(template.item)">{t}Enabled{/t}</h5>
    <h5 class="semi-bold text-white uppercase" ng-if="template.isPurchased(template.item) && !template.isActive(template.item) && template.item.loading">{t}Enabling{/t}...</h5>
  </button>
</div>
