<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();">&times;</button>
  <h4 class="modal-title">
      {t}Settings{/t}
  </h4>
</div>
<div class="modal-body">
  <div class="form-group">
    <label class="form-label clearfix" for="settings">
      <div class="pull-left">{t}Model{/t}</div>
    </label>
    <select class="form-control " ng-model="template.onmai_config.model">
      <option value="">[% template.model %] [Manager]</option>
      <option value="[% item.id %]" ng-repeat="item in template.models">[% item.title %]</option>
    </select>
  </div>
</div>
<div class="modal-footer">
    <button class="btn secondary" ng-click="dismiss()" ng-disabled="loading">{t}Cancel{/t}</button>
    <button class="btn btn-loading btn-success text-uppercase" ng-click="confirm()">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
    </button>
</div>
