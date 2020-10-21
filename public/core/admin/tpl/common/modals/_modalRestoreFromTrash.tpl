<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="no();">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-retweet"></i>
      {t}Restore item{/t}
  </h4>
</div>
<div class="modal-body">
    <p>{t escape=off 1="[% template.content.title %]"}Are you sure that do you want restore from trash "%1"?{/t}</p>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="yes()" ng-disabled="yesLoading" type="button">
      <i class="fa fa-absolute fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': yesLoading }"></i>
      {t}Yes, restore{/t}
    </button>
    <button class="btn secondary" data-dismiss="modal" ng-click="no()" ng-disabled="noLoading" type="button">
      <i class="fa fa-absolute fa-times m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': noLoading }"></i>
      {t}No{/t}
    </button>
</div>
