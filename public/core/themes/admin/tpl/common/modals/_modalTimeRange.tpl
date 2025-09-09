<div class="modal-header form-settings-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
      {t}Default expanded fields{/t}
  </h4>
</div>
<div class="modal-body form-settings-body">
  <div class="form-group">
    <div class="input-group">
      <span class="input-group-addon add-on">
          <i class="fa fa-calendar m-r-5"></i> {t}Start date{/t}
      </span>
      <input class="input-min-45 input-300" type="datetime" id="starttime" autocomplete="off" name="starttime" datetime-picker ng-model="tempCriteria.starttime" />
    </div>
  </div>
  <div class="form-group">
    <div class="input-group">
      <span class="input-group-addon add-on">
        <i class="fa fa-calendar m-r-5"></i> {t}End date{/t}
      </span>
      <input class="input-min-45 input-300" type="datetime" id="endtime" autocomplete="off" name="endtime" datetime-picker ng-model="tempCriteria.endtime" />
    </div>
  </div>
</div>
<div class="modal-footer row">
    <div class="col-xs-6">
      <button class="btn btn-block btn-danger text-uppercase" data-dismiss="modal" ng-click="close()" ng-disabled="loading" type="button">
        <h4 class="bold text-uppercase text-white">
          <i class="fa fa-times m-r-5"></i>
          {t}Cancel{/t}
        </h4>
      </button>
    </div>
    <div class="col-xs-6">
      <button class="btn btn-block btn-success text-uppercase" data-dismiss="modal" ng-click="confirm()" ng-disabled="loading" type="button">
        <h4 class="bold text-uppercase text-white">
          <i class="fa fa-check m-r-5"></i>
          {t}Confirm{/t}
        </h4>
      </button>
    </div>
</div>
