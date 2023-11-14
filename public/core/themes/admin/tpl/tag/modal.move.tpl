<div class="modal-body">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="p-b-30 p-t-30 text-center">{t}Are you sure?{/t}</h3>
  <h4 class="p-b-30 text-center">{t}Do you want to move contents in tag?{/t}</h4>
  <div class="row m-t-15">
    <div class="col-xs-5 form-group" ng-show="!template.selected">
      <label class="form-label semi-bold" for="tag">{t}Source{/t}</label>
      <div class="controls">
        <p class="form-control-static">
          [% template.source.title %]
        </p>
      </div>
    </div>
    <div class="col-xs-1 form-group p-t-40" ng-show="!template.selected">
      <i class="fa fa-arrow-right fa-2x"></i>
    </div>
    <div class="col-xs-6 form-group" ng-class="{ 'col-xs-12': template.selected }">
      <label class="form-label semi-bold" for="tag">{t}Target{/t}</label>
      <div class="controls">
        <onm-tags-input class="block" label-text="{t}Tag{/t}" exclude="template.exclude" ng-model="template.target" placeholder="{t}Select a tag{/t}…" required></onm-tags-input>
      </div>
    </div>
  </div>
  <div class="m-t-30 no-margin text-center">
    <div><i class="fa fa-3x fa-warning text-warning"></i></div>
    <p class="bold m-t-10 text-uppercase">
      {t}This action can not be undone{/t}
    </p>
  </div>
</div>
<div class="modal-footer row">
  <div class="col-xs-6">
    <button class="btn btn-block btn-danger text-uppercase" data-dismiss="modal" ng-click="dismiss()" ng-disabled="loading" type="button">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-times m-r-5"></i>
        {t}No{/t}
      </h4>
    </button>
  </div>
  <div class="col-xs-6">
    <button type="button" class="btn btn-block btn-success btn-loading" ng-click="confirm()" ng-disabled="loading || !template.target">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
        {t}Yes{/t}
      </h4>
    </button>
  </div>
</div>
