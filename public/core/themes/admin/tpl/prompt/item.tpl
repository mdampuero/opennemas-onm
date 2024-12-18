{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Prompts{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="PromptCtrl" ng-init="getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-terminal m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="[% routing.generate('backend_openai_prompts_list') %]">
    {t}Prompts{/t}
  </a>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="row m-t-20">
        <div class="col-sm-12 form-group">
          <label for="name">{t}Title{/t}</label>
          <input class="form-control" id="name" name="name" ng-model="item.name" maxlength="64" type="text"/>
        </div>
        <div class="col-md-2 col-sm-6 form-group">
          <label>{t}Mode{/t}</label>
          <ui-select name="mode" class="form-control" theme="select2" ng-model="item.mode" search-enabled="false" required ng-init="options = [ { name: '{t}Create{/t}', key: 'New'}, { name: '{t}Edit{/t}', key: 'Edit'} ]">
            <ui-select-match>
              [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.key as item in options | filter: $select.search">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>
        <div class="col-md-3 col-sm-6 form-group" >
          <label>{t}Field{/t}</label>
          <ui-select name="mode" class="form-control" theme="select2" ng-model="item.field" search-enabled="false" required ng-init="options = [ { name: '{t}Titles{/t}', key: 'titles'}, { name: '{t}Introductions{/t}', key: 'introductions'}, { name: '{t}Bodies{/t}', key: 'bodies' } ]">
            <ui-select-match>
              [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.key as item in options | filter: $select.search">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>
        <div class="col-md-3 col-sm-6 form-group">
          <label>{t}Default tone{/t}</label>
          <ui-select name="tone" class="form-control" theme="select2" ng-model="item.tone">
            <ui-select-match>
              [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.name as item in data.extra.tones | filter: { name: $select.search }" position='down'>
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>
        <div class="col-sm-4 form-group">
          <label>{t}Default role{/t}</label>
          <ui-select name="role" class="form-control" theme="select2" ng-model="item.role">
            <ui-select-match>
              [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.name as item in data.extra.roles | filter: { name: $select.search }" position='down'>
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>
      </div>
      <div class="form-group no-margin">
        <label class="form-label" for="description">
          {t}Prompt{/t}
        </label>
        <div class="controls">
          <textarea name="prompt" id="prompt" ng-model="item.prompt" class="form-control" rows="5"></textarea>
        </div>
      </div>
    </div>
  </div>
{/block}
