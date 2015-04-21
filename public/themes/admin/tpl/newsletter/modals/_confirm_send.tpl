<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
    {t}Are you sure to send the newsletter.{/t}
  </h4>
</div>
<div class="modal-body">
  <p>{t escape=off}This newsletter is going to send. Are you sure?{/t}</p>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">{t}Cancel{/t}</button>
  <button type="button" class="btn btn-primary" ng-click="confirm()">{t}Send{/t}</button>
</div>
