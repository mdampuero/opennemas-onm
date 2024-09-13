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
      <div class="menu-dragable-accordion" id="pressclipping-container">
        <div class=" m-t-5">
          <div ng-show="item.pressclipping_sended" class="alert alert-success" id="alerteo">
            <i class="fa fa-check"></i>
            {t}Article sent{/t}
            <small>
              [% notification.send_date | moment : 'YYYY-MM-DD HH:mm:ss': null : '{$app.locale->getTimeZone()->getName()}' %]
            </small>
          </div>
          <div ng-show="!item.pressclipping_sended && statusPressclipping" class="alert alert-warning" id="alerteo">
            <i class="fa fa-check"></i>
            {t}Article deleted{/t}
            <small>
              [% notification.send_date | moment : 'YYYY-MM-DD HH:mm:ss': null : '{$app.locale->getTimeZone()->getName()}' %]
            </small>
          </div>
          <div ng-show="statusPressclipping === 'failure'" class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i>
            {t}Notification failed{/t}
            <small>
              [% notification.send_date | moment : 'YYYY-MM-DD HH:mm:ss': null : '{$app.locale->getTimeZone()->getName()}' %]
            </small>
          </div>
        </div>
      </div>
      <div class="text-center" ng-if="item.content_status">
        <button class="btn btn-mini btn-block ng-scope m-b-5 btn-success"
                ng-show="!item.pressclipping_sended"
                ng-click="sendPressClipping(item)"
                type="button">
          <i class="fa fa-paper-plane m-r-5"></i>{t}SEND PRESSCLIPPING{/t}
        </button>

        <button class="btn btn-mini btn-block ng-scope m-b-5 btn-danger"
                ng-show="item.pressclipping_sended"
                ng-click="removePressClipping(item.pk_content)"
                type="button">
          <i class="fa fa-paper-plane m-r-5"></i>{t}REMOVE PRESSCLIPPING{/t}
        </button>
      </div>
    </div>
  </div>
</div>




