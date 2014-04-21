<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h3>{t}Permanently remove item{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure that do you want remove permanently from trash "[% title %]"?{/t}</p>
    <p class="alert alert-error">{t} You will not be able to restore it back.{/t}</p>

</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="delete(id, index, route)">{t}Yes, remove{/t}</button>
    <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
</div>
