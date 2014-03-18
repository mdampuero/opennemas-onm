<div class="modal-header">
  <button type="button" class="close" ng-click="cancel()" aria-hidden="true">×</button>
  <h3>{t}Delete advertisements{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to delete [% selected.length %] advertisements?{/t}</p>

</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="deleteSelected()" type="button">{t}Yes, delete all{/t}</button>
    <button class="btn secondary" ng-click="cancel()" type="button">{t}No{/t}</button>
</div>
