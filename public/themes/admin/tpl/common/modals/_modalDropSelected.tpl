<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Drop these elements{/t}
  </h4>
</div>
<div class="modal-body">
    <p>
      {t escape=off}Are you sure that do you want to delete the selected elements from this frontpage?{/t}<br/>
      {t escape=off}This will temporary remove all the contents but after that you have to save the frontpage.{/t}
    </p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Drop{/t}</button>
    <button class="btn secondary" ng-click="close()" type="button">{t}Keep{/t}</button>
</div>
