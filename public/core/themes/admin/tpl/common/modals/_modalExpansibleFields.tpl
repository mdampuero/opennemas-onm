<div class="modal-header form-settings-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-cog"></i>
      {t}Default expanded fields{/t}
  </h4>
  <p class="modal-subtitle">{t}Selected fields will be automatically expanded when the form loads{/t}</p>

</div>
<div class="modal-body form-settings-body">
  <div class="form-group" ng-repeat="item in template.formSettings.expansibleFields">
    <div class="checkbox">
      <input id="[% item.name %]" name="[% item.name %]" type="checkbox" ng-model="template.defaultExpanded[item.name]" ng-cloak>
      <label for="[% item.name %]" class="form-label">{t}[% item.title %]{/t}</label>
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
