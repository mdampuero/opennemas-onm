{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script type="text/javascript">
      var advertisement_urls = {
        calculate_tags : '{url name=admin_utils_calculate_tags}'
      }

      jQuery(document).ready(function($) {
        $('#formulario').on('change', '#title', function(e, ui) {
          fill_tags(jQuery('#title').val(),'#metadata', advertisement_urls.calculate_tags);
        });

        $('#starttime, #endtime').datetimepicker({
          format: 'YYYY-MM-DD HH:mm:ss',
          useCurrent: false,
          minDate: '{$advertisement->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}'
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
  <form action="{if $advertisement->id}{url name=admin_ad_update id=$advertisement->id}{else}{url name=admin_ad_create}{/if}" method="post" id="formulario" ng-controller="AdvertisementCtrl">
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
                <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="save-button">
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
                  <input class="form-control" id="title" name="title" required="required" type="text" value="{$advertisement->title|clearslash|escape:"html"|default:""}"/>
                </div>
              </div>
              <div class="hidden">
                <label for="metadata" class="form-label">
                  {t}Keywords{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="metadata" name="metadata" required="required"
                  title="Metadatos" type="hidden" value="{$advertisement->metadata|strip|default:""}">
                </div>
              </div>
              <h5>{t}Contents{/t}</h5>
              <div class="form-group">
                <label class="form-label" for="with_script">
                  {t}Type{/t}
                </label>
                <div class="controls">
                  <select name="with_script" id="with_script" ng-model='with_script'>
                    <option value="0" {if !isset($advertisement) || $advertisement->with_script == 0}selected="selected"{/if}>{t}Image or Flash object{/t}</option>
                    <option value="1"  {if isset($advertisement) && $advertisement->with_script == 1}selected="selected"{/if}>{t}HTML or Javascript code{/t}</option>
                    {if !empty($server_url)}
                      <option value="2" {if isset($advertisement) && $advertisement->with_script == 2}selected="selected"{/if}>{t}Open X zone{/t}</option>
                    {/if}
                    <option value="3" {if isset($advertisement) && $advertisement->with_script == 3}selected="selected"{/if}>{t}Google DFP unit{/t}</option>
                  </select>
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
                  <label for="openx_zone">{t}Open X zone id{/t}</label>
                  <input type="text" name="openx_zone_id" value="{$advertisement->params['openx_zone_id']}">
                  <div class="help-block">{t 1=$server_url}OpenX/Revive Ad server uses an id to identify an advertisement. Please fill the zone id from your OpenX/Revive server %1{/t}</div>
                </div>
                <div  ng-show="with_script == 3">
                  <label for="googledfp_zone_id">
                    {t}Google DFP unit id{/t}
                  </label>
                  <input class="form-control" type="text" name="googledfp_unit_id" ng-model="googledfp_unit_id" value="{$advertisement->params['googledfp_unit_id']}">
                  <div class="help-block">{t 1=$server_url}Google DFP uses an unit ID to identify an advertisement. Please fill the zone id from your Google DFP panel{/t}</div>
                </div>
              </div>
              <div ng-init="init({json_encode($advertisement->params)|clear_json})" id="ad_dimensions">
                <input name="params_width" ng-value="params_width" type="hidden">
                <input name="params_height" ng-value="params_height" type="hidden">
                <div class="row ng-cloak" ng-show="with_script != 2 && sizes.length >= 1" ng-repeat="size in sizes track by $index">
                  <div class="col-xs-4">
                    <div class="form-group">
                      <label class="form-label">
                        {t}Width{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" ng-model="size.width" type="number" ng-value="[% size.width %]" ng-required="with_script != 2" min="0">
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-4">
                    <div class="form-group">
                      <label class="form-label">
                        {t}Height{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control pull-left" ng-model="size.height" type="number" ng-value="[% size.height %]" ng-required="with_script != 2" min="0">
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-2">
                    <div class="form-group">
                      <label class="form-label">&nbsp;</label>
                      <div class="controls">
                        <button class="btn btn-success pull-left" ng-click="addSize();" ng-if="$index === 0" type="button">
                          <i class="fa fa-plus"></i>
                        </button>
                        <button class="btn btn-danger" ng-click="removeSize($index)" ng-if="$index !== 0" type="button">
                          <i class="fa fa-trash-o"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group ng-cloak" ng-show="with_script == 3">
                  <div class="input-group">
                    <div class="input-group-btn">
                      <button class="btn btn-default" ng-click="addSize();" type="button">{t}Add another size{/t}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <div class="checkbox">
                  <input type="checkbox" name="content_status" id="content_status" value="1"
                  {if isset($advertisement->content_status) && $advertisement->content_status == 1}checked="checked"{/if} {acl isNotAllowed="ADVERTISEMENT_AVAILABLE"}disabled="disabled"{/acl} />
                  <label class="form-label" for="content_status">
                    {t}Activated{/t}
                  </label>
                </div>
              </div>
              <h5>{t}When to show this ad{/t}</h5>
              <div class="form-group">
                <label for="type_medida" class="form-label">
                  {t}Restrictions{/t}
                  <span data-container="body" tooltip-placement="top" uib-tooltip="{t}Show this ad if satisfy one condition{/t}."><i class="fa fa-info-circle text-info""></i></span>
                </label>
                <div class="controls">
                  <select name="type_medida" id="type_medida" ng-model="type_medida">
                    <option value="NULL" {if !isset($advertisement) || is_null($advertisement->type_medida)}selected="selected"{/if}>{t}Without limits{/t}</option>
                    <option value="DATE" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'DATE'}selected="selected"{/if}>{t}Date range{/t}</option>
                  </select>
                </div>
              </div>
              <div class="form-group ng-cloak" id="porfecha" ng-show="type_medida == 'DATE'">
                <label for="starttime" class="form-label">{t}Date range{/t}</label>
                <div class="controls">
                  <label for="starttime">{t}From{/t}</label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" type="datetime" id="starttime" name="starttime" value="{if isset($advertisement) && $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                  </div>
                  <label for="endtime">{t}Until{/t}</label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" type="datetime" id="endtime" name="endtime" value="{if isset($advertisement) && $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                  </div>
                  <div class="help-block">{t}Show this ad within a range of dates.{/t}.</div>
                </div>
              </div>
              <div class="form-group" id="div_url1" ng-show="with_script == 0">
                <label for="url" class="form-label">{t}Url{/t}</label>
                <div class="controls">
                  <input type="url" id="url" name="url" class="form-control" value="{$advertisement->url}" placeholder="http://" ng-required="with_script == 0" />
                </div>
              </div>
              <div class="form-group" ng-show="((type_advertisement + 50)  % 100) == 0">
                <label for="timeout" class="form-label">{t}Display banner while{/t}</label>
                <span data-container="body" tooltip-placement="top" uib-tooltip="{t}Amount of seconds that this banner will block all the page.{/t}"><i class="fa fa-info-circle text-info""></i></span>
                <div class="controls">
                 <div class="input-group">
                    <input type="number" class="form-control" id="timeout" name="timeout" placeholder="0" value="{$advertisement->timeout|default:"4"}" min="0" max="100" />
                    <div class="input-group-addon">{t}seconds{/t}</div>
                  </div>
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
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="form-label" for="position">
                      {t}Pages of type{/t}
                    </label>
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
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="category" class="form-label">{t}In categories{/t}</label>
                    <div class="controls">
                      <select name="category[]" id="category" required="required" multiple="multiple" size=6>
                        <option value="0" {if isset($advertisement) && in_array(0,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Frontpage{/t}</option>
                        <option value="4" {if isset($advertisement) && in_array(4,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Opinion{/t}</option>
                        <option value="3" {if isset($advertisement) && in_array(3,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Album{/t}</option>
                        <option value="6" {if isset($advertisement) && in_array(6,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Video{/t}</option>

                        <option value="0">{t}Home{/t}</option>
                        {section name=as loop=$allcategorys}
                        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                        <option value="{$allcategorys[as]->pk_content_category}"
                          {if isset($advertisement) && in_array($allcategorys[as]->pk_content_category,$advertisement->fk_content_categories)}selected="selected"{/if}>
                          {$allcategorys[as]->title}
                        </option>
                        {/acl}
                          {section name=su loop=$subcat[as]}
                          {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                          <option value="{$subcat[as][su]->pk_content_category}"
                            {if isset($advertisement) && in_array($subcat[as][su]->pk_content_category,$advertisement->fk_content_categories)}selected="selected"{/if}>
                            &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                          </option>
                          {/acl}
                          {/section}
                        {/section}
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group" style="position:relative; top:-20px">
                <label class="form-label" for="position">
                  {t}and inside the position{/t}
                </label>
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
                            <div class="radio">
                              <input id="amp-inner-button2" name="type_advertisement" type="radio" value="1052" {if isset($advertisement) && $advertisement->type_advertisement == 1052}checked="checked" {/if}/>
                              <label for="amp-inner-button2">
                                {t}AMP inner article - Button 2{/t}
                              </label>
                            </div>
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
