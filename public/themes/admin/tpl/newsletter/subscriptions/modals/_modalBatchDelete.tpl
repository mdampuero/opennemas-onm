<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Delete selected items{/t}
  </h4>
</div>
<div class="modal-body">
    <p>{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to delete %1 item(s)?{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, delete all{/t}</button>
    <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
</div>
