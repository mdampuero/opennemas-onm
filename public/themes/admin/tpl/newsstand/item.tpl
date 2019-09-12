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
      {include file="ui/component/content-editor/accordion/category.tpl" field="item.category"}
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
        <span ng-if="!expanded.date">
          {include file="ui/component/icon/status.tpl" iFlag="date" iField="date" iRequired=true iValidation=true}
        </span>
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.slug }"></i>
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
        <div class="col-md-4">
          <div class="thumbnail-wrapper">
            <div>
              <div class="fileinput" ng-class="{ 'fileinput-exists': item.name, 'fileinput-new': !item.name }" data-trigger="fileinput" style="width:80%; margin:0 auto; display:block">
                <div class="thumbnail no-margin" style="width:100%;">
                  <div class="fileinput-new text-center" style="padding: 60px; background: #eee;" >
                    <i class="fa fa-picture-o fa-3x"></i>
                  </div>

                  <div class="text-center p-b-15 p-t-15" ng-show="thumbnailLoading">
                    <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
                    <h3 class="spinner-text">{t}Generating thumbnail{/t}...</h3>
                  </div>

                  <img id="thumbnail" ng-src="[% item.thumbnail_url %]" ng-show="!thumbnailLoading && item.thumbnail_url" style="max-width:100%">
                </div>
                <div>
                  <span class="btn btn-white btn-file btn-block m-b-15 m-t-15">
                    <i class="fa fa-newspaper-o"></i>
                    <span class="fileinput-new">{t}Add{/t} PDF</span>
                    <span class="fileinput-exists">{t}Change{/t}</span>
                    <input type="file" accept="application/pdf" id="cover-file-input" name="cover" onchange="angular.element(this).scope().generateThumbnailFromPDF()"/>
                    <input type="file" class="hidden" name="thumbnail" ng-model="item.cover_thumbnail">
                  </span>
                  <a class="btn btn-danger btn-block fileinput-exists delete no-margin m-b-15" data-dismiss="fileinput" href="#" ng-click="unsetCover()">
                    <i class="fa fa-trash-o"></i>
                    {t}Remove{/t}
                  </a>
                  <a class="btn btn-default btn-block fileinput-exists no-margin" ng-show="item.name" ng-href="[% '{$app.instance->getNewsstandShortPath()}' + item.path +  item.name %]" target="_blank">
                    <span class="fa fa-download"></span>
                    {t}Download{/t}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          {include file="ui/component/input/text.tpl" iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
          {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Description{/t}" field="description" rows=5}
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="footer-js" append}
  {javascripts}
    <script>
      $(document).ready(function($) {
        $('.fileinput').fileinput({
          name: 'cover',
          uploadtype: 'image'
        });
      });
    </script>
  {/javascripts}

  {javascripts src="
    @Common/components/pdfjs-dist/build/pdf.min.js,
    @Common/components/pdfjs-dist/build/pdf.worker.min.js" output="covers"}
  {/javascripts}
{/block}


