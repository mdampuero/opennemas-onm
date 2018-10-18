{extends file="base/admin.tpl"}

{block name="footer-js" append}
{javascripts}
<script>
jQuery(document).ready(function($) {
  $('#date').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: '{$cover->created|default:$smarty.now|date_format:"%Y-%m-%d"}'
  });

  $('.fileinput').fileinput({
    name: 'cover',
    uploadtype: 'image'
  });
});
</script>
{/javascripts}

{javascripts src="
  @Common/components/pdfjs-dist/build/pdf.min.js,
  @Common/components/pdfjs-dist/build/pdf.worker.min.js" output="covers"}{/javascripts}
{/block}

{block name="content"}
<form name="form" method="POST" ng-controller="NewsstandCtrl" ng-init="getItem({$id});" enctype="multipart/form-data">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-newspaper-o m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <a class="no-padding" href="{url name=backend_newsstands}" title="{t}Go back to list{/t}">
              <h4>
                {t}Covers{/t}
              </h4>
            </a>
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
              <button class="btn btn-loading btn-primary text-uppercase" ng-click="save()" ng-disabled="flags.http.saving || form.$invalid" type="button">
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
      <div class="row ng-cloak" ng-show="!flags.http.loading && item !== null">
        <div class="col-md-8">
          <div class="grid simple">
            <div class="grid-body">

              <div class="form-group">
                <label for="date" class="form-label">{t}File{/t}</label>
                <div class="controls">
                  <div class="fileinput" ng-class="{ 'fileinput-exists': item.name, 'fileinput-new': !item.name }" data-trigger="fileinput">
                    <div class="fileinput-new thumbnail text-center" style="padding: 5px 60px">
                      {t}Pick a file{/t}
                    </div>

                    <div class="text-center p-b-15 p-t-15" ng-show="thumbnailLoading">
                      <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
                      <h3 class="spinner-text">{t}Generating thumbnail{/t}...</h3>
                    </div>

                    <img id="thumbnail" ng-src="[% item.thumbnail_url %]" class="thumbnail" ng-show="!thumbnailLoading && item.thumbnail_url" style="max-width:35%">

                    <div>
                      <span class="btn btn-file">
                        <span class="fileinput-new">{t}Add PDF{/t}</span>
                        <span class="fileinput-exists">{t}Change{/t}</span>
                        <input type="file" accept="application/pdf" id="cover-file-input" name="cover" onchange="angular.element(this).scope().generateThumbnailFromPDF()"/>
                        {* <input type="hidden" name="cover" class="file-input" id="cover-file" value="1" ng-model="item.cover_thumbnail"> *}
                        <input type="file" class="hidden" name="thumbnail" ng-model="item.cover_thumbnail">
                      </span>
                      <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput" ng-click="unsetCover()">
                        <i class="fa fa-trash-o"></i>
                        {t}Remove{/t}
                      </a>
                      <a ng-show="item.name" ng-href="[% data.extra.KIOSKO_IMG_URL + item.path +  item.name %]" class="btn btn-white fileinput-exists delete" target="_blank">
                        <span class="fa fa-download"></span>
                        {t}Download{/t}
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="title" class="form-label">{t}Title{/t}</label>
                <div class="controls">
                  <input type="text" id="title" name="title" ng-model="item.title" value="{$cover->title|default:""}" required class="form-control"/>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label clearfix" for="body">
                  <div class="pull-left">{t}Description{/t}</div>
                </label>
                <div class="controls">
                  <textarea name="body" id="body" ng-model="item.description" onm-editor onm-editor-preset="simple"  class="form-control" rows="15"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="grid simple">
              <div class="grid-body">

                <div class="form-group">
                  <div class="checkbox">
                    <input ng-model="item.content_status" type="checkbox" value="0" id="content_status" name="content_status" ng-false-value="0" ng-true-value="1">
                    <label for="content_status">{t}Published{/t}</label>
                  </div>
                </div>

                <div class="form-group">
                  <div class="checkbox">
                    <input ng-model="item.favorite" type="checkbox" value="0" id="favorite" name="favorite" ng-false-value="0" ng-true-value="1">
                    <label for="favorite">{t}Favorite{/t}</label>
                  </div>
                </div>

                <div class="form-group">
                  <label for="category" class="form-label">{t}Category{/t}</label>
                  <div class="controls">
                    <onm-category-selector ng-model="item.category" categories="data.extra.categories" />
                  </div>
                </div>

                <div class="form-group">
                  <label for="metadata" class="form-label">{t}Keywords{/t}</label>
                  <span class="help">{t}List of words separated by commas{/t}.</span>
                  <div class="controls">
                    <onm-tag ng-model="item.tag_ids" locale="data.extra.locale" tags-list="data.extra.tags" check-new-tags="newAndExistingTagsFromTagList" get-suggested-tags="getSuggestedTags" load-auto-suggested-tags="loadAutoSuggestedTags" suggested-tags="suggestedTags" placeholder="{t}Write a tag and press Enter...{/t}"/>
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-label" for="slug">
                    {t}Slug{/t}
                  </label>
                  <span class="m-t-2 pull-right" ng-if="item.id">
                    <a href="{$smarty.const.INSTANCE_MAIN_DOMAIN}/[% item.uri %]" target="_blank">
                      <i class="fa fa-external-link"></i>
                      {t}Link{/t}
                    </a>
                  </span>
                  <div class="controls">
                    <input class="form-control" id="slug" name="slug" ng-model="item.slug" type="text" ng-disabled="item.content_status != '0'">
                  </div>
                </div>
              </div>
            </div>

            <div class="grid simple">
              <div class="grid-body">

                <div class="form-group">
                  <label for="date" class="form-label">{t}Date{/t}</label>
                  <div class="controls">
                    <div class="input-group">
                        <input class="form-control" datetime-picker datetime-picker-format="YYYY-MM-DD" id="date" name="date" ng-model="item.date" type="datetime" required>
                      <span class="input-group-addon" id="basic-addon2"><span class="fa fa-calendar"></span></span>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="price" class="form-label">{t}Price{/t}</label>
                  <span class="help">{t}Split decimals with a dot{/t}.</span>
                  <div class="controls">
                    <input ng-model="item.price" min="0" type="number" step="0.01" id="price" name="price" required />
                  </div>
                </div>

                <div class="form-group">
                  <label for="type" class="form-label">{t}Type{/t}</label>
                  <div class="controls">
                    <select name="type" id="type" required ng-model="item.type">
                      <option ng-value="0" ng-selected="item.type == false">{t}Item{/t}</option>
                      <option ng-value="1" ng-selected="item.type == true">{t}Subscription{/t}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
