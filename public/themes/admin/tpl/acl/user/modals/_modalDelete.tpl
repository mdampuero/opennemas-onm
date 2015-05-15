<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Send to trash{/t}
  </h4>
</div>
<div class="modal-body">
    <p>
      {t escape=off}Are you sure that you want to delete the user "[% template.content.name %]"?{/t}
    </p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="confirm()" ng-disabled="loading">{t}Yes, delete{/t}</button>
    <button class="btn secondary" ng-click="close()" ng-disabled="loading">{t}No{/t}</button>
</div>
