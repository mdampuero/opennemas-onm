<div class="modal-body text-center">
  <i class="fa fa-4x m-b-10 m-t-40" ng-class="template.iconName"></i>
  <h4 class="modal-title m-b-30">{t escape=off 1="[% template.handler %]"}Change comment manager to <span class="text-primary">%1</span>{/t}</h4>
  <p >{t 1="[% template.handler %]"}Are you sure you want to change your comment system handler?{/t}</p>
</div>

<div class="modal-footer row">
  <div class="col-xs-6">
    <button class="btn btn-block btn-danger text-uppercase" data-dismiss="modal" ng-click="no()" ng-disabled="loading" type="button">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-times m-r-5"></i>
        {t}No{/t}
      </h4>
    </button>
  </div>
  <div class="col-xs-6">
    <button type="button" class="btn btn-block btn-success" ng-click="yes()">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-check m-r-5"></i>
        {t}Yes{/t}
      </h4>
    </button>
  </div>
</div>
