</div>
<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.pressclipping = !expanded.pressclipping" ng-if="!hasMultilanguage()">
  <i class="fa fa-bell m-r-10"></i>{t}PressClipping{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.pressclipping }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-if="!hasMultilanguage()" ng-class="{ 'expanded': expanded.pressclipping }">
  <div class="form-group no-margin">
    <div class="text-center m-b-5 m-t-5" ng-if="!item.content_status">
      <small><i class="fa fa-info-circle text-info"></i> {t}Check it as "Published" to send pressclipping.{/t}</small>
    </div>
    <div>
      <div class="text-center" ng-if="item.content_status">
        <button class="btn btn-mini btn-block ng-scope m-b-5 btn-success" type="button"><i class="fa fa-paper-plane m-r-5"></i>{t}SEND PRESSCLIPPING{/t}</button>
      </div>
    </div>
  </div>
</div>




