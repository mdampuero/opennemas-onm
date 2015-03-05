{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="@Common/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" filters="cssrewrite"}
    <link rel="stylesheet" href="{$asset_url}" media="screen">
  {/stylesheets}
{/block}

{block name="footer-js" append}
    {javascripts src="@Common/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}

    <script type="text/javascript">
    var advertisement_urls = {
        calculate_tags : '{url name=admin_utils_calculate_tags}'
    }
    </script>
    <script>

    jQuery(document).ready(function($) {
        $('#formulario').on('change', '#title', function(e, ui) {
            fill_tags(jQuery('#title').val(),'#metadata', advertisement_urls.calculate_tags);
        });

        $('#starttime, #endtime').datetimepicker({
          format: 'YYYY-MM-D HH:mm:ss'
        });

        $("#starttime").on("dp.change",function (e) {
            $('#endtime').data("DateTimePicker").minDate(e.date);
        });
        $("#endtime").on("dp.change",function (e) {
            $('#starttime').data("DateTimePicker").maxDate(e.date);
        });
    });
    </script>
{/block}

{block name="content"}
  <form action="{if $advertisement->id}{url name=admin_ad_update id=$advertisement->id category=$category page=$page filter=$filter}{else}{url name=admin_ad_create filter=$filter}{/if}" method="post" id="formulario" ng-controller="InnerCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-bullhorn"></i>
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
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-save"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      {render_messages}
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
              <div class="form-group">
                <div class="ng-cloak" ng-show="!with_script || with_script == 0">
                  {include file="advertisement/partials/advertisement_images.tpl"}
                </div>
                <div class="ng-cloak" ng-show="with_script == 1">
                  <textarea name="script" id="script" class="form-control" rows="10" >{$advertisement->script|escape:'htmlall'|default:'&lt;script type="text/javascript"&gt;/* JS code */&lt;/script&gt;'}</textarea>
                </div>
                <div class="ng-cloak" ng-show="'{$server_url}' && with_script == 2">
                  <label for="openx_zone">{t}Open X zone id{/t}</label>
                  <input type="text" name="openx_zone_id" value="{$advertisement->params['openx_zone_id']}">
                  <div class="help-block">{t 1=$server_url}OpenX/Revive Ad server uses an id to identify an advertisement. Please fill the zone id from your OpenX/Revive server %1{/t}</div>
                </div>
                <div class="ng-cloak" ng-show="with_script == 3">
                  <label for="googledfp_zone_id">
                    {t}Google DFP unit id{/t}
                  </label>
                  <input type="text" name="googledfp_unit_id" value="{$advertisement->params['googledfp_unit_id']}">
                  <div class="help-block">{t 1=$server_url}Google DFP uses an unit ID to identify an advertisement. Please fill the zone id from your Google DFP panel{/t}</div>
                </div>
              </div>
              <div class="row" id="ad_dimensions" ng-if="with_script != 2">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="form-label" for="params_width">
                      {t}Width{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="params_width" name="params_width" type="number" value="{$advertisement->params['width']}" {if isset($advertisement) && $advertisement->with_script != 2}required="required"{/if} min="0">
                    </div>
                  </div>
                </div>
                <div class="col-sm-3 col-sm-offset-1">
                  <div class="form-group">
                    <label for="params_height" class="form-label">{t}Height{/t}</label>
                    <div class="controls">
                      <input class="form-control" id="params_height" name="params_height" type="number" value="{$advertisement->params['height']}" {if isset($advertisement) && $advertisement->with_script != 2}required="required"{/if} min="0">
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
                <label for="type_medida" class="form-label">{t}Restrictions{/t}</label>
                <div class="controls">
                  <select name="type_medida" id="type_medida" ng-model="type_medida">
                    <option value="NULL" {if !isset($advertisement) || is_null($advertisement->type_medida)}selected="selected"{/if}>{t}Without limits{/t}</option>
                    <option value="DATE" {if isset($advertisement) && isset($advertisement->type_medida) && $advertisement->type_medida == 'DATE'}selected="selected"{/if}>{t}Date range{/t}</option>
                  </select>
                  <div class="help-block">{t}Show this ad if satisfy one condition{/t}.</div>
                </div>
              </div>
              <div class="form-group ng-cloak" id="porfecha" ng-show="type_medida == 'DATE'">
                <label for="starttime" class="form-label">{t}Date range{/t}</label>
                <div class="controls">
                  <label for="starttime">{t}From{/t}</label>
                  <input type="datetime" id="starttime"  name="starttime" title="Fecha inicio publicacion"
                  value="{if isset($advertisement) && $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />
                  <label for="endtime">{t}Until{/t}</label>
                  <input type="datetime" id="endtime" name="endtime" title="Fecha fin publicacion"
                  value="{if isset($advertisement) && $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />
                  <div class="help-block">{t}Show this ad within a range of dates.{/t}.</div>
                </div>
              </div>
              <div class="form-group" style="{if !isset($advertisement) || (($advertisement->type_advertisement + 50) % 100) != 0}display:none{/if};">
                <label for="timeout" class="form-label">{t}Display banner while{/t}</label>
                <div class="controls">
                  <input type="number" id="timeout" name="timeout" value="{$advertisement->timeout|default:"4"}" min="0" max="100"/>
                  <div class="help-block">{t}Amount of seconds that this banner will block all the page.{/t}</div>
                </div>
              </div>
              <div class="form-group" id="div_url1" style="display:{if !isset($advertisement) || $advertisement->with_script==0}block{else}none{/if};">
                <label for="url" class="form-label">{t}Url{/t}</label>
                <div class="controls">
                  <input type="url" id="url" name="url" class="form-control" value="{$advertisement->url}" placeholder="http://" {if !empty($advertisement)  && ($advertisement->with_script == 0)} required="required"{/if} />
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
                <label for="category" class="form-label">{t}Category{/t}</label>
                <div class="controls">
                  <select class="select2" name="category[]" id="category" required="required" multiple>
                    {if isset($advertisement->id)}
                    <option value="0" {if isset($advertisement) && in_array(0,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Frontpage{/t}</option>
                    <option value="4" {if isset($advertisement) && in_array(4,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Opinion{/t}</option>
                    <option value="3" {if isset($advertisement) && in_array(3,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Album{/t}</option>
                    <option value="6" {if isset($advertisement) && in_array(6,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Video{/t}</option>

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
                    {else}
                    <option value="0" {if $category == 0}selected="selected"{/if}>{t}Frontpage{/t}</option>
                    {is_module_activated name="OPINION_MANAGER"}
                    <option value="4" {if $category == 4}selected="selected"{/if}>{t}Opinion{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="ALBUM_MANAGER"}
                    <option value="3" {if $category == 3}selected="selected"{/if}>{t}Album{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="VIDEO_MANAGER"}
                    <option value="6" {if $category == 6}selected="selected"{/if}>{t}Video{/t}</option>
                    {/is_module_activated}

                    {section name=as loop=$allcategorys}
                    {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                    <option value="{$allcategorys[as]->pk_content_category}"
                      {if $category eq $allcategorys[as]->pk_content_category}selected="selected"{/if}>
                      {$allcategorys[as]->title}
                    </option>
                    {/acl}
                    {section name=su loop=$subcat[as]}
                    {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                    <option value="{$subcat[as][su]->pk_content_category}"
                      {if $category eq $subcat[as][su]->pk_content_category}selected="selected"{/if}>
                      &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                    </option>
                    {/acl}
                    {/section}
                    {/section}
                    {/if}
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="position">
                  {t}Page type{/t}
                </label>
                <div class="controls">
                  <select name="position" id="position" ng-model="position">
                    <option value="publi-frontpage">{t}Frontpage{/t}</option>
                    <option value="publi-inner">{t}Inner article{/t}</option>
                    {is_module_activated name="VIDEO_MANAGER"}
                      <option value="publi-video">{t}Video frontpage{/t}</option>
                      <option value="publi-video-inner">{t}Inner video{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="OPINION_MANAGER"}
                      <option value="publi-opinion">{t}Opinion frontpage{/t}</option>
                      <option value="publi-opinion-inner">{t}Inner opinion{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="ALBUM_MANAGER"}
                      <option value="publi-gallery">{t}Galleries{/t}</option>
                      <option value="publi-gallery-inner">{t}Gallery Inner{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="POLL_MANAGER"}
                      <option value="publi-poll">{t}Poll{/t}</option>
                      <option value="publi-poll-inner">{t}Poll Inner{/t}</option>
                    {/is_module_activated}
                    {is_module_activated name="NEWSLETTER_MANAGER"}
                      <option value="publi-newsletter">{t}Newsletter{/t}</option>
                    {/is_module_activated}
                    <option value="publi-others">{t}Others{/t}</option>
                  </select>
                </div>
              </div>
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
                    <div class="ng-cloak" ng-show="position == 'publi-others'">
                      {foreach $themeAds as $adId => $ad}
                        <div class="row">
                          <div class="col-md-12">
                            <div class="radio">
                              <input id="{$adId}" type="radio" name="type_advertisement" value="{$adId}" {if isset($advertisement) && $advertisement->type_advertisement == $adId}checked="checked" {/if}/>
                              <label for="ad-{$adId}">
                                {$ad['name']}
                              </label>
                            </div>
                          </div>
                        </div>
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
  </form>
{/block}
