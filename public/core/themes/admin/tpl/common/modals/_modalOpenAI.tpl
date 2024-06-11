<div class="modal-header">
  <h3 class="modal-title">{t}Generate text with AI{/t}</h3>

</div>
<div class="modal-body">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h4 class="p-b-10 p-t-10">{t}Introduce prompt text{/t}</h4>
  <textarea class="form-control" ng-model="template.user_prompt" rows="7" placeholder="{t}Write a brief summary about the WWII{/t}"></textarea>

  <button class="btn btn-link m-t-10" type="button" data-toggle="collapse" data-target="#advanced" aria-expanded="false" aria-controls="advanced"> >>{t}Advanced Settings{/t}</button>
  <div class="collapse" id="advanced">
    <div class="card card-body">
      <h4 class="p-b-10 p-t-10">{t}Introduce system context{/t}</h4>
      <textarea  class="form-control" ng-model="template.system_prompt" rows="5" placeholder="{t}You are a SEO expert ...{/t}"></textarea>
    </div>
  </div>
  <button type="button" class="btn btn-block btn-success btn-loading m-t-10" ng-click="generate()" ng-if="!waiting">
    {t}Generate Text{/t}
  </button>
  <button class="btn btn-block btn-success btn-loading m-t-10" type="button" disabled ng-if="waiting">
    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    {t}Loading{/t}...
  </button>
  <div class="m-t-10" ng-if="showResult && !waiting">
    <label class="m-t-10">{t}Result:{/t}</label>
    <textarea class="form-control" ng-model="template.response" rows="7" disabled></textarea>
    <div class="alert alert-primary m-t-10" role="alert" ng-if="showTokens">
      {t 1="[% last_token_usage %]"}You have spent a total of %1 tokens on this request{/t}
    </div>
    <div class="alert alert-danger m-t-10" role="alert" ng-if="showError">
      {t}The request cannot be processed, please try to be more specific.{/t}
    </div>
  </div>
</div>
<div class="modal-footer row">
  <div class="col-xs-6">
    <button class="btn btn-block btn-danger text-uppercase" data-dismiss="modal" ng-click="dismiss()" ng-disabled="loading" type="button">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-times m-r-5"></i>
        {t}Cancel{/t}
      </h4>
    </button>
  </div>
  <div class="col-xs-6">
    <button type="button" class="btn btn-block btn-success btn-loading" ng-click="confirm()" ng-disabled="loading">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
        {t}Accept{/t}
      </h4>
    </button>
  </div>
</div>
