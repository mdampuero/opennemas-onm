{if {setting name=seo_information} eq "1"}
<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.seo_info = !expanded.seo_info" >
  <i class="fa fa-search m-r-10"></i>{t}SEO Information{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.seo_info }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.seo_info }">
  <div class="form-group">
    <div class="m-t-5" ng-if="item.text_complexity">
      <div class="showcase-info">
        <label class="form-label" for="moment1">{t}Text complexity{/t}</label>
        <span class="form-status">
          <span class="ng-cloak badge badge-default" ng-class="{ 'badge-danger': item.text_complexity <= 40, 'badge-warning': item.text_complexity > 40 &amp;&amp; item.text_complexity <=60, 'badge-success' : item.text_complexity >60 }">
            <strong>
              [% item.text_complexity <= 40 ? 'Difficult to read' : (item.text_complexity > 40 && item.text_complexity <= 60 ? 'Easily understood' : 'Very easy to read') %] ([% item.text_complexity %]/100)
            </strong>
          </span>
        </span>
      </div>
    </div>
    <div class="m-t-5" ng-if="!item.text_complexity">
      <i class="fa fa-info-circle m-r-5 text-info"></i>
      {t}In order to dislay the text complexity, you need to save the article first.{/t}
    </div>
  </div>
</div>
{/if}
