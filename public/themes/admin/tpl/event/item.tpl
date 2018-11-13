{extends file="base/admin.tpl"}

{block name="footer-js" append}
{javascripts}
<script>
jQuery(document).ready(function($) {
  $('#date').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: '{$cover->created|default:$smarty.now|date_format:"%Y-%m-%d"}'
  });
});
</script>
{/javascripts}
{/block}

{block name="content"}
<form name="form" method="POST" ng-controller="EventCtrl" ng-init="getItem({$id});">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-calendar m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <a class="no-padding" href="{url name=backend_events}" title="{t}Go back to list{/t}">
              <h4>
                {t}Events{/t}
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
                <label for="title" class="form-label">{t}Title{/t}</label>
                <div class="controls">
                  <input type="text" id="title" name="title" ng-model="item.title" value="{$cover->title|default:""}" required class="form-control"/>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-6">
                  <label class="form-label" for="endtime">{t}Start date{/t}</label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" id="closetime" name="event_startdate" type="datetime" value="{$poll->params['closetime']}">
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label class="form-label" for="endtime">{t}End date{/t}</label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" id="closetime" name="event_startdate" type="datetime" value="{$poll->params['closetime']}">
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label clearfix" for="description">
                  <div class="pull-left">{t}Description{/t}</div>
                </label>
                <div class="controls">
                  <textarea name="description" id="description" ng-model="item.description" onm-editor onm-editor-preset="simple"  class="form-control" rows="5"></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label clearfix" for="body">
                  <div class="pull-left">{t}Body{/t}</div>
                </label>
                <div class="controls">
                  <textarea name="body" id="body" ng-model="item.body" onm-editor onm-editor-preset="standard"  class="form-control" rows="15"></textarea>
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
                  <label for="category" class="form-label">{t}Category{/t}</label>
                  <div class="controls">
                    <onm-category-selector ng-model="item.category" categories="data.extra.categories" placeholder="{t}Select a category{/t}" default-value-text="{t}Select a category...{/t}" required />
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
              <div class="grid-title">
                <h4>{t}Schedule{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="form-group">
                  <label class="form-label" for="starttime">
                    {t}Publication start date{/t}
                  </label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" id="starttime" name="starttime" type="datetime" value="{if $album->starttime neq '0000-00-00 00:00:00'}{$album->starttime}{/if}">
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                    <span class="help-block">
                      {t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="endtime">
                    {t}Publication end date{/t}
                  </label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" id="endtime" name="endtime" type="datetime" value="{if $album->endtime neq '0000-00-00 00:00:00'}{$album->endtime}{/if}">
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Image{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="thumbnail-wrapper">
                  <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.cover }"></div>
                  <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.cover }">
                    <p>Are you sure?</p>
                    <div class="confirm-actions">
                      <button class="btn btn-link" ng-click="toggleOverlay('cover')" type="button">
                        <i class="fa fa-times fa-lg"></i>
                        {t}No{/t}
                      </button>
                      <button class="btn btn-link" ng-click="removeImage('cover');toggleOverlay('cover')" type="button">
                        <i class="fa fa-check fa-lg"></i>
                        {t}Yes{/t}
                      </button>
                    </div>
                  </div>
                  <div class="thumbnail-placeholder">
                    <div class="img-thumbnail" ng-if="!cover">
                      <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover">
                        <i class="fa fa-picture-o fa-2x"></i>
                        <h5>Pick an image</h5>
                      </div>
                    </div>
                    <div class="dynamic-image-placeholder" ng-if="cover">
                      <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="cover">
                        <div class="thumbnail-actions">
                          <div class="thumbnail-action remove-action" ng-click="toggleOverlay('cover')">
                            <i class="fa fa-trash-o fa-2x"></i>
                          </div>
                          <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover" media-picker-types="photo">
                            <i class="fa fa-camera fa-2x"></i>
                          </div>
                        </div>
                      </dynamic-image>
                    </div>
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
