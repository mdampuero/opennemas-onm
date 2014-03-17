<div class="modal-header">
  <button type="button" class="close" ng-click="cancel()" aria-hidden="true">×</button>
  <h3>{t}Delete menus{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to delete [% selected.length %] menus?{/t}</p>

</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="deleteSelected()">{t}Yes, delete all{/t}</button>
    <button class="btn secondary" ng-click="cancel()">{t}No{/t}</button>
</div>
