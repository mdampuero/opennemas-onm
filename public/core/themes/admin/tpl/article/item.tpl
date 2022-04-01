{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Articles{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="ArticleCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-file-text m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_articles_list}">
    {t}Articles{/t}
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
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('Article')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-white m-r-5" id="preview-button" ng-click="preview()" type="button" id="preview_button">
          <i class="fa fa-desktop" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.generating_preview }" ></i>
          {t}Preview{/t}
        </button>
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
        {acl isAllowed="ARTICLE_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/published.tpl"}
        {/acl}
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
        </div>
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/checkbox.tpl" field="frontpage" title="{t}Suggested for frontpage{/t}"}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/liveBlogPosting.tpl"}
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/category.tpl" field="categories[0]"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="params.bodyLink" icon="fa-external-link" title="{t}External link{/t}" iRoute="item.params.bodyLink"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
      </div>
      {is_module_activated name="es.openhost.module.advancedSubscription"}
        <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.lists = !expanded.lists">
        <i class="fa fa-list m-r-10"></i>{t}Lists{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.lists }"></i>
        </div>
        <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded' : expanded.lists }">
          <div ng-show="!data.extra.subscriptions || data.extra.subscriptions.length === 0">
            <i class="fa fa-warning m-r-5 text-warning"></i>
            {t escape=off 1="[% routing.generate('backend_subscriptions_list') %]"}There are no enabled <a href="%1">subscriptions</a>{/t}
          </div>
          <div class="form-group no-margin" ng-show="data.extra.subscriptions && data.extra.subscriptions.length > 0">
            <div class="checkbox m-b-5" ng-repeat="subscription in data.extra.subscriptions">
              <input checklist-model="item.subscriptions" checklist-value="subscription.pk_user_group" id="checkbox-[% $index %]" type="checkbox">
              <label for="checkbox-[% $index %]">[% subscription.name %]</label>
            </div>
            <div class="help m-l-3" ng-if="isHelpEnabled()">
              <i class="fa fa-info-circle m-r-5 text-info"></i>
              {t}The content will be fully available only for subscribers in the selected subscriptions{/t}
            </div>
          </div>
        </div>
      {/is_module_activated}
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iTitle="{t}Featured in frontpage{/t}" types="photo,video,album"}
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredInner" iTitle="{t}Featured in inner{/t}" types="photo,video,album"}
      {include file="common/component/related-contents/_related-content.tpl" iName="relatedFrontpage" iTitle="{t}Related in frontpage{/t}"}
      {include file="common/component/related-contents/_related-content.tpl" iName="relatedInner" iTitle="{t}Related in inner{/t}"}
      {include file="ui/component/content-editor/accordion/additional-data.tpl"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
      {include file="ui/component/input/text.tpl" iCounter=true iField="title_int" iRequired=true iSource="title" iTitle="{t}Inner title{/t}" iValidation=true}
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
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standard" rows=15 imagepicker=true contentPicker=true}
    </div>
  </div>
  {include file="ui/component/content-editor/liveBlogUpdate.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-preview">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()" type="button">&times;</button>
      <h4 class="modal-title">
        {t}Preview{/t}
      </h4>
    </div>
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
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}

