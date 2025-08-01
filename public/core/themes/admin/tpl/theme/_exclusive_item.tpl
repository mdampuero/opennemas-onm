<div class="grid grid-hover pointer simple" ng-class="{ 'vertical blue': isActivated(item) && !isEnabled(item), 'vertical green': isEnabled(item) }">
  <div class="grid-title no-border no-padding"></div>
  <div class="grid-body">
    <h4 class="uppercase">[% item.name %]</h4>
    <div class="row">
      <div class="col-md-6">
        <img class="img-responsive" src="/assets/images/store/[% item.thumbnail %]">
      </div>
      <div class="col-md-6">
        <h5 class="semi-bold text-uppercase">{t}Exclusive{/t}</h5>
        <p ng-if="item.id === 'CUSTOM_TEMPLATE'">{t}No{/t}</p>
        <p ng-if="item.id !== 'CUSTOM_TEMPLATE'">{t}Yes{/t}</p>
        <h5 class="semi-bold text-uppercase">{t}Delivery time{/t}</h5>
        <p ng-if="item.id === 'CUSTOM_TEMPLATE'">2-4 {t}weeks{/t}</p>
        <p ng-if="item.id === 'EXCLUSIVE_TEMPLATE'">2 {t}months{/t}</p>
        <p ng-if="item.id === 'CUSTOM_EXCLUSIVE_TEMPLATE'">{t}Contact us{/t}</p>
        <h5 class="semi-bold text-uppercase">{t}Widgets{/t}</h5>
        <p ng-if="item.id === 'CUSTOM_TEMPLATE'">{t}standard{/t}</p>
        <p ng-if="item.id === 'EXCLUSIVE_TEMPLATE'">{t}standard{/t} + 1 {t}widget{/t}</p>
        <p ng-if="item.id === 'CUSTOM_EXCLUSIVE_TEMPLATE'">{t}Contact us{/t}</p>
      </div>
    </div>
    <div class="p-t-30" ng-bind-html="item.description"></div>
    <div class="p-t-15">
      <a class="btn btn-success pull-right" href="mailto:sales@openhost.es">
        <h5 class="semi-bold text-white uppercase">{t}Contact us{/t}</h5>
      </a>
    </div>
  </div>
</div>
