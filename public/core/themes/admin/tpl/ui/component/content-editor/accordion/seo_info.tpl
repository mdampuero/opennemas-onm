<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.seo_info = !expanded.seo_info" >
  <i class="fa fa-pencil m-r-10"></i>{t}Body complexity{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.seo_info }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.seo_info }">
  <div class="form-group">
    <div class="m-t-5" ng-if="item.text_complexity">
      <div class="showcase-info">
        <label class="form-label">{t}Text complexity{/t}</label>
        <span class="form-status">
          <span class="ng-cloak badge badge-default" ng-class="{ 'badge-danger': item.text_complexity <= 40, 'badge-warning': item.text_complexity > 40 &amp;&amp; item.text_complexity <=60, 'badge-success' : item.text_complexity >60 }">
             <strong ng-if='item.text_complexity <= 40'>
              {t}Difficult to read{/t}
            </strong>
            <strong ng-if='item.text_complexity > 40 && item.text_complexity <= 60'>
              {t}Easily understood{/t}
            </strong>
            <strong ng-if='item.text_complexity > 60'>
              {t}Very easy to read{/t}
            </strong>
            ([% item.text_complexity %]/100)
          </span>
        </span>
      </div>
    </div>
    <hr>
    <div class="m-t-5" ng-if="item.text_complexity">
      <div class="showcase-info">
        <label class="form-label">{t}Word count{/t}</label>
        <span class="form-status">
          <span class="ng-cloak badge badge-default">
            <strong>
              [% item.word_count %]
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

