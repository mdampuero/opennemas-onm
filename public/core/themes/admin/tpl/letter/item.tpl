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
  <a class="no-padding" href="{url name=backend_letters_list}">
    {t}Letters to the Editor{/t}
  </a>
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks hidden-xs ng-cloak" ng-if="draftSaved">
        <h5>
          <i class="p-r-15">
            <i class="fa fa-check"></i>
            {t}Draft saved at {/t}[% draftSaved %]
          </i>
        </h5>
      </li>
      <li class="quicklinks">
      <a class="btn btn-link" class="" ng-click="expansibleSettings()" title="{t 1=_('Letter')}Config form: '%1'{/t}">
        <span class="fa fa-cog fa-lg"></span>
      </a>
    </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="submit()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
          <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
          {t}Save{/t}
        </button>
      </li>
    </ul>
  </div>
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
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
      <div class="row">
        <div class="form-group col-md-6">
          {include file="ui/component/input/text.tpl" iCounter=false iField="author" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Author{/t}" iValidation=true}
        </div>
        <div class="form-group col-md-6">
          {include file="ui/component/input/text.tpl" iCounter=false iField="email" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Email{/t}" iValidation=true}
        </div>
      </div>
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standar" rows=15}
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-draft">
    {include file="common/modals/_draft.tpl"}
  </script>
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
