{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Videos{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="VideoCtrl" ng-init="flags.visible.grid = false; forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-film m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_videos_list}">
    {t}Videos{/t}
  </a>
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <button class="btn btn-loading btn-success text-uppercase" ng-click="submit($event)" type="button">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
      {t}Save{/t}
    </button>
  </li>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {acl isAllowed="VIDEO_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
        {/acl}
        <div class="m-t-5">
          {acl isAllowed="VIDEO_FAVORITE"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Favorite{/t}" field="favorite"}
          {/acl}
        </div>
        <div class="m-t-5">
          {acl isAllowed="VIDEO_HOME"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Home{/t}" field="in_home"}
          {/acl}
        </div>
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/category.tpl" field="categories[0]"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
  <div class="grid simple" ng-show="type == 'script' || type == 'external'">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
      </div>
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iRequired="type == 'script' || type == 'external'" iTitle="{t}Frontpage image{/t}"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <span ng-if="type == 'external'">
        {include file="video/partials/_form_video_external.tpl"}
      </span>
      <span ng-if="type == 'script'">
        {include file="video/partials/_form_video_script.tpl"}
      </span>
      <span ng-if="type == 'web-source'">
        {include file="video/partials/_form_video_panorama.tpl"}
      </span>
    </div>
  </div>
{/block}

{block name="grid" append}
  <div class="row ng-cloak text-center" ng-show="!flags.http.loading && !type">
    <h4>{t}Pick the method to add the video{/t}</h4>
    <div class="video-type-selector">
      <button ng-click="setType('web-source')" class="clearfix btn btn-white video-type-selector-button">
        <div class="video-selector-icon">
          <i class="fa fa-youtube fa-3x"></i>
        </div>
        <div class="video-selector-text">
          {t}Link video from other web video services{/t}
        </div>
      </button>
      <button ng-click="setType('script')" class="clearfix btn btn-white video-type-selector-button">
        <div class="video-selector-icon">
          <i class="fa fa-file-code-o fa-3x"></i>
        </div>
        <div class="video-selector-text">
          {t}Use HTML code{/t}
        </div>
      </button>
      <button ng-click="setType('external')" class="clearfix btn btn-white video-type-selector-button">
        <div class="video-selector-icon">
          <i class="fa fa-film fa-3x"></i>
        </div>
        <div class="video-selector-text">
          {t}Use file video URLs (External HTML5/FLV){/t}
        </div>
      </button>
    </div>
  </div>
{/block}
