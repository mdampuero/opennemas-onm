<div class="modal-header text-left">
  <h3 class="modal-title">{t}opennemas AI{/t}</h3>
</div>
<div class="modal-body">
  <ng-container ng-show="waiting">
    <div class="loading-container">
      <i class="fa fa-circle-o-notch fa-spin fa-3x"></i>
    </div>
  </ng-container>
  <ng-container ng-show="!waiting && template.step == 1">
    <div class="form-group">
      <label ng-if="mode == 'Edit'">[% template.AIFieldTitle %]</label>
      <label ng-if="mode == 'New'">{t}Enter a topic{/t}</label>
      <input ng-if="displayMode == 'input' || mode == 'New'" ng-disabled="mode == 'Edit'" type="text" class="form-control input-lg" ng-model="template.input" ng-change="updateUserPrompt()" placeholder="{t}Ex: Benefits of AI-powered CMS{/t}">
      <div ng-if="displayMode == 'textarea' && mode == 'Edit'" class="openai-wrapper-text input-lg" ng-bind-html="template.input"></div>
    </div>
    <div class="form-group">
      <label>{t}Suggested prompts{/t}</label>
      <select name="field" id="field" class="form-control input-lg" ng-model="template.promptSelected" ng-change="updateUserPrompt()"  ng-options="item as item.name for item in prompts | filter: { mode_or: mode }">
        <option value="">{t}Select a prompt{/t}</option>
      </select>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          <label>{t}Suggested role{/t}  </label>
          <select name="field" id="field" class="form-control input-lg" ng-model="template.roleSelected" ng-options="item as item.name for item in extra.roles">
             <option value="">{t}Select a role{/t}</option>
          </select>
          <div class="help m-l-3 m-t-5" ng-show="template.roleSelected"> <i class="fa fa-info-circle m-r-5 text-info"></i> [% template.roleSelected.prompt %] </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label>{t}Suggested tone{/t}</label>
          <select name="field" id="field" class="form-control input-lg" ng-model="template.toneSelected" ng-options="item as item.name for item in extra.tones">
            <option value="">{t}Select a tone{/t}</option>
          </select>
          <div class="help m-l-3 m-t-5" ng-show="template.toneSelected"> <i class="fa fa-info-circle m-r-5 text-info"></i> [% template.toneSelected.description %] </div>
        </div>
      </div>
    </div>
    <div class="form-group m-b-0">
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
        <div class="openai-wrapper-text" ng-bind-html="template.suggested_text"></div>
      </ng-container>
      <ng-container ng-if="activeText == 'original'">
        <label class="m-t-10 pull-left">{t}Original text{/t}</label>
        <button type="button" class="btn btn-link pull-right m-l-20" ng-click="setActiveText('suggested')" >
          {t}View generated{/t}
          <i class="fa fa-eye"></i>
        </button>
        <span class="m-t-10 label label-info pull-right m-l-5">[% countWords(template.input) %] {t}words{/t}, [% template.input.length %] {t}characters{/t}</span>
        <div class="clearfix"></div>
        <div class="openai-wrapper-text" ng-bind-html="template.input"></div>
      </ng-container>
    </ng-container>
    <p>
      <span class="label label-success pull-left"><strong>[% last_words_generated %]</strong> {t}words generated{/t}</span>
      <span class="pull-right text-pink pointer" ng-click="generate()">{t}Regenerate {/t} <i class="fa fa-refresh openai-text-degrade"></i></span>
    </p>
  </ng-container>
</div>
<div class="modal-footer m-t-15 border-top">
  <button ng-if="template.step == 1" class="btn btn-white btn-lg pull-left" data-dismiss="modal" ng-click="dismiss()" type="button">
    {t}Cancel{/t}
  </button>
  <button ng-if="template.step == 2" class="btn btn-white btn-lg pull-left" ng-click="back()" type="button">
    <i class="fa fa-angle-left"></i>
    {t}Edit prompt{/t}
  </button>
  <button type="button" class="btn btn-success btn-loading btn-lg pull-right" ng-click="continue()" ng-disabled="waiting || !template.promptInput || !template.input || !template.promptSelected">
    {t}Continue{/t}
    <i class="fa fa-angle-right"></i>
  </button>
</div>
