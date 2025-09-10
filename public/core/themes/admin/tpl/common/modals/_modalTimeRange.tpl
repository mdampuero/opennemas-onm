<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
      {t}Time Range{/t}
  </h4>
</div>
<div class="modal-body">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="starttime" class="control-label">
                    <i class="fa fa-calendar-plus-o"></i> {t}Start date{/t}
                </label>
                <div class="input-group">
                    <input type="datetime" id="starttime" name="starttime" class="form-control" autocomplete="off" ng-model="tempCriteria.starttime" datetime-picker />
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="endtime" class="control-label">
                    <i class="fa fa-calendar-minus-o"></i> {t}End date{/t}
                </label>
                <div class="input-group">
                    <input type="datetime" id="endtime" name="endtime" class="form-control" autocomplete="off" ng-model="tempCriteria.endtime" datetime-picker />
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
            </div>
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
