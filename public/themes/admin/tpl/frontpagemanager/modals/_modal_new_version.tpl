<div class="modal-body">
  <div class="clearfix">
    <p>
    {t escape=off}There is a new version for this frontpage, if you try to save the current changes the new version will be overwritten.{/t}<br/>
    </p>
    <div>
      <button class="btn btn-danger" data-dismiss="modal" ng-click="no()" ng-disabled="noLoading" type="button">
        <i class="fa fa-absolute fa-times m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': noLoading }"></i>
        {t}continue with the current version{/t}
      </button>
      <button class="btn btn-success" ng-click="yes()" ng-disabled="yesLoading" type="button">
        <i class="fa fa-absolute fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': yesLoading }"></i>
        {t}Reload frontpage{/t}
      </button>
    </div>
  </div>
</div>
