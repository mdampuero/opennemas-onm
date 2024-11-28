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
          <div class="col-md-4 col-sm-6 form-group">
            <label>{t}Mode{/t}</label>
            <ui-select name="mode" class="form-control" theme="select2" ng-model="item.mode" search-enabled="false" required>
              <ui-select-match>
                {t}[% $select.selected.name %]{/t}
              </ui-select-match>
              <ui-select-choices repeat="item.key as item in data.extra.modes | filter: { name: $select.search }" position='down'>
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </div>
          <div class="col-md-4 col-sm-6 form-group">
            <label>{t}Field{/t}</label>
            <ui-select name="field" class="form-control" theme="select2" ng-model="item.field" search-enabled="false" required>
              <ui-select-match>
                [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.key as item in data.extra.inputTypes | filter: { name: $select.search }" position='down'>
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </div>
         <div class="col-md-4 col-sm-6 form-group">
          <label>{t}Default tono{/t}</label>
          <ui-select name="tone" class="form-control" theme="select2" ng-model="item.tone" required>
            <ui-select-match>
              [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.name as item in data.extra.tones | filter: { name: $select.search }" position='down'>
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
         </div>
      </div>
      <div class="form-group">
        <label>{t}Default role{/t}</label>
        <ui-select name="role" class="form-control" theme="select2" ng-model="item.role" required>
          <ui-select-match>
            [% $select.selected.name %]
          </ui-select-match>
          <ui-select-choices repeat="item.name as item in data.extra.roles | filter: { name: $select.search }" position='down'>
            <div ng-bind-html="item.name | highlight: $select.search"></div>
          </ui-select-choices>
        </ui-select>
      </div>
      <div class="form-group">
        {include file="ui/component/input/text.tpl" iField="name" iMessageField="name" iFlag="validating" iRequired=true iTitle="{t}Prompt{/t}" iValidation=true iPlaceholder="{t}Improve this text in terms of SEO...{/t}"}
      </div>
    </div>
  </div>
{/block}
