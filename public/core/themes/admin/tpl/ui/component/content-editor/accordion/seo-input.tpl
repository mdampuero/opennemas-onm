<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.seo = !expanded.seo">
  <i class="fa fa-list m-r-10"></i>{t}Options for SEO{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.seo }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded' : expanded.seo }">
  <div class="form-group no-margin">
      {include file="ui/component/input/text.tpl" iField="seo_title" iRequired=false iTitle="{t}Title for SEO{/t}" iValidation=false iHelp="{t}Write the SEO title for this content{/t}"}
      {include file="ui/component/input/text.tpl" iField="seo_description" iRequired=false iTitle="{t}Description for SEO{/t}" iValidation=false iHelp="{t}Write the SEO description for this content{/t}" }
      {include file="ui/component/input/text.tpl" iField="canonicalurl" iRequired=false iTitle="{t}Canonical url{/t}" iValidation=false iHelp="{t}Write the Canonincal URL for this content{/t}"}
      <div class="m-t-5">
        <label for="noindex" class="form-label">
          Noindex
        </label>
        {include file="ui/component/content-editor/accordion/checkbox.tpl" field="noindex" title="{t}Mark this content to prevent indexing{/t}"}
    </div>
  </div>
</div>
