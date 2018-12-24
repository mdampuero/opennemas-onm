{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="EventCtrl" ng-init="getItem({$id});">
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
              <h4>
                <a class="no-padding" href="{url name=backend_events_list}" title="{t}Go back to list{/t}">
                  {t}Events{/t}
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
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.published }">
              </div>
              <div class="grid-collapse-title">
                <div class="form-group">
                  <div class="checkbox">
                    <input id="content-status" ng-false-value="0" ng-model="item.content_status" ng-true-value="1" type="checkbox">
                    <label for="content-status">{t}Published{/t}</label>
                  </div>
                </div>
                {is_module_activated name="COMMENT_MANAGER"}
                  <div class="form-group no-margin">
                    <div class="checkbox">
                    <input id="with-comments" ng-false-value="0" ng-model="item.with_comment" ng-true-value="1" type="checkbox">
                      <label for="with-comments">{t}Allow comments{/t}</label>
                    </div>
                  </div>
                {/is_module_activated}
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.category = !expanded.category">
                <input name="categories" ng-value="categories" type="hidden">
                <i class="fa fa-bookmark m-r-10"></i>{t}Category{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.category || item.categories.length === 0 || !item.categories[0]" ng-class="{ 'badge-danger' : item.categories.length === 0 || !item.categories[0] }">
                  <span ng-show="item.categories.length === 0 || !item.categories[0]">
                    <strong>{t}Not selected{/t}</strong>
                  </span>
                  <span ng-show="item.categories.length !== 0 && item.categories[0] && !flags.categories.none">
                    <strong><span ng-repeat="category in data.extra.categories|filter:{ pk_content_category: item.categories[0]}">[% category.title %]</span></strong>
                  </span>
                </span>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.category }">
                <div class="form-group">
                  <div class="controls">
                    <onm-category-selector class="block" ng-model="item.categories[0]" categories="data.extra.categories" placeholder="{t}Select a category{/t}" default-value-text="{t}Select a category...{/t}" required />
                  </div>
                </div>
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.schedule = !expanded.schedule">
                <i class="fa fa-calendar m-r-10"></i>{t}Schedule{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.schedule }"></i>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.schedule && item.endtime">
                  <strong>{t}End{/t}</strong>
                  <span class="hidden-lg visible-xlg pull-right">: [% item.endtime %]</span>
                </span>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.schedule && item.starttime">
                  <strong>{t}Start{/t}</strong>
                  <span class="hidden-lg visible-xlg pull-right">: [% item.starttime %]</span>
                </span>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.schedule }">
                <div class="form-group">
                  <label class="form-label" for="starttime">
                    {t}Publication start date{/t}
                  </label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" datetime-picker datetime-picker-timezone="{$timezone}" datetime-picker-use-current=true datetime-picker-min="item.created" id="starttime" name="starttime" ng-model="item.starttime" type="datetime">
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
                      <input class="form-control" datetime-picker datetime-picker-timezone="{$timezone}" datetime-picker-use-current=true datetime-picker-min="item.endtime" id="endtime" name="endtime" ng-model="item.endtime" type="datetime">
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                  </div>
                </div>
                <span><i class="fa fa-info-circle text-info"></i> {t}This content will only be available in the time range specified above.{/t}</span>
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.tags = !expanded.tags">
                <i class="fa fa-tag m-r-10"></i>{t}Tags{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.tags }"></i>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.tags" ng-class="{ 'badge-danger' : item.tags.length === 0 }">
                  <span ng-show="item.tags.length === 0"><strong>{t}No tags{/t}</strong></span>
                  <span ng-show="item.tags.length != 0">
                    <strong>[% item.tags.length %] {t}Tags{/t}</span></strong>
                  </span>
                </span>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.tags }">
                <div class="form-group">
                  <label for="metadata" class="form-label">{t}Tags{/t}</label>
                  <div class="controls">
                    {include file="ui/component/tags-input/tags.tpl" ngModel="item.tags"}
                  </div>
                </div>
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.slug = !expanded.slug">
                <i class="fa fa-globe m-r-10"></i>{t}Slug{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.slug }"></i>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.slug }">
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
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.when = !expanded.when">
                <i class="fa fa-clock-o m-r-10"></i>
                {t}When{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.when }"></i>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.when }">
                <div class="row">
                  <div class="form-group col-sm-6">
                    <label class="form-label" for="event_start_date">{t}Start date{/t}</label>
                    <div class="controls">
                      <div class="input-group">
                        <input class="form-control" datetime-picker datetime-picker-format="YYYY-MM-DD" datetime-picker-timezone="{$timezone}" datetime-picker-max="item.event_end_date" datetime-picker-use-current="true" id="event_start_date" name="event_start_date" ng-model="item.event_start_date" type="datetime">
                        <span class="input-group-addon add-on">
                          <span class="fa fa-calendar"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-sm-6">
                    <label class="form-label" for="event_start_hour">{t}Start hour{/t}</label>
                    <div class="controls">
                      <div class="input-group">
                        <input class="form-control" datetime-picker datetime-picker-format="HH:mm" datetime-picker-timezone="{$timezone}" datetime-picker-min="item.event_end_date" datetime-picker-use-current="true" id="event_start_hour" name="event_start_hour" ng-model="item.event_start_hour" type="datetime">
                        <span class="input-group-addon add-on">
                          <span class="fa fa-clock-o"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-6">
                    <label class="form-label" for="event_end_date">{t}End date{/t}</label>
                    <div class="controls">
                      <div class="input-group">
                        <input class="form-control" datetime-picker datetime-picker-format="YYYY-MM-DD" datetime-picker-timezone="{$timezone}" datetime-picker-min="item.event_start_date" id="event_end_date" name="event_end_date" ng-model="item.event_end_date" type="datetime">
                        <span class="input-group-addon add-on">
                          <span class="fa fa-calendar"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-sm-6">
                    <label class="form-label" for="event_end_hour">{t}End hour{/t}</label>
                    <div class="controls">
                      <div class="input-group">
                        <input class="form-control" datetime-picker datetime-picker-format="HH:mm" datetime-picker-timezone="{$timezone}" datetime-picker-min="item.event_start_hour" datetime-picker-use-current="true" id="event_end_hour" name="event_end_hour" ng-model="item.event_end_hour" type="datetime">
                        <span class="input-group-addon add-on">
                          <span class="fa fa-clock-o"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.where = !expanded.where">
                <i class="fa fa-map-marker m-r-10"></i>
                {t}Where{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.where }"></i>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.where }">
                <div class="form-group">
                  <label class="form-label" for="event_place">{t}Place{/t}</label>
                  <div class="controls">
                    <input class="form-control"  id="event_place" name="event_place" ng-model="item.event_place" type="text">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="event_website">{t}Website URL{/t}</label>
                  <div class="controls">
                    <input class="form-control"  id="event_website" name="event_website" ng-model="item.event_website" type="text">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                <i class="fa fa-picture-o m-r-10"></i>
                {t}Image{/t}
              </h4>
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
                  <div class="img-thumbnail" ng-show="!cover">
                    <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover">
                      <i class="fa fa-picture-o fa-2x"></i>
                      <h5>Pick an image</h5>
                    </div>
                  </div>
                  <div class="dynamic-image-placeholder" ng-show="cover">
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
      </div>
    </div>
  </form>
{/block}
