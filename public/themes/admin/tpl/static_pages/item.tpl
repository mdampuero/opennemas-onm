{extends file="base/admin.tpl"}

{block name="content"}
<form name="form" ng-controller="StaticPageCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-file-o m-r-10"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/238735-opennemas-p%C3%A1ginas-est%C3%A1ticas" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="[% routing.generate('backend_static_pages_list') %]" title="{t}Go back to list{/t}">
                {t}Static Pages{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <h4>{if empty($id)}{t}Create{/t}{else}{t}Edit{/t}{/if}</h4>
          </li>
          <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="config.locale.multilanguage && config.locale.available">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks ng-cloak" ng-if="config.locale.multilanguage && config.locale.available">
            <translator item="data.item" keys="data.extra.keys" ng-model="config.locale.selected" options="config.locale"></translator>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving || form.$invalid" type="button">
                <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="listing-no-contents" ng-hide="!flags.http.loading">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
        <h3 class="spinner-text">{t}Loading{/t}...</h3>
      </div>
    </div>
    <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
      <div class="text-center p-b-15 p-t-15">
        <a href="[% routing.generate('backend_users_list') %]">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find the item{/t}</h3>
          <h4>{t}Click here to return to the list{/t}</h4>
        </a>
      </div>
    </div>
    <div class="row ng-cloak" ng-show="!flags.http.loading && item">
      <div class="col-md-4 col-md-push-8">
        <div class="grid simple">
          <div class="grid-body no-padding">
            {acl isAllowed="STATIC_PAGE_AVAILABLE"}
            <div class="grid-collapse-title">
              {include file="ui/component/content-editor/accordion/published.tpl"}
            </div>
            {/acl}

            {include file="ui/component/content-editor/accordion/tags.tpl"}
            {include file="ui/component/content-editor/accordion/slug.tpl" route="[% routing.generate('frontend_static_page', { slug: item.slug }) %]"}
          </div>
        </div>
      </div>
      <div class="col-md-8 col-md-pull-4">
        <div class="grid simple">
          <div class="grid-body">
            {include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true}
            {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standard" rows=30 required=true imagepicker=true l10n=true}
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
