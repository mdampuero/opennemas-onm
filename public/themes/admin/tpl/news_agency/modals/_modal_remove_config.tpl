<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
    {t}Remove the server configuration{/t}
  </h4>
</div>
<div class="modal-body">
  <p>
      {t escape=off}Are you sure that you want to delete the selected server configuration?{/t}
  </p>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-link" data-dismiss="modal" ng-click="close()">{t}No{/t}</button>
  <button type="button" class="btn btn-primary" ng-click="confirm()">{t}Yes, delete it{/t}</button>
</div>
