<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Arquive these elements{/t}
  </h4>
</div>
<div class="modal-body">
  <p>
    {t escape=off}Are you sure that do you want to arquive the selected elements from this frontpage?{/t}<br/>
    {t escape=off}This will remove selected elements from <strong>ALL FRONTPAGES</strong> but they will remain available for read.{/t}
  </p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Arquive{/t}</button>
    <button class="btn secondary" ng-click="close()" type="button">{t}Keep{/t}</button>
</div>
