{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="OnmAIConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_onmai_config') %]">
                  <i class="fa fa-cogs m-r-10"></i>
                  {t}Models{/t}
                </a>
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success ng-cloak text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content ng-cloak">
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-xs-6 col-md-3 ">
              <h4>{t}Service type{/t}</h4>
              <div class="controls">
                <select class="form-control " ng-model="settings.onmai_config.service" ng-change="filterModels()">
                  <option value="onmai" selected>{t}Opennemas AI{/t}</option>
                  <option ng-repeat="(key, value) in settings.engines" value="[% key %]">
                      [% value %]
                  </option>
                </select>
              </div>
            </div>
            <div class="col-xs-6 col-md-3 " ng-if="settings.onmai_config.service !== 'onmai'">
              <h4>{t}Model{/t}</h4>
              <div class="controls">
                <select class="form-control " ng-model="settings.onmai_config.model">
                  <option value="[% item.id %]" ng-repeat="item in models">[% item.title %]</option>
                </select>
              </div>
            </div>
            <div class="col-xs-12 col-md-6" ng-if="settings.onmai_config.service === 'openai'">
              <h4>{t}Open AI API Key{/t}</h4>
              <input class="form-control" ng-model="settings.onmai_config.openai.apiKey" type="text">
            </div>
            <div class="col-xs-12 col-md-6" ng-if="settings.onmai_config.service === 'gemini'">
              <h4>{t}Gemini API Key{/t}</h4>
              <input class="form-control" ng-model="settings.onmai_config.gemini.apiKey" type="text">
            </div>
            <div class="col-xs-12 col-md-6" ng-if="settings.onmai_config.service === 'deepseek'">
              <h4>{t}DeepSeek API Key{/t}</h4>
              <input class="form-control" ng-model="settings.onmai_config.deepseek.apiKey" type="text">
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
{block name="modals"}
  <script type="text/ng-template" id="modal-import-settings">
    {include file="common/modals/_modalImportSettings.tpl"}
  </script>
{/block}
