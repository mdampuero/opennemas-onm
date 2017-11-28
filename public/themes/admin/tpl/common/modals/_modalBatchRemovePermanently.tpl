<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Remove permanently selected items{/t}
  </h4>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to remove permanently [% template.selected.contents.length %] item(s)?{/t}</p>
    <p class="alert alert-error">{t} You will not be able to restore them back.{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="yes()" type="button">{t}Yes, remove them all{/t}</button>
    <button class="btn secondary" ng-click="no()" type="button">{t}No{/t}</button>
</div>
