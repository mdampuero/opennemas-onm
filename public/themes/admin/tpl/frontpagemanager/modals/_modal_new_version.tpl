<div ng-init="template.extra.client ? template.step = 2 : template.step = 1">
  <div class="modal-body">
    <h3 class="p-b-30 p-t-30 text-center">{t}New version{/t}</h3>
    <p class="text-center">{t escape=off}There is a new version for this frontpage, if you try to save the current changes the new version will be overwritten.{/t}</p>
  </div>
  <div class="modal-footer row">
    <div class="col-xs-12">
      <button type="button" class="btn btn-block btn-success btn-loading" ng-click="confirm()">
        <h4 class="bold text-uppercase text-white">
          <i class="fa fa-refresh m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
          {t}Reload frontpage{/t}
        </h4>
      </button>
    </div>
  </div>
</div>
