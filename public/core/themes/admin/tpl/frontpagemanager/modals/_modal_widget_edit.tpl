<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title">{t}Edit Widget{/t}</h3>
    </div>
    <div class="modal-body">
      {include file="widget/item-quick.tpl"}
    </div>
    <div class="modal-footer">
      <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
        <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
        {t}Save{/t}
      </button>
      <button class="btn btn-danger" ng-click="close()">{t}Close{/t}</button>
    </div>
  </div>
</div>
