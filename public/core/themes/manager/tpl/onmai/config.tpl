<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_onmai_config') %]">
              <i class="fa fa-file-o"></i>
              {t}Configs{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <button class="btn btn-loading btn-success text-uppercase" ng-click="saveActiveItems()" ng-disabled="promptForm.$invalid || saving">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="grid simple bg-white onm-shadow">
    <div class="grid-body ng-cloak">
      <div class="row">
        <div class="col-sm-4">
          <h4 class="form-label m-b-20">
            {t}Default{/t}
          </h4>
          <div class="controls">
            <select class="form-control-lg" ng-model="onmai_settings.model">
              <option value="[% item.id %]" ng-repeat="item in allModels">[% item.name %]</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <uib-tabset class="tab-form">
    <uib-tab heading="{t}Open AI{/t}" ng-click="selectTab('openai')">
      <ng-container>
        <h4>{t}API Key{/t}</h4>
        <div class="row m-t-15">
          <div class="controls col-md-12 m-b-10">
            <input class="form-control" ng-model="onmai_settings.engines.openai.apiKey" type="text">
          </div>
        </div>
        <h4>{t}Settings{/t}</h4>
        <div class="row m-t-15">
          <div class="controls col-xs-12 col-md-3 m-b-10">
            <label>{t}AI 'Temperature' param{/t}  <i class="fa fa-info-circle text-info" uib-tooltip="{t}Numeric valuer between 0 and 2.{/t}"></i></label>
            <input class="form-control" ng-model="onmai_settings.engines.openai.settings.temperature" type="text">
          </div>
          <div class="controls col-xs-12 col-md-3 m-b-10">
            <label>{t}Max tokens{/t} <i class="fa fa-info-circle text-info" uib-tooltip="{t}The maximum number of tokens that can be generated.{/t}"></i></label>
            <input class="form-control" ng-model="onmai_settings.engines.openai.settings.max_tokens" type="text">
          </div>
          <div class="controls col-xs-12 col-md-3 m-b-10">
            <label>{t}Frequency penalty{/t} <i class="fa fa-info-circle text-info" uib-tooltip="{t}Number between -2.0 and 2.0{/t}"></i> </label>
            <input class="form-control" ng-model="onmai_settings.engines.openai.settings.frequency_penalty" type="text">
          </div>
          <div class="controls col-xs-12 col-md-3 m-b-10">
            <label>{t}Presence penalty{/t} <i class="fa fa-info-circle text-info" uib-tooltip="{t}Number between -2.0 and 2.0{/t}"></i></label>
            <input class="form-control" ng-model="onmai_settings.engines.openai.settings.presence_penalty" type="text">
          </div>
        </div>
        <h4>{t}Models and prices{/t}</h4>
        <p>
          <i class="fa fa-info-circle text-info"></i>
          <small class="text-muted">{t}The selling prices are expressed per million words, with one word approximately equivalent to 1.5 tokens. You can check the price table here.{/t}
            <a class="btn-link" target="_blank" href="https://openai.com/api/pricing/" class="admin_add" title="{t}Open AI pricing{/t}">
              <span class="fa fa-external-link"></span>
            </a>
          </small>
        </p>
        <div class="grid simple bg-white onm-shadow m-t-30" ng-repeat="item in onmai_settings.engines.openai.models">
          <div class="grid-body ng-cloak">
            <h4 class="form-label m-b-20">
              <div class="row">
                <div class="col-md-6">
                  <input type="text" class="form-control"  ng-model="item.id">
                </div>
                <div class="col-md-6">
                  <div class="checkbox pull-right">
                    <button class="btn btn-block btn-danger ng-cloak" ng-click="removeModel($index)" type="button">
                      <i class="fa fa-trash-o"></i>
                    </button>
                  </div>
                </div>
              </div>
            </h4>
            <hr>
            <div class="row">
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
              </div>
              <div class="col-sm-6">
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
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 m-t-20">
            <button class="btn btn-block btn-default" ng-click="addModel()" type="button">
              <i class="fa fa-plus"></i>
              {t}Add{/t}
            </button>
          </div>
        </div>
      </ng-container>
    </uib-tab>
    <uib-tab heading="{t}Gemini{/t}" ng-click="selectTab('gemini')">
      <ng-container>
        <h4>{t}API Key{/t}</h4>
        <div class="row m-t-15">
          <div class="controls col-md-12 m-b-10">
            <input class="form-control" ng-model="onmai_settings.engines.gemini.apiKey" type="text">
          </div>
        </div>
        <h4>{t}Settings{/t}</h4>
        <div class="row m-t-15">
          <div class="controls col-xs-12 col-md-3 m-b-10">
            <label>{t}AI 'Temperature' param{/t}  <i class="fa fa-info-circle text-info" uib-tooltip="{t}Numeric valuer between 0 and 2.{/t}"></i></label>
            <input class="form-control" ng-model="onmai_settings.engines.gemini.settings.temperature" type="text">
          </div>
          <div class="controls col-xs-12 col-md-3 m-b-10">
            <label>{t}Max tokens{/t} <i class="fa fa-info-circle text-info" uib-tooltip="{t}The maximum number of tokens that can be generated.{/t}"></i></label>
            <input class="form-control" ng-model="onmai_settings.engines.gemini.settings.max_tokens" type="text">
          </div>
          <div class="controls col-xs-12 col-md-3 m-b-10">
            <label>{t}Frequency penalty{/t} <i class="fa fa-info-circle text-info" uib-tooltip="{t}Number between -2.0 and 2.0{/t}"></i> </label>
            <input class="form-control" ng-model="onmai_settings.engines.gemini.settings.frequency_penalty" type="text">
          </div>
          <div class="controls col-xs-12 col-md-3 m-b-10">
            <label>{t}Presence penalty{/t} <i class="fa fa-info-circle text-info" uib-tooltip="{t}Number between -2.0 and 2.0{/t}"></i></label>
            <input class="form-control" ng-model="onmai_settings.engines.gemini.settings.presence_penalty" type="text">
          </div>
        </div>
        <h4>{t}Models and prices{/t}</h4>
        <p>
          <i class="fa fa-info-circle text-info"></i>
          <small class="text-muted">{t}The selling prices are expressed per million words, with one word approximately equivalent to 1.5 tokens. You can check the price table here.{/t}
            <a class="btn-link" target="_blank" href="https://ai.google.dev/pricing" class="admin_add" title="{t}Gemini pricing{/t}">
              <span class="fa fa-external-link"></span>
            </a>
          </small>
        </p>
        <div class="grid simple bg-white onm-shadow m-t-30" ng-repeat="item in onmai_settings.engines.gemini.models">
          <div class="grid-body ng-cloak">
            <h4 class="form-label m-b-20">
              <div class="row">
                <div class="col-md-6">
                  <input type="text" class="form-control"  ng-model="item.id">
                </div>
                <div class="col-md-6">
                  <div class="checkbox pull-right">
                    <button class="btn btn-block btn-danger ng-cloak" ng-click="removeModel($index)" type="button">
                      <i class="fa fa-trash-o"></i>
                    </button>
                  </div>
                </div>
              </div>
            </h4>
            <hr>
            <div class="row">
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
              </div>
              <div class="col-sm-6">
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
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 m-t-20">
            <button class="btn btn-block btn-default" ng-click="addModel()" type="button">
              <i class="fa fa-plus"></i>
              {t}Add{/t}
            </button>
          </div>
        </div>
      </ng-container>
    </uib-tab>
  </uib-tabset>
</div>
