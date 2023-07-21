<div class="grid-collapse-body expanded ng-cloak" ng-class="{ 'expanded': expanded.seo_info }">
  <div class="form-group m-b-0">
  <div class="row">
    <div class="col-xs-6">
      <div class="m-t-5" ng-if="item.text_complexity">
        <div class="showcase-info showcase-info-score panel m-b-0">
          <div class="form-status text-center">
            <p class="onm-score text-center lead m-b-5" ng-class="{ 'text-danger': item.text_complexity <= 40, 'text-warning': item.text_complexity > 40 &amp;&amp; item.text_complexity <=60, 'text-success' : item.text_complexity >60 }">
              [% item.text_complexity %]
            </p>
          </div>
          <label class="form-label text-center m-t-10">
            {t}Body complexity{/t}
            <div class="small text-danger" ng-if='item.text_complexity <= 40'>
              {t}Difficult to read{/t}
            </div>
            <div class="small text-warning" ng-if='item.text_complexity > 40 && item.text_complexity <= 60'>
              {t}Easily understood{/t}
            </div>
            <div class="small text-success" ng-if='item.text_complexity > 60'>
              {t}Very easy to read{/t}
            </div>
          </label>
        </div>
      </div>
    </div>
    <div class="col-xs-6">
      <div class="m-t-5" ng-if="item.text_complexity">
        <div class="showcase-info showcase-info-score panel m-b-0">
          <div class="form-status text-center">
            <p class="onm-score text-center lead m-b-5">
              [% item.word_count %]
            </p>
          </div>
          <label class="form-label text-center m-t-10">{t}Words{/t}</label>
        </div>
      </div>
    </div>
    <div class="col-xs-12">
      <div class="m-t-5" ng-if="!item.text_complexity">
        <i class="fa fa-info-circle m-r-5 text-info"></i>
        {t}In order to dislay the text complexity, you need to save the article first.{/t}
      </div>
    </div>
  </div>
  </div>
</div>

