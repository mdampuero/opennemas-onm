<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_aimodel_config') %]">
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
<div class="content ng-hide" ng-show="items">
  <div class="grid simple onm-shadow">
    <div class="grid-body ng-cloak">
      <h4>{t}Settings{/t}</h4>
      <div class="row m-t-20">
        <div class="controls col-xs-12 col-md-3 m-b-10">
          <label>{t}AI 'Temperature' param{/t}</label>
          <input class="form-control" ng-model="openai_settings.temperature" type="text">
          <i class="fa fa-info-circle text-info"></i>
          <small class="text-muted">{t}Numeric valuer between 0 and 2. Higher values like 1.2 will make the output more random, while lower values like 0.2 will make it more focused and deterministic{/t}</small>
        </div>
        <div class="controls col-xs-12 col-md-3 m-b-10">
          <label>{t}Max tokens{/t}</label>
          <input class="form-control" ng-model="openai_settings.max_tokens" type="text">
          <i class="fa fa-info-circle text-info"></i>
          <small class="text-muted">{t}The maximum number of tokens that can be generated in the chat completion. (Completion tokens){/t}</small>
        </div>
        <div class="controls col-xs-12 col-md-3 m-b-10">
          <label>{t}Frequency penalty{/t}</label>
          <input class="form-control" ng-model="openai_settings.frequency_penalty" type="text">
          <i class="fa fa-info-circle text-info"></i>
          <small class="text-muted">{t}Number between -2.0 and 2.0{/t}.{t}Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model's likelihood to repeat the same line verbatim.{/t}</small>
        </div>
        <div class="controls col-xs-12 col-md-3 m-b-10">
          <label>{t}Presence penalty{/t}</label>
          <input class="form-control" ng-model="openai_settings.presence_penalty" type="text">
          <i class="fa fa-info-circle text-info"></i>
          <small class="text-muted">{t}Number between -2.0 and 2.0{/t}.{t}Positive values penalize new tokens based on whether they appear in the text so far, increasing the model's likelihood to talk about new topics.{/t}</small>
        </div>
      </div>
    </div>
  </div>
  <div class="grid simple bg-white onm-shadow">
  <div class="grid-body">
    <h4>
      {t}Models and prices{/t}
    </h4>
    <p>
      <i class="fa fa-info-circle text-info"></i>
      <small class="text-muted">{t}The selling prices are expressed per million words, with one word approximately equivalent to 1.5 tokens. You can check the price table here.{/t}
      <a class="btn-link" target="_blank" href="https://openai.com/api/pricing/" class="admin_add" title="{t}Open AI pricing{/t}">
        <span class="fa fa-external-link"></span>
      </a>
      </small>
    </p>
    <div class="grid simple bg-white onm-shadow m-t-30" ng-repeat="item in items">
      <div class="grid-body ng-cloak">
        <h4 class="form-label m-b-20">
          [% item.id %]
          <div class="checkbox pull-right">
            <input
              name="selected-[% item.id %]" id="checkbox-[% item.id %]"
              type="checkbox"
              ng-model="item.active"
              ng-true-value="true"
              ng-false-value="false">
            <label for="checkbox-[% item.id %]">{t}Active{/t}</label>
          </div>
          <div class="radio pull-right m-r-10" ng-show="item.active">
            <input id="radio-[% item.id %]" type="radio" name="defaultRadio" ng-model="item.default" ng-value="true" ng-change="setDefaultItem(item)">
            <label for="radio-[% item.id %]">{t}Default{/t}</label>
          </div>
        </h4>
        <hr>
        <small>{t}Date{/t}: [% item.formatted_date %]</small>
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
                        ng-disabled="!item.active"
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
                        ng-disabled="!item.active"
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
                        ng-disabled="!item.active"
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
                        ng-disabled="!item.active"
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
  </div>
</div>
