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

{block name="grid"}
  <div class="content">
    <div class="listing-no-contents" ng-hide="!flags.http.loading">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
        <h3 class="spinner-text">{t}Loading{/t}...</h3>
      </div>
    </div>
    <div class="listing-no-contents ng-cloak" ng-show="!flags.http.loading && item === null">
      <div class="text-center p-b-15 p-t-15">
        <a href="[% routing.generate(routes.list) %]">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}The item doesn't exists or you don't have permission to see it.{/t}</h3>
          <h4>{t}Click here to return to the list{/t}</h4>
        </a>
      </div>
    </div>
    <div class="ng-cloak" ng-show="!flags.http.loading && flags.visible.grid && item">
      <div class="grid simple" >
        <div class="grid-body">
          {include file="ui/component/input/text.tpl" iCounter=true iField="keyword" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Name{/t}" iValidation=true}
          {include file="ui/component/select/keyword_type.tpl" iField="type" iRequired=true iTitle="{t}Type{/t}" sClass="form-control" sStyle="width:50%" ngModel="criteria.type"}
          {include file="ui/component/input/text.tpl" iCounter=true iField="value" iRequired=true iTitle="{t}Value{/t}" iValidation=true}
        </div>
      </div>
    </div>
  </div>
{/block}


