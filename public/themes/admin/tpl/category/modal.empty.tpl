<div class="modal-body">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="p-b-30 p-t-30 text-center">{t}Are you sure?{/t}</h3>
  <h4 class="p-b-30 text-center" ng-if="!template.selected || template.selected == 1">{t}Do you want to empty the item?{/t}</h4>
  <h4 class="p-b-30 text-center" ng-if="template.selected > 1">{t}Do you want to empty the selected items?{/t}</h4>
  <p ng-if="!template.selected || template.selected == 1">
    {t escape=off}This means that all contents assigned to the category <strong>[% template.item.title %]</strong> will be deleted.{/t}
  </p>
  <p ng-if="template.selected > 1">
    {t escape=off}This means that all contents assigned to the selected categories will be deleted.{/t}
  </p>
  <div class="alert alert-warning m-t-15 no-margin">
    <i class="fa fa-warning"></i>
    {t}Be careful. This operation can not be undone.{/t}
  </div>
</div>
<div class="modal-footer row">
  <div class="col-xs-6">
    <button class="btn btn-block btn-danger text-uppercase" data-dismiss="modal" ng-click="dismiss()" ng-disabled="loading" type="button">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-times m-r-5"></i>
        {t}No{/t}
      </h4>
    </button>
  </div>
  <div class="col-xs-6">
    <button type="button" class="btn btn-block btn-success btn-loading" ng-click="confirm()" ng-disabled="loading">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
        {t}Yes{/t}
      </h4>
    </button>
  </div>
</div>
