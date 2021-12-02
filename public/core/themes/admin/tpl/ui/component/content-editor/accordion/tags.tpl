<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.tags = !expanded.tags">
  <i class="fa fa-tag m-r-10"></i>
  {t}Tags{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.tags }"></i>
  <span class="pull-right" ng-if="!expanded.tags">
    {include file="common/component/icon/status.tpl" iForm="form.tags" iNgModel="item.tags" iValidation=true}
  </span>
  <span class="badge badge-default m-r-5 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.tags && item.tags && item.tags.length != 0" ng-class="{ 'badge-danger' : item.tags.length === 0 }">
    <strong>
      [% data.extra.tags[config.locale.selected].length  %] {t}Tags{/t}
    </strong>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.tags }">
  <div class="form-group no-margin">
    <label for="metadata" class="form-label">{t}Tags{/t}</label>
    <div class="controls">
      {include file="ui/component/tags-input/tags.tpl" ngModel="item.tags"}
      {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iForm="form.tags" iNgModel="item.tags" iValidation=true}
    </div>
  </div>
</div>
