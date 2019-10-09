{extends file="base/admin.tpl"}

{block name="content"}
<form name="form" ng-controller="OpinionCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-quote-right m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_opinions_list}" title="{t}Go back to list{/t}">
                {t}Opinions{/t}
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
            <li class="quicklinks hidden-xs">
              <button class="btn btn-white" id="preview-button" ng-click="preview()" type="button" id="preview_button">
                <i class="fa fa-desktop" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.generating_preview }" ></i>
                {t}Preview{/t}
              </button>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-loading btn-success text-uppercase" ng-click="submit()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
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
        <a href="[% routing.generate('backend_opinions_list') %]">
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
            <div class="grid-collapse-title">
              {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
              <div class="m-t-5">
                {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Home{/t}" field="in_home"}
              </div>
              <div class="m-t-5">
                {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Favorite{/t}" field="favorite"}
              </div>
              <div class="m-t-5">
                {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
              </div>
            </div>
            {include file="ui/component/content-editor/accordion/author.tpl" required=true blog=true}
            {include file="ui/component/content-editor/accordion/tags.tpl"}
            {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
            {include file="ui/component/content-editor/accordion/scheduling.tpl"}
          </div>
        </div>

        <div class="grid simple">
          <div class="grid-body no-padding">
            <div class="grid-collapse-title">
              <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
            </div>
            {include file="ui/component/content-editor/accordion/image.tpl" title="{t}Frontpage image{/t}" field="photo1" footer="item.img1_footer"}
            {include file="ui/component/content-editor/accordion/image.tpl" title="{t}Inner image{/t}" field="photo2" footer="item.img2_footer"}
          </div>
        </div>

        <div class="grid simple" ng-show="data.extra.extra_fields !== undefined && data.extra.extra_fields">
          <div class="grid-body no-padding">
            <div class="grid-collapse-title">
              <i class="fa fa-magic"></i>
              {t}Additional data{/t}
            </div>

            <div class="grid-collapse-body expanded">
              <autoform ng-model="item" fields-by-module="data.extra.extra_fields"/>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8 col-md-pull-4">
        <div class="grid simple">
          <div class="grid-body">
            {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
            {include file="ui/component/content-editor/textarea.tpl" title="{t}Summary{/t}" field="summary" rows=5 imagepicker=true}
            {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standard" rows=30 imagepicker=true}
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
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
</form>
{/block}
