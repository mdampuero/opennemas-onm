<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.seo = !expanded.seo">
  <i class="fa fa-list m-r-10"></i>{t}SEO DATA{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.seo }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded' : expanded.seo }">
  <div class="form-group no-margin">
      {include file="ui/component/input/text.tpl" iField="seo_title" iRequired=false iTitle="{t}SEO TITLE{/t}" iValidation=false iHelp="{t}Write the SEO title for this article{/t}"}
      {include file="ui/component/input/text.tpl" iField="seo_description" iRequired=false iTitle="{t}SEO DESCRIPTION{/t}"  iHelp="{t}Write the SEO description for this article{/t}" iValidation=false}
  </div>
</div>
