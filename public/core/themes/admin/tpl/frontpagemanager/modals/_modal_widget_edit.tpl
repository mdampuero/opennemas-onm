<div class="modal-header" style="position:sticky;top:0;z-index: 50;background-color:#fff;">
  <h3 class="modal-title">{t}Edit Widget{/t}</h3>
</div>
<div class="modal-body modal-widget-edit" ng-init="getItem(id)">
  {include file="widget/item-quick.tpl"}
</div>
<div class="modal-footer" style="position: sticky; bottom: 0; background-color: #fff; border-top: 1px solid #e5e5e5;">
  <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
    <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
    {t}Save{/t}
  </button>
  <button class="btn btn-danger" ng-click="close()">{t}Close{/t}</button>
</div>
<style>
  .modal-open {
    .modal {
      &:has(.modal-widget-edit) {
        bottom: 30px;
        top: 30px;

        ~ .picker {
          z-index: 1060;
        }
        .modal-dialog {
          margin: 0 auto;

          @media (min-width: 992px) {
            width: 900px;
          }
        }
      }
    }
  }
</style>
