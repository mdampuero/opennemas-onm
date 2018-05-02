<div class="modal-body text-center">
  <i class="fa fa-4x fa-warning text-warning m-b-10 m-t-40"></i>
  <h4 class="modal-title m-b-10">{t}Change comment manager{/t}</h4>
  <p >{t 1="[% template.handler %]"}Are you sure you want to change your comment system handler to "%1".{/t}</p>
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
