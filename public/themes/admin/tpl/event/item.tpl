{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="EventCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});">
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
                <a class="no-padding" href="{url name=backend_events_list}">
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
            <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="config.locale.multilanguage && config.locale.available">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks ng-cloak" ng-if="config.locale.multilanguage && config.locale.available">
              <translator item="data.item" keys="data.extra.keys" ng-model="config.locale.selected" options="config.locale"></translator>
            </li>
          </ul>
          <div class="pull-right" ng-if="!flags.http.loading">
            <ul class="nav quick-section">
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
              <div class="grid-collapse-title">
                {include file="ui/component/content-editor/accordion/published.tpl"}
                <div class="m-t-5">
                  {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
                </div>
              </div>
              {include file="ui/component/content-editor/accordion/category.tpl" field="item.categories[0]"}
              {include file="ui/component/content-editor/accordion/tags.tpl"}
              {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getL10nUrl(routing.generate('frontend_event_show', { slug: item.slug })) %]"}
              {include file="ui/component/content-editor/accordion/scheduling.tpl"}
            </div>
          </div>
          <div class="grid simple">
            <div class="grid-body no-padding">
              <div class="grid-collapse-title">
                <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.when = !expanded.when">
                <i class="fa fa-calendar m-r-10"></i>{t}Event date{/t}
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
                  <span class="help-block">
                    {t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"} ({$app.locale->getTimeZone()->getName()})
                  </span>
                </div>
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.where = !expanded.where">
                <i class="fa fa-map-marker m-r-10"></i>
                {t}Event location{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.where }"></i>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.where }">
                <div class="form-group">
                  <label class="form-label" for="event_place">{t}Place{/t}</label>
                  <div class="controls">
                    <input class="form-control"  id="event_place" name="event_place" ng-model="item.event_place" type="text">
                  </div>
                </div>
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.external_website = !expanded.external_website">
                <i class="fa fa-external-link m-r-10"></i>
                {t}External website{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.external_website }"></i>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.external_website }">
                <div class="form-group no-padding">
                  <label class="form-label" for="event_website">{t}Website URL{/t}</label>
                  <div class="controls">
                    <input class="form-control"  id="event_website" name="event_website" ng-model="item.event_website" type="text">
                  </div>
                </div>
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.cover = !expanded.cover">
                <i class="fa fa-image m-t-5"></i> {t}Image{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.cover }"></i>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.cover }">
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
        </div>
        <div class="col-md-8 col-md-pull-4">
          <div class="grid simple">
            <div class="grid-body">
              {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
              {include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=5 imagepicker=true}
              {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standard" rows=15 imagepicker=true}
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
