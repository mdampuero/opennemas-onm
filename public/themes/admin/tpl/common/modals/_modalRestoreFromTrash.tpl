<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Restore item{/t}
  </h4>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure that do you want restore from trash "[% template.content.title %]"?{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, restore{/t}</button>
    <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
</div>
