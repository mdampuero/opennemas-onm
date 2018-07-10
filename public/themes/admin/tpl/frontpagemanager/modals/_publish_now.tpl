<div class="modal-body">
  <div class="clearfix">
    <p>
    {t escape=off}Are you sure you want to publish this frontpage now?{/t}<br/>
    </p>
    <div>
      <button class="btn btn-danger" data-dismiss="modal" ng-click="no()" ng-disabled="noLoading" type="button">
        <i class="fa fa-absolute fa-times m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': noLoading }"></i>
        {t}Cancel{/t}
      </button>
      <button class="btn btn-success" ng-click="yes()" ng-disabled="yesLoading" type="button">
        <i class="fa fa-absolute fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': yesLoading }"></i>
        {t}Publish{/t}
      </button>
    </div>
  </div>
</div>
