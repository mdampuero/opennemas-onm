<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.{$field} = !expanded.{$field}">
  <i class="fa {$icon} m-r-10"></i>{$title}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.{$field} }"></i>
  {if !empty($iRoute)}
    <a ng-if="{$iRoute} && {$iRoute}.length > 0" ng-click="$event.stopPropagation()" ng-href="[% {$iRoute} %]" ng-show="!expanded.{$field} && item.pk_content > 0" class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" target="_blank">
      <i class="fa fa-external-link"></i>
      {t}Link{/t}
    </a>
  {/if}
  {if $iRequired}
    <span class="pull-right" ng-if="!expanded.{$field}">
      {include file="common/component/icon/status.tpl" iForm="form.{$field}" iNgModel="item.{$field}" iValidation=true}
    </span>
  {/if}
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.{$field} }">
  <div class="form-group no-margin">
    <div class="controls">
      <input class="form-control" id="{$field}" name="{$field}" ng-model="item.{$field}" {if $number}type="number" {elseif $datetime}datetime-picker datetime-picker-format="YYYY-MM-DD" datetime-picker-timezone="{$timezone}" datetime-picker-max="item.event_end_date" datetime-picker-use-current="true" type="datetime"{else}type="text"{/if} {if $iRequired}required{/if}/>
      {if $iRequired}
        {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iForm="form.{$field}" iNgModel="item.{$field}" iValidation=true}
      {/if}
    </div>
  </div>
</div>
