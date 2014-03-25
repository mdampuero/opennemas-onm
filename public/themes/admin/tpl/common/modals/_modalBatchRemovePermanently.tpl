<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h3>{t}Remove permanently selected items{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to remove permanently [% selected.length %] item(s)?{/t}</p>
    <p class="alert alert-error">{t} You will not be able to restore them back.{/t}</p>

</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="deleteSelected(route)" type="button">{t}Yes, remove them all{/t}</button>
    <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
</div>
