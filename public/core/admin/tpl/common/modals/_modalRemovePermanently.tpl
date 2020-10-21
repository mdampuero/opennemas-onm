<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="no();">&times;</button>
    <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Permanently remove item{/t}
  </h4>
</div>
<div class="modal-body">
    <p>{t escape=off 1="[% template.content.title %]"}Are you sure that do you want remove permanently from trash "%1"?{/t}</p>
    <p class="alert alert-error">{t} You will not be able to restore it back.{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="yes()" type="button">{t}Yes, remove{/t}</button>
    <button class="btn secondary" ng-click="no()">{t}No{/t}</button>
</div>
