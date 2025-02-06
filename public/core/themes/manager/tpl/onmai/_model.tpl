<div class="col-sm-6">
  <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
    <div class="form-status text-left">
      <label class="m-b-10"><b>{t}Cost price per 1 million tokens{/t}</b></label>
      <div class="row">
        <div class="col-xs-6 form-group">
          <label class="form-label" for="name">{t}Input tokens{/t}</label>
          <div class="input-group">
            <span class="input-group-addon">€</span>
            <input
              type="text"
              class="form-control"
              ng-model="item.cost_input_tokens">
          </div>
        </div>
        <div class="col-xs-6 form-group">
          <label class="form-label" for="name">{t}Output tokens{/t}</label>
          <div class="input-group">
            <span class="input-group-addon">€</span>
            <input
              type="text"
              class="form-control"
              ng-model="item.cost_output_tokens">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
    <div class="form-status text-left">
      <label class="m-b-10"><b>{t}Sale price per 1 million words{/t}</b></label>
      <div class="row">
        <div class="col-xs-6 form-group">
          <label class="form-label" for="name">{t}Input words{/t}</label>
          <div class="input-group">
            <span class="input-group-addon">€</span>
            <input
              type="text"
              class="form-control"
              ng-model="item.sale_input_tokens">
          </div>
        </div>
        <div class="col-xs-6 form-group">
          <label class="form-label" for="name">{t}Output words{/t}</label>
          <div class="input-group">
            <span class="input-group-addon">€</span>
            <input
              type="text"
              class="form-control"
              ng-model="item.sale_output_tokens">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-sm-6">
  <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
    <div class="form-status text-left">
      <label class="m-b-10"><b>{t}Params{/t}</b></label>
      <div class="row">
        <div class="col-xs-5">
          <label class="form-label" for="name">{t}Key{/t}</label>
        </div>
        <div class="col-xs-5">
          <label class="form-label" for="name">{t}Value{/t}</label>
        </div>
      </div>
      <div class="row" ng-repeat = "param in item.params">
        <div class="col-xs-5 m-b-10">
          <input
            type="text"
            class="form-control"
            ng-model="param.key">
        </div>
        <div class="col-xs-5 m-b-10">
          <input
            type="text"
            class="form-control"
            ng-model="param.value">
        </div>
        <div class="col-xs-2 text-center p-t-10">
          <i class="fa fa-remove fa-lg text-danger pointer" ng-click="removeParam(item, $index)" uib-tooltip="{t}Remove param{/t}"></i>
        </div>
      </div>
      <div class="text-center">
      <button class="btn btn-success" ng-click="addParam(item)" type="button" uib-tooltip="{t}Add param{/t}">
        <i class="fa fa-plus"></i>
        {t}Add{/t}
      </button>
      </div>
    </div>
  </div>
</div>
