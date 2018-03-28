{extends file="base/admin.tpl"}

{block name="footer-js" append}
{javascripts}
<script type="text/javascript">
jQuery(document).ready(function($) {
  $('#formulario').on('change', '#title', function() {
    fill_tags(jQuery('#title').val(), '#metadata', '{url name=admin_utils_calculate_tags}');
  });

  $('#starttime, #endtime').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    useCurrent: false,
    minDate: '{$advertisement->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}'
  });

  $('#starttime').on('dp.change', function(e) {
    $('#endtime').data('DateTimePicker').minDate(e.date);
  });
  $('#endtime').on('dp.change', function(e) {
    $('#starttime').data('DateTimePicker').maxDate(e.date);
  });
});
</script>
{/javascripts}
{/block}

{block name="content"}
<form action="{if $advertisement->id}{url name=admin_ad_update id=$advertisement->id}{else}{url name=admin_ad_create}{/if}" method="post" id="formulario" name="AdvertisementForm" ng-controller="AdvertisementCtrl" ng-init="init(
    {json_encode($advertisement->params)|clear_json},
    {json_encode($advertisement->fk_content_categories)|clear_json},
    {json_encode($advertisement->positions)|clear_json}
  );
  with_script = {if empty($advertisement->with_script)}0{else}{{$advertisement->with_script}}{/if};
  extra = {json_encode($extra)|clear_json};">
    <div class="page-navbar actions-navbar" ng-controller="AdBlockCtrl">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-bullhorn"></i>
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/818598-opennemas-como-crear-y-gestionar-publicidades" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
                <span class="hidden-xs">{t}Advertisements{/t}</span>
                <span class="visible-xs-inline">{t}Ads{/t}</span>
              </h4>
            </li>
            <li class="quicklinks seperate hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {if empty($advertisement->id)}
                  {t}Creating banner{/t}
                {else}
                  {t}Editing banner{/t}
                {/if}
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_ads}" title="{t}Go back{/t}">
                  <i class="fa fa-reply fa-lg"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" data-text="{t}Saving{/t}..." ng-disabled="AdvertisementForm.$invalid" type="submit" id="save-button">
                    <i class="fa fa-save"></i>
                    <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="row">
        <div class="col-md-8">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label class="form-label" for="title">
                  {t}Title{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="title" name="title" required type="text" value="{$advertisement->title|clearslash|escape:"html"|default:""}"/>
                </div>
              </div>
              <div class="hidden">
                <label for="metadata" class="form-label">
                  {t}Keywords{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="metadata" name="metadata" required title="Metadatos" type="hidden" value="{$advertisement->metadata|strip|default:''}">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">
                  {t}Type{/t}
                </label>
                <div class="controls row">
                  <div class="col-sm-3">
                    <div class="radio">
                      <input id="image" name="with_script" ng-model="with_script" {if $with_script == 0}checked{/if} type="radio" value="0">
                      <label for="image" title="{t}Image or Flash object{/t}">{t}Image or Flash object{/t}</label>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="radio">
                      <input id="html" name="with_script" ng-model="with_script"  {if $with_script == 1}checked{/if} type="radio" value="1">
                      <label for="html" title="{t}HTML or Javascript code{/t}">{t}HTML or Javascript code{/t}</label>
                    </div>
                  </div>
                  {if !empty($server_url)}
                  <div class="col-sm-3">
                    <div class="radio">
                      <input id="open-x" name="with_script" ng-model="with_script"  {if $with_script == 2}checked{/if} type="radio" value="2">
                      <label for="open-x" title="{t}OpenX{/t}">{t}OpenX{/t}</label>
                    </div>
                  </div>
                  {/if}
                  <div class="col-sm-3">
                    <div class="radio">
                      <input id="dfp" name="with_script" ng-model="with_script" {if $with_script == 3}checked{/if} type="radio" value="3">
                      <label for="dfp" title="{t}Google DFP{/t}">{t}Google DFP{/t}</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group ng-cloak">
                <div ng-show="!with_script || with_script == 0">
                  {include file="advertisement/partials/advertisement_images.tpl"}
                </div>
                <div  ng-show="with_script == 1">
                  <textarea name="script" id="script" class="form-control" rows="10" ng-model="script">{$advertisement->script|escape:'htmlall'|default:'&lt;script type="text/javascript"&gt;/* JS code */&lt;/script&gt;'}</textarea>
                </div>
                <div  ng-show="'{$server_url}' && with_script == 2">
                  <label for="openx_zone">{t}OpenX zone id{/t}</label>
                  <input type="text" name="openx_zone_id" value="{$advertisement->params['openx_zone_id']}">
                  <div class="help-block"><small>{t 1=$server_url}OpenX/Revive Ad server uses an id to identify an advertisement. Please fill the zone id from your OpenX/Revive server %1{/t}</small></div>
                </div>
                <div  ng-show="with_script == 3">
                  <label for="googledfp_zone_id">
                    {t}Google DFP unit id{/t}
                  </label>
                  <input class="form-control" type="text" name="googledfp_unit_id" ng-model="googledfp_unit_id" value="{$advertisement->params['googledfp_unit_id']}">
                  <div class="help-block">{t 1=$server_url}Google DFP uses an unit ID to identify an advertisement. Please fill the zone id from your Google DFP panel{/t}</div>
                </div>
              </div>
              <div class="form-group" id="div_url1" ng-show="with_script == 0">
                <label for="url" class="form-label">{t}Url{/t}</label>
                <div class="controls">
                  <input type="url" id="url" name="url" class="form-control" value="{$advertisement->url}" placeholder="http://" ng-required="with_script == 0" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body no-padding">
              <div class="grid-collapse-title">
                <div class="checkbox">
                  <input type="checkbox" name="content_status" id="content_status" value="1"
                  {if isset($advertisement->content_status) && $advertisement->content_status == 1}checked="checked"{/if} {acl isNotAllowed="ADVERTISEMENT_AVAILABLE"}disabled="disabled"{/acl} />
                  <label class="form-label" for="content_status">
                    {t}Published{/t}
                  </label>
                </div>
              </div>
              <div class="grid-collapse-title pointer" ng-class="{ 'open': expanded.mark }" ng-click="expanded.mark = !expanded.mark">
                <i class="fa fa-tags m-r-5"></i> {t}Mark{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.mark }"></i>
                <span class="badge badge-default m-r-10 ng-cloak pull-right text-uppercase" ng-if="!expanded.mark">
                  <span ng-if="params.orientation === 'top'">{t}Top{/t}</span>
                  <span ng-if="params.orientation === 'right'">{t}Right{/t}</span>
                  <span ng-if="params.orientation === 'bottom'">{t}Bottom{/t}</span>
                  <span ng-if="params.orientation === 'left'">{t}Left{/t}</span>
                </span>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.mark }">
                <div class="row">
                  <div class="col-xs-6 form-group">
                    <div class="radio">
                      <input id="mark-top" name="orientation" ng-model="params.orientation" type="radio" value="top">
                      <label for="mark-top">
                        {t}Top{/t}
                      </label>
                    </div>
                    <label class="pointer" for="mark-top" style="padding-left: 25px; position: relative;">
                      <small style="display: block; height: 20px; text-align: center; width: 80px;">{t}Advertisement{/t}</small>
                      <div style="background: rgba(0, 0, 0, .25); height: 80px; width: 80px;"></div>
                    </label>
                  </div>
                  <div class="col-xs-6 form-group">
                    <div class="radio">
                      <input id="mark-right" name="orientation" ng-model="params.orientation" type="radio" value="right">
                      <label for="mark-right">
                        {t}Right{/t}
                      </label>
                    </div>
                    <label class="pointer" for="mark-right" style="padding-right: 25px; padding-top: 20px; position: relative; width: 80px;">
                      <small style="height: 20px; margin-bottom: -50px; position: absolute; right: 0; margin-right: -50px; text-align: center; transform: rotate(90deg); top: 50%; width: 80px; -webkit-backface-visibility: hidden;">{t}Advertisement{/t}</small>
                      <div style="background: rgba(0, 0, 0, .25); height: 80px; width: 80px;"></div>
                    </label>
                  </div>
                  <div class="col-xs-6 form-group">
                    <div class="radio">
                      <input id="mark-left" name="orientation" ng-model="params.orientation" type="radio" value="left">
                      <label for="mark-left">
                        {t}Left{/t}
                      </label>
                    </div>
                    <label class="pointer" for="mark-left" style="padding-left: 25px; padding-top: 20px; position: relative;">
                      <small style="height: 20px; margin-left: -50px; position: absolute; text-align: center; transform: rotate(-90deg); top: 50%; width: 80px; -webkit-backface-visibility: hidden;">{t}Advertisement{/t}</small>
                      <div style="background: rgba(0, 0, 0, .25); height: 80px; width: 80px;"></div>
                    </label>
                  </div>
                  <div class="col-xs-6 form-group">
                    <div class="radio">
                      <input id="mark-bottom" name="orientation" ng-model="params.orientation" type="radio" value="bottom">
                      <label for="mark-bottom">
                        {t}Bottom{/t}
                      </label>
                    </div>
                    <label class="pointer" for="mark-bottom" style="padding-bottom: 20px; padding-top: 20px; position: relative;">
                      <small style="bottom: 0;display: block; height: 20px; position: absolute; text-align: center; width: 80px;">{t}Advertisement{/t}</small>
                      <div style="background: rgba(0, 0, 0, .25); height: 80px; width: 80px;"></div>
                    </label>
                  </div>
                </div>
                <div>
                  <small class="help">
                    <i class="fa fa-info-circle m-r-5 text-info"></i>
                    {t}Defines the orientation for the word that marks the advertisement position{/t}
                  </small>
                </div>
              </div>
              <div class="grid-collapse-title pointer" ng-click="expanded.devices = !expanded.devices">
                <i class="fa fa-desktop m-r-5"></i> {t}Devices{/t}
                <i class="animated fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.devices }"></i>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase" ng-if="!expanded.devices && params.devices.phone + params.devices.tablet + params.devices.desktop > 0">
                  [% params.devices.phone + params.devices.tablet + params.devices.desktop %]
                  {t}selected{/t}
                </span>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.devices }">
                <div class="row">
                  <div class="col-sm-4 col-md-12 col-lg-4">
                    <div class="checkbox">
                      <input type="checkbox" name="restriction_devices_desktop" id="restriction_device_desktop" ng-model="params.devices.desktop" ng-false-value="0" ng-true-value="1" value="1">
                      <label class="form-label" for="restriction_device_desktop" uib-tooltip="{t}Width{/t} >= 992px">
                        <i class="fa fa-desktop m-l-5"></i>
                        {t}Desktop{/t}
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-4 col-md-12 col-lg-4">
                    <div class="checkbox">
                      <input type="checkbox" name="restriction_devices_tablet" id="restriction_device_tablet" ng-model="params.devices.tablet" ng-false-value="0" ng-true-value="1" value="1">
                      <label class="form-label" for="restriction_device_tablet" uib-tooltip="{t}Width{/t} < 992px">
                        <i class="fa fa-tablet m-l-5"></i>
                        {t}Tablet{/t}
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-4 col-md-12 col-lg-4">
                    <div class="checkbox">
                      <input type="checkbox" name="restriction_devices_phone" id="restriction_device_phone" ng-model="params.devices.phone" ng-false-value="0" ng-true-value="1" value="1">
                      <label class="form-label" for="restriction_device_phone" uib-tooltip="{t}Width{/t} < 768px"s>
                        <i class="fa fa-phone m-l-5"></i>
                        {t}Phone{/t}
                      </label>
                    </div>
                  </div>
                </div>
                <div class="m-t-10">
                  <small class="help">
                    <i class="fa fa-info-circle m-r-5 text-info"></i>
                    {t}Display the advertisement only on the selected devices{/t}
                  </small>
                </div>
              </div>
              <div class="grid-collapse-title pointer" ng-class="{ 'open': expanded.dimensions }" ng-click="expanded.dimensions = !expanded.dimensions">
                <i class="fa fa-arrows m-r-5"></i> {t}Dimensions{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.dimensions }"></i>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase" ng-class="{ 'badge-danger': countEmpty() >= 3, 'badge-warning text-black': countEmpty() === 1 || countEmpty() === 2 }" ng-if="params.sizes && params.sizes.length > 0" tooltip-enable="countEmpty() > 0" uib-tooltip="{t}One or more dimensions are invalid{/t}""" tooltip-placement="left">
                  [% params.sizes.length %] {t}Dimensions{/t}
                </span>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.dimensions }">
                <input name="sizes" ng-value="json_sizes" type="hidden">
                <div class="no-animate row" ng-repeat="size in params.sizes track by $index">
                  <div class="col-xs-1 p-t-15 text-center">
                    <i class="fa fa-desktop" ng-class="{ 'text-success': !isEmpty($index), 'text-danger ': isEmpty($index) }" ng-if="size.device === 'desktop'" uib-tooltip="{t}Desktop{/t} (>= 992px)"></i>
                    <i class="fa fa-tablet" ng-class="{ 'text-success': !isEmpty($index), 'text-danger ': isEmpty($index) }" ng-if="size.device === 'tablet'" uib-tooltip="{t}Tablet{/t} (< 992px)"></i>
                    <i class="fa fa-mobile" ng-class="{ 'text-success': !isEmpty($index), 'text-danger ': isEmpty($index) }" ng-if="size.device === 'phone'" uib-tooltip="{t}Phone{/t} (< 768px)"></i>
                    <i class="fa fa-external-link" ng-class="{ 'text-success': !isEmpty($index), 'text-danger ': isEmpty($index) }" ng-if="!size.device" uib-tooltip="{t}Google DFP{/t}"></i>
                  </div>
                  <div class="col-xs-9">
                    <div class="row">
                      <div class="col-xs-6 form-group">
                        <div class="controls">
                          <div class="input-group">
                            <span class="input-group-addon" uib-tooltip="{t}Width{/t}">
                              <i class="fa fa-arrows-h"></i>
                            </span>
                            <input class="form-control" min="0" ng-model="size.width" ng-value="size.width" placeholder="{t}Width{/t}" required type="number">
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-6 form-group">
                        <div class="controls">
                          <div class="input-group">
                            <span class="input-group-addon" uib-tooltip="{t}Height{/t}">
                              <i class="fa fa-arrows-v"></i>
                            </span>
                            <input class="form-control pull-left" min="0" ng-model="size.height" ng-value="size.height" placeholder="{t}Height{/t}" required type="number">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-2">
                    <div class="form-group">
                      <div class="controls">
                        <button class="btn btn-danger" ng-click="removeSize($index)" ng-if="$index > 2" type="button">
                          <i class="fa fa-trash-o"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 text-center">
                    <button class="btn btn-block btn-white no-animate" ng-click="addSize();" ng-if="with_script == 3" type="button">
                      <i class="fa fa-plus m-r-5"></i>
                      {t}Add{/t}
                    </button>
                  </div>
                </div>
              </div>
              <div class="grid-collapse-title pointer" ng-class="{ 'open': expanded.dates }" ng-click="expanded.dates = !expanded.dates">
                <i class="fa fa-calendar-check-o m-r-5"></i> {t}Date range{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.dates }"></i>
                <span class="badge badge-default m-r-10 ng-cloak pull-right text-uppercase" ng-if="!expanded.dates && endtime" uib-tooltip="[% endtime %]">
                  <strong>{t}End{/t}</strong>
                  <span class="hidden-lg pull-right visible-xlg">: [% endtime %]</span>
                </span>
                <span class="badge badge-default m-r-10 ng-cloak pull-right text-uppercase" ng-if="!expanded.dates && starttime" uib-tooltip="[% starttime %]">
                  <strong>{t}Start{/t}</strong>
                  <span class="hidden-lg pull-right visible-xlg">: [% starttime %]</span>
                </span>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.dates }">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon add-on">
                      <i class="fa fa-calendar m-r-5"></i>
                      {t}Start{/t}
                    </span>
                    <input class="form-control" type="datetime" id="starttime" name="starttime" value="{if isset($advertisement) && $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" datetime-picker ng-model="starttime" />
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon add-on">
                      <span class="fa fa-calendar m-r-5"></span>
                      {t}End{/t}&nbsp;&nbsp;&nbsp;
                    </span>
                    <input class="form-control" type="datetime" id="endtime" name="endtime" value="{if isset($advertisement) && $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" datetime-picker ng-model="endtime" />
                  </div>
                </div>
                <div class="m-t-10">
                  <small class="help">
                    <i class="fa fa-info-circle m-r-5 text-info"></i>
                    {t}Display the advertisement if current date is in range{/t}
                  </small>
                </div>
              </div>
              {if $safeFrame}
                <div class="grid-collapse-title pointer ng-cloak" ng-click="expanded.user_groups = !expanded.user_groups" ng-hide="!extra.user_groups || extra.user_groups.length === 0">
                  <i class="fa fa-users m-r-5"></i>{t}User groups{/t}
                  <i class="animated fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.user_groups }"></i>
                  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.user_groups">
                    <span ng-show="ui.user_groups.length === 0">{t}All{/t}</span>
                    <span ng-show="ui.user_groups.length != 0">
                      <strong>[% ui.user_groups.length %]</strong>
                      {t}selected{/t}
                    </span>
                  </span>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.user_groups }">
                  <input name="user_groups" ng-value="user_groups" type="hidden">
                  <div class="checkbox p-b-5">
                    <input id="group-all" name="group-all" ng-change="areAllUserGroupsSelected()" ng-model="selected.all.user_groups" type="checkbox">
                    <label class="form-label" for="group-all">
                      {t}Select all{/t}
                    </label>
                  </div>
                  <div class="checkbox-list checkbox-list-user-groups">
                    <div class="checkbox p-b-5" ng-repeat="group in extra.user_groups">
                      <input id="group-[% $index %]" name="group-[% $index %]" checklist-model="ui.user_groups" checklist-value="group.id" type="checkbox">
                      <label class="form-label" for="group-[% $index %]">
                        [% group.name %]
                      </label>
                    </div>
                  </div>
                  <div class="m-t-5">
                    <small class="help">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}Display the advertisement if user belongs to one or more of the selected user groups{/t}
                    </small>
                  </div>
                </div>
              {/if}
              <div class="grid-collapse-title pointer ng-cloak" ng-click="expanded.duration = !expanded.duration" ng-show="isInterstitial()">
                <i class="fa fa-clock-o m-r-5"></i>
                {t}Duration{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.duration }"></i>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold" ng-show="!expanded.duration">
                  <strong>[% timeout %]</strong>s
                </span>
              </div>
              <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.duration }" ng-show="isInterstitial()">
                <div class="input-group">
                  <input type="number" class="form-control" id="timeout" name="timeout" placeholder="0" value="{$advertisement->timeout|default:"4"}" min="0"/>
                  <div class="input-group-addon">{t}seconds{/t}</div>
                </div>
                <div class="m-t-10">
                  <small class="help">
                    <i class="fa fa-info-circle m-r-5 text-info"></i>
                    {t}Display the advertisement for a limited time (intersticials only){/t}
                  </small>
                </div>
              </div>
              <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.category = !expanded.category">
                <input name="categories" ng-value="categories" type="hidden">
                <i class="fa fa-bookmark m-r-5"></i>
                {t}Categories{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.category">
                  <span ng-show="ui.categories.length === 0 || ui.categories_all == true">{t}All{/t}</span>
                  <span ng-show="ui.categories.length != 0 && ui.categories_all == false">
                    <strong>[% ui.categories.length %]</strong> {t}selected{/t}
                  </span>
                </span>
              </div>
              <div class="grid-collapse-body" ng-class="{ 'expanded': expanded.category, 'no-animate': ui.categories_all }">
                <div class="checkbox">
                  <input id="category-all" name="category-all" ng-model="ui.categories_all" ng-true-value="true" ng-false-value="false" type="checkbox">
                  <label class="form-label" for="category-all">
                    {t}Show in all categories{/t}
                  </label>
                </div>
                <div class="m-t-10" ng-show="!ui.categories_all">
                  <div class="m-b-10">
                    <small class="help">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}Display the advertisement only in the selected categories{/t}
                    </small>
                  </div>
                  <div class="checkbox p-b-5">
                    <input id="toggle-categories" name="toggle-categories" ng-change="areAllCategoriesSelected()" ng-model="selected.all.categories" type="checkbox">
                    <label class="form-label" for="toggle-categories">
                      {t}Select/deselect all{/t}
                    </label>
                  </div>
                  <div class="checkbox-list checkbox-list-user-groups">
                    <div class="checkbox p-b-5" ng-repeat="category in (filteredCategories = (extra.categories | filter : { parent: 0 }))">
                      <div class="m-t-15" ng-if="$index > 0 && category.type != filteredCategories[$index - 1].type">
                        <h5 ng-if="category.type == 1"><i class="fa fa-sticky-note m-r-5"></i>{t}Contents{/t}</h5>
                        <h5 ng-if="category.type == 7"><i class="fa fa-camera m-r-5"></i>{t}Albums{/t}</h5>
                        <h5 ng-if="category.type == 9"><i class="fa fa-play-circle-o m-r-5"></i>{t}Videos{/t}</h5>
                        <h5 ng-if="category.type == 11"><i class="fa fa-pie-chart m-r-5"></i>{t}Polls{/t}</h5>
                      </div>
                      <div ng-if="category.parent == 0">
                        <input id="category-[% category.id %]" name="category-[% category.id %]" checklist-model="ui.categories" checklist-value="category.id" type="checkbox">
                        <label class="form-label" for="category-[% category.id %]">
                          [% category.name %]
                        </label>
                      </div>
                      <div ng-if="category.id != 0">
                        <div ng-repeat="subcategory in extra.categories | filter : { parent: category.id }">
                          <input id="category-[% subcategory.id %]" name="category-[% subcategory.id %]" checklist-model="ui.categories" checklist-value="subcategory.id" type="checkbox">
                          <label class="form-label" for="category-[% subcategory.id %]">
                            &rarr; [% subcategory.name %]
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="m-t-5" ng-if="selected.all.categories">
                    <small class="help">
                      <i class="fa fa-exclamation-triangle m-r-5 text-warning"></i>
                      {t}We recommend you to use the "Show in all categories" mark to avoid unchecked future created categories{/t}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="grid simple advertisement-positions">
        {* spinner to show when the page is loading *}
        <div class="grid-body" ng-if="ui.loading">
          <div class="spinner-wrapper">
            <div class="loading-spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
          </div>
        </div>

        {* contents only shown when page is already loaded *}
        <div class="grid-title shaded ng-cloak positions-selected">
          <div class="ng-cloak m-b-5 positions-selected-counter">
            <span ng-if="positions.length == 0">{t}No positions selected, mark those you want on the form below.{/t}</span>
            <span ng-if="positions.length > 0">{t 1="[% positions.length %]"}%1 positions{/t}</span>
          </div>
          <div class="ng-cloak positions-selected-list collapsed" ng-class="{ collapsed : !expanded.positions }">
            <div ng-repeat="position in positions| orderBy:'position'" class="position badge p-l-15 p-r-15 m-b-5 m-r-5" >[% extra['ads_positions'][position] %]</div>
            <div class="position-selected-hidden-counter small-text btn btn-link" ng-click="expanded.positions = !expanded.positions">
              <span ng-if="!expanded.positions && ui.hidden_elements > 0"> {t 1="[% ui.hidden_elements %]"}And %1 more…{/t}</span>
              <span ng-if="expanded.positions"><span class="fa fa-chevron-up"></span> {t}Show less…{/t}</span>
            </div>
          </div>
        </div>
        <div class="grid-body no-padding ng-cloak">
          <scrollable-tabset show-drop-down="false" show-tooltips="false" scroll-by="200">
            <uib-tabset>
              <uib-tab>
                <uib-tab-heading>
                  {t}Frontpages{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(0, 99) > 0">[% countPositionsSelectedbyRange(0, 99) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_frontpage.tpl"}
                  </div>
                </div>
              </uib-tab>

              <uib-tab>
                <uib-tab-heading>
                  {t}Article: inner{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(100, 199) > 0">[% countPositionsSelectedbyRange(100, 199) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_article_inner.tpl"}
                  </div>
                </div>
              </uib-tab>

              {is_module_activated name="VIDEO_MANAGER"}
              <uib-tab>
                <uib-tab-heading>
                  {t}Video: frontpages{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(200, 299) > 0">[% countPositionsSelectedbyRange(200, 299) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_video_frontpage.tpl"}
                  </div>
                </div>
              </uib-tab>
              <uib-tab>
                <uib-tab-heading>
                  {t}Video: inner{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(300, 399) > 0">[% countPositionsSelectedbyRange(300, 399) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_video_inner.tpl"}
                  </div>
                </div>
              </uib-tab>
              {/is_module_activated}

              {is_module_activated name="ALBUM_MANAGER"}
              <uib-tab>
                <uib-tab-heading>
                  {t}Album: frontpages{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(400, 499) > 0">[% countPositionsSelectedbyRange(400, 499) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_album_frontpage.tpl"}
                  </div>
                </div>
              </uib-tab>

              <uib-tab>
                <uib-tab-heading>
                  {t}Album: inner{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(500, 599) > 0">[% countPositionsSelectedbyRange(500, 599) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_album_inner.tpl"}
                  </div>
                </div>
              </uib-tab>
              {/is_module_activated}

              {is_module_activated name="OPINION_MANAGER"}
              <uib-tab>
                <uib-tab-heading>
                  {t}Opinion: frontpage{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(600, 699) > 0">[% countPositionsSelectedbyRange(600, 699) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_opinion_frontpage.tpl"}
                  </div>
                </div>
              </uib-tab>

              <uib-tab>
                <uib-tab-heading>
                  {t}Opinion: inner{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(700, 799) > 0">[% countPositionsSelectedbyRange(700, 799) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_opinion_inner.tpl"}
                  </div>
                </div>
              </uib-tab>
              {/is_module_activated}

              {is_module_activated name="POLL_MANAGER"}
              <uib-tab>
                <uib-tab-heading>
                  {t}Poll: frontpage{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(800, 899) > 0">[% countPositionsSelectedbyRange(800, 899) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_poll_frontpage.tpl"}
                  </div>
                </div>
              </uib-tab>

              <uib-tab>
                <uib-tab-heading>
                  {t}Poll: inner{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(900, 999) > 0">[% countPositionsSelectedbyRange(900, 999) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_poll_inner.tpl"}
                  </div>
                </div>
              </uib-tab>
              {/is_module_activated}

              {is_module_activated name="NEWSLETTER_MANAGER"}
              <uib-tab>
                <uib-tab-heading>
                  {t}Newsletter{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(1000, 1049) > 0">[% countPositionsSelectedbyRange(1000, 1049) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_newsletter.tpl"}
                  </div>
                </div>
              </uib-tab>
              {/is_module_activated}

              {is_module_activated name="AMP_MODULE"}
              <uib-tab>
                <uib-tab-heading>
                  {t}Google AMP{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(1050, 1074) > 0">[% countPositionsSelectedbyRange(1050, 1074) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_amp.tpl"}
                  </div>
                </div>
              </uib-tab>
              {/is_module_activated}

              {is_module_activated name="FIA_MODULE"}
              <uib-tab>
                <uib-tab-heading>
                  {t}Facebook Instant Articles{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(1075, 1099) > 0">[% countPositionsSelectedbyRange(1075, 1099) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  {include file="advertisement/partials/advertisement_positions_fia.tpl"}
                </div>
              </uib-tab>
              {/is_module_activated}

              <uib-tab>
                <uib-tab-heading>
                  {t}Others{/t} <span class="badge" ng-show="countPositionsSelectedbyRange(1100, null) > 0">[% countPositionsSelectedbyRange(1100, null) %]</span>
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    {include file="advertisement/partials/advertisement_positions_other.tpl"}
                  </div>
                </div>
              </uib-tab>
            </uib-tabset>
          </scrollable-tabset>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-dfp-detected">
      {include file="advertisement/modal/dfp_detected.tpl"}
    </script>
  </form>
  <script type="text/ng-template" id="modal-adblock">
    {include file="base/modal/modal.adblock.tpl"}
  </script>
{/block}
