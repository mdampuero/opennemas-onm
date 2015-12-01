<div class="grid grid-hover pointer simple" ng-class="{ 'vertical blue': isActivated(item) && !isEnabled(item), 'vertical green': isEnabled(item) }">
  <div class="grid-title no-border no-padding"></div>
  <div class="grid-body">
    <h4 class="uppercase">[% item.name %]</h4>
    <div class="row">
      <div class="col-md-6">
        <img class="img-responsive" src="/assets/images/market/[% item.thumbnail %]">
      </div>
      <div class="col-md-6">
        <h5 class="semi-bold text-uppercase">{t}Exclusive{/t}</h5>
        <p>{t}Yes{/t}</p>
        <h5 class="semi-bold text-uppercase">{t}Delivery time{/t}</h5>
        <p>2-4 weeks</p>
        <h5 class="semi-bold text-uppercase">{t}Widgets{/t}</h5>
        <p>standard</p>
      </div>
    </div>
    <div ng-bind-html="item.description"></div>
    <div class="p-t-15">
      <a class="btn btn-success pull-right" href="mailto:sales@openhost.es">
        <h5 class="semi-bold text-white uppercase">{t}Contact us{/t}</h5>
      </a>
    </div>
  </div>
</div>
