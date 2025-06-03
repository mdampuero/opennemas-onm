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
          <li class="quicklinks m-r-10">
            <a class="btn btn-white" ng-click="openImportModal()">
              <span class="fa fa-sign-in"></span>
              {t}Import{/t}
            </a>
          </li>
          <li class="quicklinks m-r-10">
            <a class="btn btn-white" ng-href="{url name=manager_ws_onmai_config_download}?token=[% security.token %]">
              <span class="fa fa-download"></span>
              {t}Download{/t}
            </a>
          </li>
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
            <input class="form-control l-h-16" ng-model="onmai_settings.engines.openai.apiKey" type="text">
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
                <div class="col-md-4">
                  <select class="form-control" ng-model="item.id" ng-if="modelsSuggested.openai.length > 0">
                    <option value="[% model %]" ng-repeat="model in modelsSuggested.openai">[% model %]</option>
                  </select>
                  <input type="text" class="form-control"  ng-model="item.id" ng-if="modelsSuggested.openai.length == 0">
                </div>
                <div class="col-md-8">
                  <div class="checkbox pull-right">
                    <button class="btn btn-block btn-danger ng-cloak" ng-click="removeModel($index)" type="button">
                      <i class="fa fa-trash-o"></i>
                    </button>
                  </div>
                </div>
              </div>
            </h4>
            <hr>
            <div class="row" ng-include="'model'"></div>
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
            <input class="form-control l-h-16" ng-model="onmai_settings.engines.gemini.apiKey" type="text">
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
                  <select class="form-control" ng-model="item.id" ng-if="modelsSuggested.gemini.length > 0">
                    <option value="[% model %]" ng-repeat="model in modelsSuggested.gemini">[% model %]</option>
                  </select>
                  <input type="text" class="form-control"  ng-model="item.id" ng-if="modelsSuggested.gemini.length == 0">
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
            <div class="row" ng-include="'model'"></div>
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
    <uib-tab heading="{t}DeepSeek{/t}" ng-click="selectTab('deepseek')">
      <ng-container>
        <h4>{t}API Key{/t}</h4>
        <div class="row m-t-15">
          <div class="controls col-md-12 m-b-10">
            <input class="form-control l-h-16" ng-model="onmai_settings.engines.deepseek.apiKey" type="text">
          </div>
        </div>
        <h4>{t}Models and prices{/t}</h4>
        <p>
          <i class="fa fa-info-circle text-info"></i>
          <small class="text-muted">{t}The selling prices are expressed per million words, with one word approximately equivalent to 1.5 tokens. You can check the price table here.{/t}
            <a class="btn-link" target="_blank" href="https://api-docs.deepseek.com/quick_start/pricing" class="admin_add" title="{t}DeepSeek Pricing{/t}">
              <span class="fa fa-external-link"></span>
            </a>
          </small>
        </p>
        <div class="grid simple bg-white onm-shadow m-t-30" ng-repeat="item in onmai_settings.engines.deepseek.models">
          <div class="grid-body ng-cloak">
            <h4 class="form-label m-b-20">
              <div class="row">
                <div class="col-md-6">
                  <select class="form-control" ng-model="item.id" ng-if="modelsSuggested.deepseek.length > 0">
                    <option value="[% model %]" ng-repeat="model in modelsSuggested.deepseek">[% model %]</option>
                  </select>
                  <input type="text" class="form-control"  ng-model="item.id" ng-if="modelsSuggested.deepseek.length == 0">
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
            <div class="row" ng-include="'model'"></div>
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
    <uib-tab heading="{t}Mistral AI{/t}" ng-click="selectTab('mistralai')">
      <ng-container>
        <h4>{t}API Key{/t}</h4>
        <div class="row m-t-15">
          <div class="controls col-md-12 m-b-10">
            <input class="form-control l-h-16" ng-model="onmai_settings.engines.mistralai.apiKey" type="text">
          </div>
        </div>
        <h4>{t}Models and prices{/t}</h4>
        <p>
          <i class="fa fa-info-circle text-info"></i>
          <small class="text-muted">{t}The selling prices are expressed per million words, with one word approximately equivalent to 1.5 tokens. You can check the price table here.{/t}
            <a class="btn-link" target="_blank" href="https://mistral.ai/technology/#pricing" class="admin_add" title="{t}DeepSeek Pricing{/t}">
              <span class="fa fa-external-link"></span>
            </a>
          </small>
        </p>
        <div class="grid simple bg-white onm-shadow m-t-30" ng-repeat="item in onmai_settings.engines.mistralai.models">
          <div class="grid-body ng-cloak">
            <h4 class="form-label m-b-20">
              <div class="row">
                <div class="col-md-6">
                  <select class="form-control" ng-model="item.id" ng-if="modelsSuggested.mistralai.length > 0">
                    <option value="[% model %]" ng-repeat="model in modelsSuggested.mistralai">[% model %]</option>
                  </select>
                  <input type="text" class="form-control"  ng-model="item.id" ng-if="modelsSuggested.mistralai.length == 0">
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
            <div class="row" ng-include="'model'"></div>
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
<script type="text/ng-template" id="model">
  {include file="onmai/_model.tpl"}
</script>
<script type="text/ng-template" id="modal-import-settings">
  {include file="common/modalImportSettings.tpl"}
</script>
