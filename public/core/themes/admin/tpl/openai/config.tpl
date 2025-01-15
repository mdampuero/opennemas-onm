{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="OpenAIConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_openai_config') %]">
                  <i class="fa fa-cog m-r-10"></i>
                  {t}OpenAI Module Configuration{/t}
                </a>
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="MASTER"}
                <li class="quicklinks m-r-10">
                  <a class="btn btn-white" ng-click="openImportModal()">
                    <span class="fa fa-sign-in"></span>
                    {t}Import{/t}
                  </a>
                </li>
                <li class="quicklinks m-r-10">
                  <a class="btn btn-white" href="[% routing.generate(routes.downloadConfig) %]">
                    <span class="fa fa-download"></span>
                    {t}Download{/t}
                  </a>
                </li>
              {/acl}
              <li class="quicklinks">
                <button class="btn btn-loading btn-success ng-cloak text-uppercase" ng-click="checkApiKey()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-xs-6 col-md-3 ">
              <h4>{t}Service type{/t}</h4>
              <div class="controls">
                <select class="form-control " ng-model="settings.openai_service">
                  <option value="opennemas" selected>{t}Opennemas AI{/t}</option>
                  <option value="custom">{t}Open AI{/t}</option>
                </select>
              </div>
            </div>
            <div class="col-xs-6 col-md-9" ng-if="settings.openai_service === 'custom'">
              <h4>{t}OpenAI Secret Key{/t}</h4>
              <input class="form-control" ng-model="settings.openai_credentials.apikey" type="text">
            </div>
          </div>
        </div>
      </div>
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <h4>{t}Roles{/t}</h4>
          <div class="form-group">
            <div class="controls">
              <div class="row" ng-repeat="role in settings.openai_roles track by $index">
                <div class="col-lg-4 col-md-3">
                  <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required ng-disabled="role.readOnly" maxlength="64">
                </div>
                <div class="col-lg-7 col-md-7">
                  <input class="form-control" ng-model="role.prompt" placeholder="{t}Prompt{/t}" type="text" required ng-disabled="role.readOnly" maxlength="2048">
                </div>
                <div class="col-lg-1 col-md-2 m-b-15">
                  <button class="btn btn-block btn-danger ng-cloak" ng-click="removeRole($index)" type="button" ng-disabled="role.readOnly">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
              <button class="btn btn-block btn-default" ng-click="addRole()" type="button">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <h4>{t}Tones{/t}</h4>
          <div class="form-group">
            <div class="controls">
              <div class="row" ng-repeat="role in settings.openai_tones track by $index">
                <div class="col-lg-4 col-md-3">
                  <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required ng-disabled="role.readOnly" maxlength="64">
                </div>
                <div class="col-lg-7 col-md-7">
                  <input class="form-control" ng-model="role.description" placeholder="{t}Description{/t}" type="text" required ng-disabled="role.readOnly" maxlength="2048">
                </div>
                <div class="col-lg-1 col-md-2 m-b-15">
                  <button class="btn btn-block btn-danger ng-cloak" ng-click="removeTone($index)" type="button" ng-disabled="role.readOnly">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
              <button class="btn btn-block btn-default" ng-click="addTone()" type="button">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
      </div>
      {acl isAllowed="MASTER"}
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-xs-12">
              <div class="col-xs-12">
                <div class="row">
                  <h4>{t}OpenAI configuration{/t}</h4>
                </div>
              </div>
              <div class="row m-t-50" >
                <div class="col-xs-6 col-md-3 ">
                  <div class="controls">
                    <label>{t}OpenAI module{/t}</label>
                    <select class="form-control-lg" ng-model="settings.openai_config.model">
                      <option value="">[% settings.openai_model_default.id %] [Manager]</option>
                      <option value="[% item.id %]" ng-repeat="item in settings.openai_models">[% item.id %]</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row m-t-50">
                <div class="controls col-xs-12 col-md-3 m-b-10">
                  <label>{t}AI 'Temperature' param{/t}</label>
                  <input class="form-control" ng-model="settings.openai_config.temperature" placeholder="[% settings.openai_config_manager.temperature %] [Manager]" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Numeric valuer between 0 and 2. Higher values like 1.2 will make the output more random, while lower values like 0.2 will make it more focused and deterministic{/t}</small>
                </div>
                <div class="controls col-xs-12 col-md-3 m-b-10">
                  <label>{t}Max tokens{/t}</label>
                  <input class="form-control" ng-model="settings.openai_config.max_tokens" placeholder="[% settings.openai_config_manager.max_tokens %] [Manager]" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}The maximum number of tokens that can be generated in the chat completion. (Completion tokens){/t}</small>
                </div>
                <div class="controls col-xs-12 col-md-3 m-b-10">
                  <label>{t}Frequency penalty{/t}</label>
                  <input class="form-control" ng-model="settings.openai_config.frequency_penalty" placeholder="[% settings.openai_config_manager.frequency_penalty %] [Manager]" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Number between -2.0 and 2.0{/t}.{t}Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model's likelihood to repeat the same line verbatim.{/t}</small>
                </div>
                <div class="controls col-xs-12 col-md-3 m-b-10">
                  <label>{t}Presence penalty{/t}</label>
                  <input class="form-control" ng-model="settings.openai_config.presence_penalty" placeholder="[% settings.openai_config_manager.presence_penalty %] [Manager]" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Number between -2.0 and 2.0{/t}.{t}Positive values penalize new tokens based on whether they appear in the text so far, increasing the model's likelihood to talk about new topics.{/t}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {/acl}
    </div>
  </form>
{/block}
{block name="modals"}
  <script type="text/ng-template" id="modal-import-settings">
    {include file="common/modals/_modalImportSettings.tpl"}
  </script>
{/block}
