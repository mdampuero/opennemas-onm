<div class="modal-body text-center">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="no()">&times;</button>
  <i class="fa fa-envelope fa-4x m-t-40 m-b-20"></i>
  <h3 class="modal-title" class="m-b-30">{t}This newsletter is going to be sent{/t}</h3>
  <h4 class="m-b-30">{t escape=off 1="[% template.emails_to_send %]"}Are you sure to send %1 emails?{/t}</h4>
</div>
<div class="modal-footer row">
  <div class="col-xs-6">
    <button type="button" class="btn btn-danger btn-lg btn-block" data-dismiss="modal" ng-click="no()">{t}Cancel{/t}</button>
  </div>
  <div class="col-xs-6">
    <button type="button" class="btn btn-success btn-lg btn-block" ng-click="yes()" ng-disabled="yesLoading">{t}Send{/t}</button>
  </div>
</div>
