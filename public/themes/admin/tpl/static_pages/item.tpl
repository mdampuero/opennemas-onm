{extends file="base/admin.tpl"}

{block name="content"}
<form name="form" ng-controller="StaticPageCtrl" ng-init="getItem({$id});">
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
              <a class="no-padding" href="{url name=backend_static_pages_list}" title="{t}Go back to list{/t}">
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
              {include file="ui/component/content-editor-accordion/published.tpl"}
            </div>
            {/acl}

            {include file="ui/component/content-editor-accordion/tags.tpl"}
            {include file="ui/component/content-editor-accordion/slug.tpl" route="[% routing.generate('frontend_static_page', { slug: item.slug }) %]"}
          </div>
        </div>
      </div>
      <div class="col-md-8 col-md-pull-4">
        <div class="grid simple">
          <div class="grid-body">
            <div class="form-group">
              <label for="title" class="form-label">{t}Title{/t}</label>
              <div class="controls">
                <input type="text" id="title" name="title" ng-model="item.title" required class="form-control"/>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label clearfix" for="body">
                <div class="pull-left">{t}Body{/t}</div>
              </label>
              {acl isAllowed='PHOTO_ADMIN'}
              <div class="pull-right">
                <div class="btn btn-mini" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="5" media-picker-target="editor.body">
                  <i class="fa fa-plus"></i>
                  {t}Insert image{/t}
                </div>
              </div>
              {/acl}
              <div class="controls">
                <textarea name="body" id="body" ng-model="item.body" onm-editor onm-editor-preset="standard"  class="form-control" rows="30"></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</form>
{/block}
