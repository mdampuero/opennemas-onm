{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Events{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="EventCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-calendar m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_events_list}">
    {t}Events{/t}
  </a>
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <button class="btn btn-loading btn-success text-uppercase" ng-click="submit()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
      {t}Save{/t}
    </button>
  </li>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {include file="ui/component/content-editor/accordion/published.tpl"}
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
            <input class="form-control" id="event_website" name="event_website" ng-model="item.event_website" type="text">
          </div>
        </div>
      </div>
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iTitle="{t}Frontpage image{/t}"}
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredInner" iTitle="{t}Inner image{/t}"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=5 imagepicker=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standard" rows=15 imagepicker=true}
    </div>
  </div>
{/block}
