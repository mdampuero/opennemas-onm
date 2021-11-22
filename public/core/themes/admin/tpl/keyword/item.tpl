{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Keywords{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="KeywordCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-tags m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_keywords_list}">
    {t}Keywords{/t}
  </a>
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
      {t}Save{/t}
    </button>
  </li>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iCounter=true iField="keyword" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Keyword{/t}" iValidation=true}
      {include file="ui/component/input/text.tpl" iCounter=true iField="value" iRequired=true iTitle="{t}Value{/t}" iValidation=true}
      {include file="ui/component/select/keyword_type.tpl" iField="type" iRequired=true iTitle="{t}Type{/t}" sClass="form-control" ngModel="criteria.type"}
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-translate">
    {include file="common/modals/_translate.tpl"}
  </script>
{/block}

