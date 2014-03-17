<div class="modal-header">
  <button type="button" class="close" ng-click="cancel()" aria-hidden="true">×</button>
  <h3>{t}Delete widget{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure that do you want delete "[% title %]"?{/t}</p>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="delete(index, id)">{t}Yes, delete{/t}</button>
    <button class="btn secondary" ng-click="cancel()">{t}No{/t}</button>
</div>
