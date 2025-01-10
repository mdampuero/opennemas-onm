<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.social = !expanded.social">
  <i class="fa fa-list m-r-10"></i>{t}Options for Social Networks{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.social }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded' : expanded.social }">
  <div class="form-group no-margin">
      {include file="ui/component/input/text.tpl" iCounter=true iField="social_title" iRequired=false iTitle="{t}Title for Social Networks{/t}" iValidation=false iHelp="{t}Title shown when sharing on social networks.{/t}"}
      {include file="ui/component/input/text.tpl" iCounter=true iField="social_description" iRequired=false iTitle="{t}Description for Social Networks{/t}" iValidation=false iHelp="{t}Description shown when sharing on social networks.{/t}" }
      {include file="ui/component/input/image.tpl" iName="featuredSocial" iCaption="false"}
  </div>
</div>
