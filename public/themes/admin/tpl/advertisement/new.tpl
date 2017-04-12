{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('#formulario').on('change', '#title', function(e, ui) {
          fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
        });

        $('#starttime, #endtime').datetimepicker({
          format: 'YYYY-MM-DD HH:mm:ss',
          useCurrent: false
        });

        $("#starttime").on("dp.change",function (e) {
          $('#endtime').data("DateTimePicker").minDate(e.date);
        });
        $("#endtime").on("dp.change",function (e) {
          $('#starttime').data("DateTimePicker").maxDate(e.date);
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
<form action="{if $advertisement->id}{url name=admin_ad_update id=$advertisement->id}{else}{url name=admin_ad_create}{/if}" method="post" id="formulario" name="AdvertisementForm" ng-controller="AdvertisementCtrl" ng-init="init({json_encode($advertisement->params)|clear_json}, {json_encode($advertisement->fk_content_categories)|clear_json}); type_advertisement = '{$advertisement->type_advertisement}'; extra = { categories: {json_encode($categories)|clear_json}, user_groups: {json_encode($user_groups)|clear_json} }; with_script = {if empty($advertisement->with_script)}0{else}{{$advertisement->with_script}}{/if}">
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
                      <input id="dfp" name="with_script" ng-model="with_script"   {if $with_script == 3}checked{/if} type="radio" value="3">
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
                  <div class="col-md-4">
                    <div class="checkbox">
                      <input type="checkbox" name="restriction_devices_desktop" id="restriction_device_desktop" ng-model="params.devices.desktop" ng-false-value="0" ng-true-value="1" value="1">
                      <label class="form-label" for="restriction_device_desktop" uib-tooltip="{t}Width{/t} >= 992px">
                        <i class="fa fa-desktop m-l-5"></i>
                        {t}Desktop{/t}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="checkbox">
                      <input type="checkbox" name="restriction_devices_tablet" id="restriction_device_tablet" ng-model="params.devices.tablet" ng-false-value="0" ng-true-value="1" value="1">
                      <label class="form-label" for="restriction_device_tablet" uib-tooltip="{t}Width{/t} < 992px">
                        <i class="fa fa-tablet m-l-5"></i>
                        {t}Tablet{/t}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-4">
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
                    <button class="btn btn-block btn-white no-animate" ng-click="addSize();" ng-if="params.sizes.length >= 3 && with_script == 3" type="button">
                      <i class="fa fa-plus m-r-5"></i>
                      {t}Add{/t}
                    </button>
                  </div>
                </div>
              </div>
              <div class="grid-collapse-title pointer" ng-class="{ 'open': expanded.mark }" ng-click="expanded.mark = !expanded.mark">
                <i class="fa fa-tags m-r-5"></i> {t}Mark{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.mark }"></i>
                <span class="badge badge-default m-r-10 ng-cloak pull-right text-uppercase" ng-if="!expanded.mark">
                  <span ng-if="params.orientation === 'top'">{t}Top{/t}</span>
                  <span ng-if="params.orientation === 'right'">{t}Left{/t}</span>
                  <span ng-if="params.orientation === 'bottom'">{t}Left{/t}</span>
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
              <div class="grid-collapse-title pointer" ng-click="expanded.category = !expanded.category">
                <input name="categories" ng-value="categories" type="hidden">
                <i class="fa fa-bookmark m-r-5"></i>
                {t}Categories{/t}
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
                <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.category">
                  <span ng-show="ui.categories.length === 0">{t}All{/t}</span>
                  <span ng-show="ui.categories.length != 0">
                    <strong>[% ui.categories.length %]</strong>
                    {t}selected{/t}
                  </span>
                </span>
              </div>
              <div class="grid-collapse-body" ng-class="{ 'expanded': expanded.category }">
                <div class="checkbox p-b-5">
                  <input id="category-all" name="category-all" ng-change="areAllCategoriesSelected()" ng-model="selected.all.categories" type="checkbox">
                  <label class="form-label" for="category-all">
                    {t}Select all{/t}
                  </label>
                </div>
                <div class="checkbox-list checkbox-list-user-groups">
                  <div class="checkbox p-b-5" ng-repeat="category in extra.categories">
                    <input id="category-[% $index %]" name="category-[% $index %]" checklist-model="ui.categories" checklist-value="category.id" type="checkbox">
                    <label class="form-label" for="category-[% $index %]">
                      [% category.name %]
                    </label>
                  </div>
                </div>
                <div class="m-t-5">
                  <small class="help">
                    <i class="fa fa-info-circle m-r-5 text-info"></i>
                    {t}Display the advertisement only in the selected categories{/t}
                  </small>
                </div>
              </div>
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
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Where to show this ad{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="form-group">
                <label class="form-label">{t}Pages of type{/t}</label>
                <div class="controls">
                  <select name="position" id="position" ng-model="position">
                    <option value="publi-frontpage" {if $advertisement->type_advertisement < 100}selected{/if}>{t}Frontpage{/t}</option>
                    <option value="publi-inner" {if $advertisement->type_advertisement > 100 && $advertisement->type_advertisement < 200}selected{/if}>{t}Inner article{/t}</option>
                    {is_module_activated name="VIDEO_MANAGER"}
                    <option value="publi-video" {if $advertisement->type_advertisement > 200 && $advertisement->type_advertisement < 300}selected{/if}>{t}Video frontpage{/t}</option>
                    <option value="publi-video-inner" {if $advertisement->type_advertisement > 300 && $advertisement->type_advertisement < 400}selected{/if}>{t}Inner video{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="OPINION_MANAGER"}
                    <option value="publi-opinion" {if $advertisement->type_advertisement > 600 && $advertisement->type_advertisement < 700}selected{/if}>{t}Opinion frontpage{/t}</option>
                    <option value="publi-opinion-inner" {if $advertisement->type_advertisement > 700 && $advertisement->type_advertisement < 800}selected{/if}>{t}Inner opinion{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="ALBUM_MANAGER"}
                    <option value="publi-gallery" {if $advertisement->type_advertisement > 400 && $advertisement->type_advertisement < 500}selected{/if}>{t}Galleries{/t}</option>
                    <option value="publi-gallery-inner" {if $advertisement->type_advertisement > 500 && $advertisement->type_advertisement < 600}selected{/if}>{t}Gallery Inner{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="POLL_MANAGER"}
                    <option value="publi-poll" {if $advertisement->type_advertisement > 800 && $advertisement->type_advertisement < 900}selected{/if}>{t}Poll{/t}</option>
                    <option value="publi-poll-inner" {if $advertisement->type_advertisement > 900 && $advertisement->type_advertisement < 1000}selected{/if}>{t}Poll Inner{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="NEWSLETTER_MANAGER"}
                    <option value="publi-newsletter" {if $advertisement->type_advertisement > 1000 && $advertisement->type_advertisement < 1050}selected{/if}>{t}Newsletter{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="AMP_MODULE"}
                    <option value="publi-amp" {if $advertisement->type_advertisement >= 1050 && $advertisement->type_advertisement < 1075}selected{/if}>{t}AMP pages{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="FIA_MODULE"}
                    <option value="publi-fia" {if $advertisement->type_advertisement >= 1075 && $advertisement->type_advertisement < 1100}selected{/if}>{t}Instant Articles pages{/t}</option>
                    {/is_module_activated}
                    <option value="publi-others" {if $advertisement->type_advertisement > 1100}selected{/if}>{t}Others{/t}</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="controls">
                  <div id="position-adv">
                    <div class="ng-cloak" ng-show="position == 'publi-frontpage'">
                      {include file="advertisement/partials/advertisement_positions.tpl"}
                    </div>
                    <div class="ng-cloak" ng-show="position == 'publi-inner'">
                      {include file="advertisement/partials/advertisement_positions_inner.tpl"}
                    </div>
                    {is_module_activated name="VIDEO_MANAGER"}
                    <div class="ng-cloak" ng-show="position == 'publi-video'">
                      {include file="advertisement/partials/advertisement_positions_video.tpl"}
                    </div>
                    <div class="ng-cloak" ng-show="position == 'publi-video-inner'">
                      {include file="advertisement/partials/advertisement_positions_video_inner.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="OPINION_MANAGER"}
                    <div class="ng-cloak" ng-show="position == 'publi-opinion'">
                      {include file="advertisement/partials/advertisement_positions_opinion.tpl"}
                    </div>
                    <div class="ng-cloak" ng-show="position == 'publi-opinion-inner'">
                      {include file="advertisement/partials/advertisement_positions_opinion_inner.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="ALBUM_MANAGER"}
                    <div class="ng-cloak" ng-show="position == 'publi-gallery'">
                      {include file="advertisement/partials/advertisement_positions_gallery.tpl"}
                    </div>
                    <div class="ng-cloak" ng-show="position == 'publi-gallery-inner'">
                      {include file="advertisement/partials/advertisement_positions_gallery_inner.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="POLL_MANAGER"}
                    <div class="ng-cloak" ng-show="position == 'publi-poll'">
                      {include file="advertisement/partials/advertisement_positions_poll.tpl"}
                    </div>
                    <div class="ng-cloak" ng-show="position == 'publi-poll-inner'">
                      {include file="advertisement/partials/advertisement_positions_poll_inner.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="NEWSLETTER_MANAGER"}
                    <div class="ng-cloak" ng-show="position == 'publi-newsletter'">
                      {include file="advertisement/partials/advertisement_positions_newsletter.tpl"}
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="AMP_MODULE"}
                    <div class="ng-cloak" ng-show="position == 'publi-amp'">
                      <div class="col-md-9">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="radio">
                              <input id="amp-inner-button1" name="type_advertisement" type="radio" value="1051" {if isset($advertisement) && $advertisement->type_advertisement == 1051}checked="checked" {/if}/>
                              <label for="amp-inner-button1">
                                {t}AMP inner article - Button 1{/t}
                              </label>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="radio">
                              <input id="amp-inner-button2" name="type_advertisement" type="radio" value="1052" {if isset($advertisement) && $advertisement->type_advertisement == 1052}checked="checked" {/if}/>
                              <label for="amp-inner-button2">
                                {t}AMP inner article - Button 2{/t}
                              </label>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="radio">
                              <input id="amp-inner-button3" name="type_advertisement" type="radio" value="1053" {if isset($advertisement) && $advertisement->type_advertisement == 1053}checked="checked" {/if}/>
                              <label for="amp-inner-button3">
                                {t}AMP inner article - Button 3{/t}
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="FIA_MODULE"}
                    <div class="ng-cloak" ng-show="position == 'publi-fia'">
                      <div class="col-md-9">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="radio">
                              <input id="fia-inner-button1" name="type_advertisement" type="radio" value="1075" {if isset($advertisement) && $advertisement->type_advertisement == 1075}checked="checked" {/if}/>
                              <label for="fia-inner-button1">
                                {t}Instant Articles inner article - Button 1{/t}
                              </label>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="radio">
                              <input id="fia-inner-button2" name="type_advertisement" type="radio" value="1076" {if isset($advertisement) && $advertisement->type_advertisement == 1076}checked="checked" {/if}/>
                              <label for="fia-inner-button2">
                                {t}Instant Articles inner article - Button 2{/t}
                              </label>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="radio">
                              <input id="fia-inner-button3" name="type_advertisement" type="radio" value="1077" {if isset($advertisement) && $advertisement->type_advertisement == 1077}checked="checked" {/if}/>
                              <label for="fia-inner-button3">
                                {t}Instant Articles inner article - Button 3{/t}
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    {/is_module_activated}
                    <div class="ng-cloak" ng-show="position == 'publi-others'">
                      {foreach $themeAds as $adId => $ad}
                      <div class="row">
                        <div class="col-md-12">
                          <div class="radio">
                            <input id="ad-{$adId}" type="radio" name="type_advertisement" value="{$adId}" {if isset($advertisement) && $advertisement->type_advertisement == $adId}checked="checked" {/if}/>
                            <label for="ad-{$adId}">
                              {$ad['name']}
                            </label>
                          </div>
                        </div>
                      </div>
                      <hr>
                      {/foreach}
                    </div>
                  </div><!-- /position-adv -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-dfp-detected">
      {include file="advertisement/modal/dfp_detected.tpl"}
    </script>
  </form>
  <script type="text/ng-template" id="modal-adblock">
    {include file="base/modals/modalAdblock.tpl"}
  </script>
{/block}
