{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Letters to the Editor{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="LetterCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-envelope m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_articles_list}">
    {t}Letters to the Editor{/t}
  </a>
{/block}

{block name="primaryActions"}
  <li class="quicklinks hidden-xs ng-cloak" ng-if="draftSaved">
    <h5>
      <i class="p-r-15">
        <i class="fa fa-check"></i>
        {t}Draft saved at {/t}[% draftSaved %]
      </i>
    </h5>
  </li>
  <li class="quicklinks">
    <button class="btn btn-loading btn-success text-uppercase" ng-click="submit()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
      {t}Save{/t}
    </button>
  </li>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {include file="ui/component/content-editor/accordion/published.tpl"}
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      <div class="grid-collapse-title">
        {include file="ui/component/input/text.tpl" iField="url" iNgActions="ng-blur=\"generate()\"" iTitle="{t}Related url{/t}"}
      </div>
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iTitle="{t}Featured in frontpage{/t}" types="photo"}
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredInner" iTitle="{t}Featured in inner{/t}" types="photo"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standar" rows=15}
      <h4>{t}Author information{/t}</h4>
      <div class="row">
        <div class="form-inline-block">
          <div class="form-group col-md-6">
            {include file="ui/component/input/text.tpl" iCounter=false iField="author" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Nickname{/t}" iValidation=true}
          </div>
          <div class="form-group col-md-6">
            {include file="ui/component/input/text.tpl" iCounter=false iField="email" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Email{/t}" iValidation=true}
          </div>
        </div>
      </div>
      {include file="ui/component/input/text.tpl" iField="created" iNgActions="ng-blur=\"generate()\" readonly" iTitle="{t}Created at{/t}"}
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-translate">
    {include file="common/modals/_translate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-draft">
    {include file="common/modals/_draft.tpl"}
  </script>
{/block}
