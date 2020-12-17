<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="no()">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Delete all trashed contents{/t}
  </h4>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to remove permanently all the contents inside the trash?{/t}</p>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="yes()" ng-disabled="yesLoading" type="button">
      <i class="fa fa-absolute fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': yesLoading }"></i>
      {t}Yes, remove all{/t}
    </button>
    <button class="btn secondary" data-dismiss="modal" ng-click="no()" ng-disabled="noLoading" type="button">
      <i class="fa fa-absolute fa-times m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': noLoading }"></i>
      {t}No{/t}
    </button>
</div>
