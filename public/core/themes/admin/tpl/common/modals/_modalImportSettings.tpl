<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-sign-in"></i>
      {t}Import Settings{/t}
  </h4>
</div>
<div class="modal-body">
  <div class="form-group">
    <label class="form-label clearfix" for="settings">
      <div class="pull-left">{t}Insert a valid JSON{/t}</div>
    </label>
    <div class="controls">
      <textarea name="settings" id="settings" ng-model="template.settings" onm-editor onm-editor-preset="simple" class="form-control" rows="15"></textarea>
    </div>
  </div>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="confirm()" ng-disabled="loading">{t}Import{/t}</button>
    <button class="btn secondary" ng-click="close()" ng-disabled="loading">{t}Cancel{/t}</button>
</div>
