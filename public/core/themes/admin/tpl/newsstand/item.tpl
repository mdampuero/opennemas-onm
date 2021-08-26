{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Newsstand{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="NewsstandCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-newspaper-o m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_newsstands_list}">
    {t}Newsstand{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {acl isAllowed="KIOSKO_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
        {/acl}
        <div class="m-t-5">
          {acl isAllowed="KIOSKO_FAVORITE"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Favorite{/t}" field="favorite"}
          {/acl}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/category.tpl" field="categories[0]"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i>
        {t}Parameters{/t}
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.date = !expanded.date">
        <i class="fa fa-calendar m-r-10"></i>
        {t}Date{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.slug }"></i>
        <span class="pull-right" ng-if="!expanded.date">
          {include file="common/component/icon/status.tpl" iFlag="date" iForm="form.date" iNgModel="item.date" iRequired=true iValidation=true}
        </span>
        <span class="badge badge-default m-r-10 pull-right text-bold" ng-show="!expanded.date && item.date">
          [% item.date | moment : 'YYYY-MM-DD' %]
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.date }">
        {include file="ui/component/input/datetime.tpl" iClass="no-margin" iFlag="date" iField="date" iFormat="YYYY-MM-DD" iRequired=true iValidation=true}
      </div>
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="row">
        <div class="col-lg-4">
          <div class="row">
            <div class="col-lg-12 col-lg-offset-0 col-sm-6 col-sm-offset-3">
              <input accept="application/pdf" class="hidden" id="file" name="file" file-model="item.path" type="file"/>
              <input class="hidden" id="thumbnail" name="thumbnail" file-model="item.thumbnail" type="file"/>
              <div class="overlay overlay-white open p-t-50 text-center" ng-if="flags.generate.preview">
                <i class="fa fa-circle-o-notch fa-spin fa-3x"></i>
                <p class="m-t-15 text-center">
                <strong>
                  {t}Generating thumbnail{/t}...
                </strong>
                </p>
              </div>
              <div class="p-b-30 p-l-30 p-r-30 p-t-35">
                <div class="text-center">
                  <img class="img-thumbnail" ng-if="!item.related_contents" ng-src="[% preview %]" ng-show="preview" style="max-height: 180px;">
                  <div class="dynamic-image-placeholder" ng-if="item.related_contents">
                    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="getFeaturedMedia(item, 'featured_frontpage')" transform="zoomcrop,200,200">
                    </dynamic-image>
                  </div>
                  <i class="fa fa-warning fa-3x text-warning" ng-if="!item.path"></i>
                  <p class="m-t-15 text-center nowrap">
                    <strong ng-if="item.path" title="[% getFileName() %]">
                      [% getFileName() %]
                    </strong>
                    <strong ng-if="!item.path">
                      {t}No file selected{/t}
                    </strong>
                  </p>
                </div>
                <label class="btn btn-default btn-block m-t-15" for="file">
                  <span ng-if="!item.path">
                    <i class="fa fa-plus m-r-5"></i>
                    {t}Add{/t}
                  </span>
                  <span ng-if="item.path">
                    <i class="fa fa-edit m-r-5"></i>
                    {t}Change{/t}
                  </span>
                </label>
                <a class="btn btn-white btn-block m-t-15" ng-show="item.path && !item.path.name" ng-href="[% data.extra.paths.newsstand +  '/' + item.path %]" target="_blank">
                  <i class="fa fa-download m-r-5"></i>
                  {t}Download{/t}
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
          {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Description{/t}" field="description" rows=5}
        </div>
      </div>
    </div>
  </div>
{/block}
