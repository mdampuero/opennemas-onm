<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h3>{t}Delete menus{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to delete [% selected.length %] menus?{/t}</p>

</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="deleteSelected('backend_ws_menus_batch_delete')">{t}Yes, delete all{/t}</button>
    <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
</div>
