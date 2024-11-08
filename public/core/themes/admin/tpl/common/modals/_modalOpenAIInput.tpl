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
      <label>{t}Original title{/t}</label>
      <input type="text" class="form-control" ng-model="template.original_text" ng-change="updateUserPrompt()">
    </div>
    <div class="form-group">
      <label>{t}Suggested prompts{/t}</label>
      <select name="field" id="field" class="form-control" ng-model="template.promtSelected" ng-change="updateUserPrompt()">
        <option value="">{t}Select a prompt{/t}</option>
        <option ng-repeat="item in prompts" value="[% $index %]">[% item.name %]</option>
      </select>
    </div>
    <div class="form-group">
      <label>{t}Context{/t}</label>
      <textarea class="form-control" ng-model="template.context_prompt" rows="2" placeholder="" ng-disabled="!edit_context"></textarea>
    </div>
    <div class="form-group">
      <label>{t}Prompt edit{/t}</label>
      <textarea class="form-control" ng-model="template.user_prompt" rows="5" placeholder=""></textarea>
    </div>
  </ng-container>

  <ng-container ng-show="!waiting && template.step == 2">
    <label class="pull-left">{t}Original text{/t}</label>
    <span class="badge badge-success pull-right">[% template.original_text.length %]</span>
    <div class="clearfix"></div>

    <div class="form-group over-bg-gray" ng-click="setActiveText('original')">
    <h2 class="pull-left m-r-10">[% template.original_text %]</h2>
      <h3 class="pull-right">
        <i class="fa" ng-class="{ 'fa-square-o text-muted': activeText !== 'original', 'fa-check-square text-pink': activeText === 'original'}"></i>
      </h3>
    </div>

    <hr >

    <label class="pull-left">{t}Generated text{/t}</label>
    <span class="badge badge-success pull-right">[% template.suggested_text.length %]</span>
    <div class="clearfix"></div>

    <div class="form-group over-bg-gray" ng-click="setActiveText('suggested')">
      <h2 class="pull-left m-r-10">[% template.suggested_text %]</h2>
      <h3 class="pull-right">
        <i class="fa" ng-class="{ 'fa-square-o text-muted': activeText !== 'suggested', 'fa-check-square text-pink': activeText === 'suggested'}"></i>
      </h3>
    </div>

    <p>
      <span class="pull-left">{t}Tokens used:{/t} [% last_token_usage %]</span>
      <span class="pull-right text-pink pointer" ng-click="generate()">{t}Regenerate {/t} <i class="fa fa-refresh openai-text-degrade"></i></span>
    </p>
  </ng-container>
</div>
<hr>
<div class="modal-footer">
  <button ng-if="template.step == 1" class="btn btn-default pull-left" data-dismiss="modal" ng-click="dismiss()" type="button">
    {t}Cancel{/t}
  </button>
  <button ng-if="template.step == 2" class="btn btn-default pull-left" ng-click="back()" type="button">
    {t}Back{/t}
  </button>
  <button type="button" class="btn btn-success btn-loading pull-right" ng-click="continue()" ng-disabled="waiting || !template.user_prompt || !template.original_text">
    {t}Continue{/t}
  </button>
</div>
