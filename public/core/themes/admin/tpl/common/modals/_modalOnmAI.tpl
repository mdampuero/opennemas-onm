<div class="modal-header text-left">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="dismiss()" style="font-size: 2rem;">
    <span aria-hidden="true"><b>&times;</b></span>
  </button>
  <h3 class="modal-title">{t}Opennemas AI{/t}</h3>
</div>
<div class="modal-body">
  <div class="alert alert-danger" ng-show="error">
    An error has occurred, please try again later.
  </div>
  <ng-container ng-show="waiting">
    <div class="loading-container">
      <div class="onm-ai-spinner-wrap">
        <svg id="onm-ai-spinner" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 120 120">
          <defs>
            <style>
              .st_1 { stroke: url(#grad1); }
              .st_1, .st_2 { fill: none; margin: 10; }
              .st_4 { fill: #242c88; }
              .st_3 { fill: url(#grad3); }
              .st_2 { stroke: url(#grad2); }
            </style>
            <linearGradient id="grad1" data-name="gradient" x1="-67.82" y1="139.56" x2="19.52" y2="139.56" gradientTransform="translate(36.72 -80.89) rotate(-180) scale(1 -1)" gradientUnits="userSpaceOnUse">
              <stop offset="0" stop-color="#6fc4d9"/>
              <stop offset="1" stop-color="#242c88"/>
            </linearGradient>
            <linearGradient id="grad2" data-name="gradient" x1="21.16" y1="68.08" x2="109.17" y2="68.08" gradientTransform="translate(-11.35 118.97) rotate(6.42) scale(1 -1)" xlink:href="#grad1"/>
            <linearGradient id="grad3" data-name="gradient" x1="22.88" y1="12.51" x2="166.2" y2="179.15" gradientTransform="translate(0 122) scale(1 -1)" xlink:href="#grad1"/>
          </defs>
          <g id="spinner">
            <g id="shapes">
              <path id="rect-third" class="st_1" d="M90.27,87.09l-43.15,10.91c-10.15,2.57-20.37-3.93-22.35-14.2l-6.74-34.84c-1.9-9.8,4.45-19.31,14.23-21.31l41.44-8.49c9.49-1.94,18.84,3.88,21.29,13.25l8.45,32.42c2.55,9.79-3.36,19.78-13.16,22.26h-.01Z">
              </path>
              <path id="rect-second" class="st_2" d="M29.08,34.41l37.1-15.68c9.33-3.94,20.05.7,23.57,10.19l12.63,34.1c3.42,9.23-1.32,19.47-10.56,22.85l-35.73,13.06c-8.77,3.21-18.53-.93-22.32-9.46l-14-31.48c-4.04-9.08.16-19.71,9.31-23.58Z">
              </path>
              <path id="rect-main" class="st_3" d="M77.85,15.89c10.95-.95,19.65,6.36,21.9,16.81,2.66,12.34,4.11,25.79,5.8,38.29-.91,13.02-11.49,17.11-21.58,22.01-11.99,5.83-22.28,11.51-36.09,6.77-9.84-3.39-14.76-10.95-21.05-18.56-3.67-4.44-8.18-8.34-9.83-14.08-2.23-7.75.13-16.15,6.23-21.42,14.97-8.66,29.99-18.68,45.29-26.71,3.14-1.65,5.7-2.81,9.33-3.12h0ZM78.35,20.12c-2.4.19-4.71,1.06-6.84,2.13-14.63,7.32-28.78,17.78-43.41,25.35-3.64,2.29-6.25,6.08-7.13,10.31-1.98,9.55,4.71,14.64,10.09,21.06,5.29,6.33,9.19,12.68,17.2,15.93,12.59,5.12,21.18-.04,32.22-5.39,10.31-4.99,21.41-8.42,20.33-22.26-2.27-11.02-3.22-22.59-5.55-33.56-1.75-8.22-8.12-14.28-16.92-13.57h.01Z">
              </path>
            </g>
            <path id="ai" class="st_4" d="M76.92,40.92h3.48v35.51h-3.48v-35.51ZM58.08,40.92h-3.74l-13.03,35.51h3.69l4.04-11.31,1.01-3.03,4.75-12.88c.61-1.67,1.36-4.6,1.36-4.6h.1s.76,2.93,1.36,4.6l4.65,12.88,1.06,3.03,4.09,11.31h3.69s-13.03-35.51-13.03-35.51Z"/>
          </g>
        </svg>
      </div>
    </div>
  </ng-container>
  <ng-container ng-show="!waiting && template.step == 1 && !template.translationHelper">
    <div class="form-group">
      <label ng-if="mode == 'Edit'">[% template.AIFieldTitle %]</label>
      <label ng-if="mode == 'New'">{t}Enter a topic{/t}</label>
      <input ng-if="displayMode == 'input' || mode == 'New'" ng-disabled="mode == 'Edit'" type="text" class="form-control input-lg" ng-model="template.input" ng-change="updateUserPrompt()" placeholder="{t}Ex: Benefits of AI-powered CMS{/t}">
      <div ng-if="displayMode == 'textarea' && mode == 'Edit'" class="onmai-wrapper-text input-lg" ng-bind-html="template.input"></div>
    </div>
    <div class="form-group">
      <label>{t}Suggested prompts{/t}</label>
      <select name="field" id="field" class="form-control input-lg" ng-model="template.promptSelected" ng-change="updateUserPrompt()"  ng-options="item as item.name for item in prompts | filter: { mode_or: mode }">
        <option value="">{t}Select a prompt{/t}</option>
      </select>
    </div>
    <div class="row">
      <div class="col-sm-5">
        <div class="form-group">
          <label>{t}Suggested role{/t}  </label>
          <select name="field" id="field" class="form-control input-lg" ng-model="template.roleSelected" ng-options="item as item.name for item in extra.roles">
             <option value="">{t}Select a role{/t}</option>
          </select>
          <div class="help m-l-3 m-t-5" ng-show="template.roleSelected"> <i class="fa fa-info-circle m-r-5 text-info"></i> [% template.roleSelected.prompt %] </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <label>{t}Suggested tone{/t}</label>
          <select name="field" id="field" class="form-control input-lg" ng-model="template.toneSelected" ng-options="item as item.name for item in extra.tones">
            <option value="">{t}Select a tone{/t}</option>
          </select>
          <div class="help m-l-3 m-t-5" ng-show="template.toneSelected"> <i class="fa fa-info-circle m-r-5 text-info"></i> [% template.toneSelected.description %] </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="form-group">
          <label>{t}Response language{/t}</label>
          <select
            name="language"
            id="language"
            class="form-control input-lg"
            ng-model="template.locale"
            ng-options="item.code as item.name for item in extra.languages">
        </select>
        </div>
      </div>
    </div>
    <div class="form-group m-b-0" ng-if="showPrompt">
      <label>{t}Prompt edit{/t}</label>
      <textarea class="form-control input-lg" ng-model="template.promptInput" rows="5" placeholder=""></textarea>
    </div>
  </ng-container>

  <ng-container ng-show="!waiting && template.step == 2">
    <ng-container ng-if="displayMode == 'input'">
      <ng-container ng-if="mode == 'Edit'">
        <label class="pull-left">{t}Original text{/t}</label>
        <span class="label label-info pull-right m-l-5">[% countWords(template.input) %] {t}words{/t}, [% template.input.length %] {t}characters{/t}</span>
        <div class="clearfix"></div>
        <div class="form-group over-bg-gray" ng-click="setActiveText('original')">
        <h2 class="pull-left m-r-10">[% template.input %]</h2>
          <h3 class="pull-right">
            <i class="fa" ng-class="{ 'fa-square-o text-muted': activeText !== 'original', 'fa-check-square text-pink': activeText === 'original'}"></i>
          </h3>
        </div>
        <hr >
      </ng-container>
      <label class="pull-left">{t}Generated text{/t}</label>
      <span class="label label-info pull-right m-l-5">[% countWords(template.suggested_text) %] {t}words{/t}, [% template.suggested_text.length %] {t}characters{/t}</span>
      <div class="clearfix"></div>
      <div class="form-group over-bg-gray" ng-click="setActiveText('suggested')">
        <h2 class="pull-left m-r-10">[% template.suggested_text %]</h2>
        <h3 class="pull-right" ng-if="mode == 'Edit'">
          <i class="fa" ng-class="{ 'fa-square-o text-muted': activeText !== 'suggested', 'fa-check-square text-pink': activeText === 'suggested'}"></i>
        </h3>
      </div>
    </ng-container>
    <ng-container ng-if="displayMode == 'textarea'">
      <ng-container ng-if="activeText == 'suggested'">
        <label class="m-t-10 pull-left">{t}Generated text{/t}</label>
        <button ng-if="mode == 'Edit'" type="button" class="btn btn-link btn-loading pull-right m-l-20" ng-click="setActiveText('original')" >
          {t}View original{/t}
          <i class="fa fa-eye"></i>
        </button>
        <span class="m-t-10 label label-info pull-right m-l-5">[% countWords(template.suggested_text) %] {t}words{/t}, [% template.suggested_text.length %] {t}characters{/t}</span>
        <div class="clearfix"></div>
        <div class="onmai-wrapper-text" ng-bind-html="template.suggested_text"></div>
      </ng-container>
      <ng-container ng-if="activeText == 'original'">
        <label class="m-t-10 pull-left">{t}Original text{/t}</label>
        <button type="button" class="btn btn-link pull-right m-l-20" ng-click="setActiveText('suggested')" >
          {t}View generated{/t}
          <i class="fa fa-eye"></i>
        </button>
        <span class="m-t-10 label label-info pull-right m-l-5">[% countWords(template.input) %] {t}words{/t}, [% template.input.length %] {t}characters{/t}</span>
        <div class="clearfix"></div>
        <div class="onmai-wrapper-text" ng-bind-html="template.input"></div>
      </ng-container>
    </ng-container>
    <p ng-if="!template.translationHelper">
      <span class="pull-right text-pink pointer" ng-click="generate()">{t}Regenerate {/t} <i class="fa fa-refresh openai-text-degrade"></i></span>
    </p>
  </ng-container>

  <ng-container ng-show="!waiting && template.step != 2 && template.translationHelper">
    <div class="form-group">
      <label ng-if="mode == 'New'">{t}Original{/t}</label>
      <input ng-if="displayMode == 'input'" type="text" class="form-control input-lg" ng-model="template.orVal">
      <div ng-if="displayMode == 'textarea'" class="onmai-wrapper-text input-lg" ng-bind-html="template.orVal"></div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          <label>{t}Tone{/t}</label>
          <select name="field" id="field" class="form-control input-lg" ng-model="template.toneSelected" ng-options="item as item.name for item in extra.tones">
            <option value="">{t}Select a tone{/t}</option>
          </select>
          <div class="help m-l-3 m-t-5" ng-show="template.toneSelected"> <i class="fa fa-info-circle m-r-5 text-info"></i> [% template.toneSelected.description %] </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label>{t}Translate to{/t}</label>
          <select
            name="language"
            id="language"
            class="form-control input-lg"
            ng-model="template.locale"
            ng-options="item.code as item.name for item in extra.languages">
        </select>
        </div>
      </div>
    </div>
  </ng-container>
</div>
<div class="modal-footer m-t-15 border-top">
  <ng-container ng-if="!template.translationHelper">
    <button ng-if="template.step == 1" class="btn btn-white btn-lg pull-left" data-dismiss="modal" ng-click="dismiss()" type="button">
      {t}Cancel{/t}
    </button>
    <button ng-if="template.step == 2" class="btn btn-white btn-lg pull-left" ng-click="back()" type="button">
      <i class="fa fa-angle-left"></i>
      {t}Prompt edit{/t}
    </button>
    <button type="button" class="btn btn-success btn-loading btn-lg pull-right" ng-click="continue()" ng-disabled="waiting || !template.promptInput || !template.input || !template.promptSelected">
      {t}Continue{/t}
      <i class="fa fa-angle-right"></i>
    </button>
  </ng-container>
  <ng-container ng-if="template.translationHelper">
    <button class="btn btn-white btn-lg pull-left" data-dismiss="modal" ng-click="dismiss()" type="button">
      {t}Cancel{/t}
    </button>
    <button ng-if="template.step == 1" type="button" class="btn btn-success btn-loading btn-lg pull-right" ng-click="translate()" ng-disabled="waiting || !template.orVal">
      {t}Translate{/t}
      <i class="fa fa-angle-right"></i>
    </button>
    <button ng-if="template.step == 2" type="button" class="btn btn-success btn-loading btn-lg pull-right" ng-click="continue()" ng-disabled="waiting">
      {t}Continue{/t}
      <i class="fa fa-angle-right"></i>
    </button>
  </ng-container>
</div>
