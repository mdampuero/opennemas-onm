<div class="modal-header">
  <button type="button" class="close" aria-hidden="true" ng-click="cancel()">×</button>
  <h3>{t}Delete advertisement{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure that do you want delete "[% title %]"?{/t}</p>

</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="delete(index, id)">{t}Yes, delete{/t}</button>
    <button class="btn secondary"ng-click="cancel()">{t}No{/t}</button>
</div>
