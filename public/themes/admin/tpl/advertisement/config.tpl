{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_ads_config}" method="POST">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-home fa-lg"></i>
              <span class="hidden-xs">{t}Advertisements{/t}</span>
              <span class="visible-xs-inline">{t}Ads{/t}</span>
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Configuration{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li>
              <a href="{url name=admin_ads}" class="btn btn-link" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li>
              <button type="submit" class="btn btn-primary">
                <span class="fa fa-save"></span> {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content ng-cloak">
    <uib-tabset>
      <uib-tab heading="{t}General{/t}">
        <div class="form-group">
          <label for="ads_settings_lifetime_cookie" class="form-label">
            {t}Cookie lifetime for intersticials{/t}
          </label>
          <div class="controls">
            <input type="number" class="form-control" name="ads_settings_lifetime_cookie" id="ads_settings_lifetime_cookie" value="{$configs['ads_settings']['lifetime_cookie']|default:'300'}" required/>
            <span class="help">{t}This setting indicates how long will take to re-display the interstitial in frontpage.{/t} {t}(in minutes){/t}</span>
          </div>
        </div>
        <div class="form-group">
          <label for="ads_settings_mark_default" class="form-label">
            {t}Default mark shown indicating an advertisement{/t}
          </label>
          <div class="controls">
            <input type="text" class="form-control" name="ads_settings_mark_default" id="ads_settings_mark_default" value="{$configs['ads_settings']['default_mark']}" placeholder="{t}Advertisement{/t}"/>
            {capture name=default_mark}{t}Advertisement{/t}{/capture}
            <span class="help">{t 1=$smarty.capture.default_mark}This is the text shown near advertisements. If you don't fill this field, the default value "%1" will be shown.{/t}</span>
          </div>
        </div>
        <div class="form-group">
          <div class="checkbox">
            <input{if $configs['ads_settings']['no_generics'] eq "0"} checked{/if} id="ads_settings_no_generics" name="ads_settings_no_generics" type="checkbox" value="0">
            <label for="ads_settings_no_generics" class="form-label">{t}Allow generic advertisement{/t}</label>
            <div class="help m-t-5">{t}This settings allow printing home ads when ads in category are empty.{/t}</div>
          </div>
        </div>
        {acl isAllowed="MASTER"}
          <div class="form-group">
            <div class="checkbox">
              <input {if $configs['ads_settings']['safe_frame'] eq 1}checked{/if} id="safe-frame" name="safe_frame" type="checkbox">
              <label for="safe-frame">{t}SafeFrame by default{/t} ({t}Recommended{/t})</label>
            </div>
            <div class="help m-t-5">{t}This feature displays advertisements inside iframes. It improves the user experience and has a better performance.{/t}</div>
          </div>
        {/acl}
      </uib-tab>
      <uib-tab heading="{t}External services{/t}">
        <div class="tab-wrapper">
          <div class="row">
            <div class="col-md-6">
              <div class="p-r-15">
                <h4>{t}AdSense integration{/t}</h4>
                <div class="form-group">
                  <label for="adsense_id" class="form-label">{t}AdSense ID{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="adsense_id" value="{$configs['adsense_id']}">
                    <div class="help">{t}The AdSense ID (i.e. ca-pub-0000000000000000){/t}</div>
                  </div>
                </div>
                <h4>{t}OpenX/Revive Ad server integration{/t}</h4>
                <div class="form-group">
                  <label for="revive_ad_server_url" class="form-label">{t}Ad server base url{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="revive_ad_server_url" value="{$configs['revive_ad_server']['url']}">
                    <div class="help">{t}The ad server URL (i.e. http://ad.serverexample.net/).{/t}</div>
                  </div>
                </div>
                <h4>{t}Smart Ad server integration{/t}</h4>
                <div class="form-group" ng-init="smart.domain = {json_encode($configs['smart_ad_server']['domain'])|clear_json}">
                  <label for="smart_ad_server_domain" class="form-label">{t}Domain{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="smart_ad_server_domain" ng-model="smart.domain" value="{$configs['smart_ad_server']['domain']}">
                    <div class="help">{t}The ad server Domain (i.e. https://www8.smartadserver.com/).{/t}</div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="smart_ad_server_network_id" class="form-label">{t}Network ID{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="smart_ad_server_network_id" ng-required="smart.domain" value="{$configs['smart_ad_server']['network_id']}">
                    <div class="help">{t}The ad server network ID{/t}</div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="smart_ad_server_site_id" class="form-label">{t}Site ID{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="smart_ad_server_site_id" ng-required="smart.domain" value="{$configs['smart_ad_server']['site_id']}">
                    <div class="help">{t}The ad server site ID{/t}</div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="smart_ad_server_site_id" class="form-label">{t}Page ID{/t}</label>
                </div>
                <div class="form-group">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="page_id_frontpage" class="col-sm-4 col-form-label">{t}Frontpages{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_frontpage" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_frontpage" value="{$configs['smart_ad_server']['page_id']['frontpage']}">
                      </div>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="page_id_article_inner" class="col-sm-4 col-form-label">{t}Article: inner{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_article_inner" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_article_inner" value="{$configs['smart_ad_server']['page_id']['article_inner']}">
                      </div>
                    </div>
                  </div>
                  {is_module_activated name="OPINION_MANAGER"}
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="page_id_opinion_frontpage" class="col-sm-4 col-form-label">{t}Opinion: frontpage{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_opinion_frontpage" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_opinion_frontpage" value="{$configs['smart_ad_server']['page_id']['opinion_frontpage']}">
                      </div>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="page_id_opinion_inner" class="col-sm-4 col-form-label">{t}Opinion: inner{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_opinion_inner" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_opinion_inner" value="{$configs['smart_ad_server']['page_id']['opinion_inner']}">
                      </div>
                    </div>
                  </div>
                  {/is_module_activated}
                  {is_module_activated name="VIDEO_MANAGER"}
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="page_id_video_frontpage" class="col-sm-4 col-form-label">{t}Video: frontpages{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_video_frontpage" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_video_frontpage" value="{$configs['smart_ad_server']['page_id']['video_frontpage']}">
                      </div>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="page_id_video_inner" class="col-sm-4 col-form-label">{t}Video: inner{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_video_inner" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_video_inner" value="{$configs['smart_ad_server']['page_id']['video_inner']}">
                      </div>
                    </div>
                  </div>
                  {/is_module_activated}
                  {is_module_activated name="ALBUM_MANAGER"}
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="page_id_album_frontpage" class="col-sm-4 col-form-label">{t}Album: frontpages{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_album_frontpage" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_album_frontpage" value="{$configs['smart_ad_server']['page_id']['album_frontpage']}">
                      </div>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="page_id_album_inner" class="col-sm-4 col-form-label">{t}Album: inner{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_album_inner" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_album_inner" value="{$configs['smart_ad_server']['page_id']['album_inner']}">
                      </div>
                    </div>
                  </div>
                  {/is_module_activated}
                  {is_module_activated name="POLL_MANAGER"}
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="page_id_poll_frontpage" class="col-sm-4 col-form-label">{t}Poll: frontpage{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_poll_frontpage" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_polls_frontpage" value="{$configs['smart_ad_server']['page_id']['polls_frontpage']}">
                      </div>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="page_id_poll_inner" class="col-sm-4 col-form-label">{t}Poll: inner{/t}</label>
                      <div class="col-sm-8">
                        <input class="form-control" id="page_id_poll_inner" type="text" ng-required="smart.domain" name="smart_ad_server_page_id_polls_inner" value="{$configs['smart_ad_server']['page_id']['polls_inner']}">
                      </div>
                    </div>
                  </div>
                  {/is_module_activated}
                  <div class="help">{t}The ad server pages ID{/t}</div>
                </div>
                <div class="form-group">
                  <label for="smart_ad_server_targeting" class="form-label">{t}Targeting{/t}</label>
                </div>
                <div class="form-group">
                  <div class="form-row">
                    <label for="category_targeting" class="col-sm-2 col-form-label">{t}Category{/t}</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="category_targeting" type="text" name="smart_ad_server_category_targeting" value="{$configs['smart_ad_server']['category_targeting']}">
                    </div>
                  </div>
                  <div class="form-row">
                    <label for="module_targeting" class="col-sm-2 col-form-label">{t}Module{/t}</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="module_targeting" type="text" name="smart_ad_server_module_targeting" value="{$configs['smart_ad_server']['module_targeting']}">
                    </div>
                  </div>
                  <div class="form-row">
                    <label for="url_targeting" class="col-sm-2 col-form-label">{t}Url{/t}</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="url_targeting" type="text" name="smart_ad_server_url_targeting" value="{$configs['smart_ad_server']['url_targeting']}">
                    </div>
                  </div>
                  {if $app.security->hasPermission('MASTER')}
                  <div class="form-row">
                    <label for="url_targeting" class="col-sm-2 col-form-label">{t}Custom code{/t}</label>
                    <div class="col-sm-10">
                      <textarea class="form-control" name="smart_custom_code">{$configs['smart_custom_code']|base64_decode|escape:'html'}</textarea>
                    </div>
                  </div>
                  {/if}
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-l-15">
                <h4>{t}DFP targeting{/t}</h4>
                <div class="form-group">
                  <label for="dfp_options_target" class="form-label">{t}Key for category targeting{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="dfp_options_target" value="{$configs['dfp_options']['target']}">
                    <div class="help">{t}Set a key for targeting your ads by category. Note that the value for targeting will always be the current category internal name{/t}</div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="dfp_options_module" class="form-label">{t}Key for module targeting{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="dfp_options_module" value="{$configs['dfp_options']['module']}">
                    <div class="help">{t}Set a key for targeting your ads by module. Note that the value for targeting will always be the current module name{/t}</div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="dfp_options_content_id" class="form-label">{t}Key for content ID targeting{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="dfp_options_content_id" value="{$configs['dfp_options']['content_id']}">
                    <div class="help">{t}Set a key for targeting your ads by content ID. Note that the value for targeting will always be the current content ID{/t}</div>
                  </div>
                </div>
                {if $app.security->hasPermission('MASTER')}
                <h4>{t}DFP custom code{/t}</h4>
                <div class="form-group">
                  <label for="dfp_custom_code" class="form-label">{t}Custom code at the end of DFP tags{/t}</label>
                  <div class="controls">
                    <textarea class="form-control" name="dfp_custom_code">{$configs['dfp_custom_code']|base64_decode|escape:'html'}</textarea>
                  </div>
                </div>
                {/if}
                <h4 class="m-t-30">{t}Tradedoubler integration{/t}</h4>
                <div class="form-group">
                  <label for="tradedoubler_id" class="form-label">{t}Tradedoubler ID{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="number" name="tradedoubler_id" value="{$configs['tradedoubler_id']}">
                    <div class="help">{t}Only the ID from Tradedoubler validation tag{/t}</div>
                  </div>
                </div>
                {is_module_activated name="IADBOX_MANAGER"}
                <h4 class="m-t-30">{t}Iadbox integration{/t}</h4>
                <div class="form-group">
                  <label for="iadbox_id" class="form-label">{t}Iadbox ID{/t}</label>
                  <div class="controls">
                    <input class="form-control" type="text" name="iadbox_id" value="{$configs['iadbox_id']}">
                    <div class="help">{t}Iadbox affiliate ID{/t}</div>
                  </div>
                </div>
                {/is_module_activated}
              </div>
            </div>
          </div>
        </div>
      </uib-tab>
      {is_module_activated name="es.openhost.module.advancedAdvertisement"}
      <uib-tab heading="{t}Advanced{/t}">
        <div class="form-group">
          <label for="ads_txt" class="form-label">
            {t}Authorized Digital Sellers{/t}
          </label>
          <div class="controls">
            <textarea class="form-control" name="ads_txt" rows="20">{$configs['ads_txt']|default:''}</textarea>
            <span class="help">{t}This will be the content of the file ads.txt on your root domain{/t}</span>
          </div>
        </div>
      </uib-tab>
      {/is_module_activated}
    </uib-tabset>
  </div>
</form>
{/block}
