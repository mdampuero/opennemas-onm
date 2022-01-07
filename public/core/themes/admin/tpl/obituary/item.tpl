{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Obituaries{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="ObituaryCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-shield fa-flip-vertical m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_obituaries_list}">
    {t}Obituaries{/t}
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
        {acl isAllowed="OBITUARY_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/published.tpl"}
        {/acl}
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="mortuary" icon="fa-university" title="{t}Mortuary{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="website" icon="fa-external-link" title="{t}Website{/t}" iRoute="item.website"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="maps" icon="fa-map-marker" title="{t}Google maps url{/t}" iRoute="item.maps"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
      </div>
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iTitle="{t}Featured in frontpage{/t}" types="photo,video,album"}
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredInner" iTitle="{t}Featured in inner{/t}" types="photo,video,album"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
      {include file="ui/component/input/text.tpl" iCounter=true iField="title_int" iRequired=true iTitle="{t}Inner title{/t}" iValidation=true}
      <div class="row">
        <div class="col-sm-6">
          {include file="ui/component/input/text.tpl" iCounter=true iField="agency" iTitle="{t}Signature{/t}"}
        </div>
        {is_module_activated name="NEWSLETTER_MANAGER"}
          <div class="col-sm-6">
            {include file="ui/component/input/text.tpl" iCounter=true iField="newsletter_agency" iTitle="{t}Newsletter Signature{/t}" iHelp="{t}Alternative signature for newsletter{/t}"}
          </div>
        {/is_module_activated}
      </div>
      {include file="ui/component/input/text.tpl" iCounter=true iField="pretitle" iTitle="{t}Pretitle{/t}"}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Summary{/t}" field="description" rows=5 imagepicker=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standard" rows=15 imagepicker=true}
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-preview">
    <div class="modal-body clearfix no-padding">
      <iframe ng-src="[% template.src %]" frameborder="0"></iframe>
    </div>
  </script>
  <script type="text/ng-template" id="modal-translate">
    {include file="common/modals/_translate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-draft">
    {include file="common/modals/_draft.tpl"}
  </script>
{/block}

