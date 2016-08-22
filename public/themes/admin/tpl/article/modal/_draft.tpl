<div class="modal-body">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="p-b-30 p-t-30 text-center">{t}There is an existing draf{/t}</h3>
  <h4 class="p-b-30 text-center">{t}Do you want to edit delete the user?{/t}</h4>
</div>
<div class="modal-footer row">
  <div class="col-xs-6">
    <button class="btn btn-block btn-danger text-uppercase" data-dismiss="modal" ng-click="no()" ng-disabled="noLoading" type="button">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-absolute fa-times m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': noLoading }"></i>
        {t}No, discard{/t}
      </h4>
    </button>
  </div>
  <div class="col-xs-6">
    <button type="button" class="btn btn-block btn-success" ng-click="yes()" ng-disabled="yesLoading">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-absolute fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': yesLoading }"></i>
        {t}Yes, edit{/t}
      </h4>
    </button>
  </div>
</div>
