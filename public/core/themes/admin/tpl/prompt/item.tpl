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
         <div class="col-sm-6 form-group">
          <label>{t}Type field{/t}</label>
          <select class="form-control" name="field" id="field" ng-model="item.field">
            <option value="FIELD_TITLE">FIELD_TITLE</option>
            <option value="FIELD_INTRODUCTION">FIELD_INTRODUCTION</option>
            <option value="FIELD_BODY">FIELD_BODY</option>
          </select>
         </div>
      </div>
      <div class="form-group">
        {include file="ui/component/input/text.tpl" iField="name" iMessageField="name" iFlag="validating" iRequired=true iTitle="{t}Prompt{/t}" iValidation=true iPlaceholder="{t}Improve this text in terms of SEO...{/t}"}
      </div>
      <div class="form-group">
        {include file="ui/component/input/text.tpl" iField="context" iMessageField="context" iFlag="validating" iTitle="{t}System or context{/t}" iPlaceholder="{t}You are an expert in SEO and SEO-optimized copywriting...{/t}"}
      </div>
    </div>
  </div>
{/block}
