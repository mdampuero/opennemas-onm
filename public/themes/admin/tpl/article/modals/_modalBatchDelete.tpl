<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" ng-click="close()" aria-hidden="true">×</button>
  <h3>{t}Delete articles{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to delete [% selected.length %] articles?{/t}</p>

</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="deleteSelected('backend_ws_articles_batch_delete')">{t}Yes, delete all{/t}</button>
    <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
</div>
