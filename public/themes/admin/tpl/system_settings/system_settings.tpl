{extends file="base/admin.tpl"}

{block name="header-css" append}
     {stylesheets src="@AdminTheme/js/jquery/jquery_colorpicker/css/colorpicker.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}">
    {/stylesheets}

    <style type="text/css">
      .colorpicker {
        z-index: 10;
      }
    </style>
{/block}

{block name="footer-js" append}
    {javascripts src="@AdminTheme/js/jquery/jquery_colorpicker/js/colorpicker.js,
        @Common/js/onm/md5.min.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}

    <script type="text/javascript">

    jQuery(document).ready(function($) {
        //Color Picker jQuery
        $('#site_color').ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                $(el).val(hex);
                $(el).ColorPickerHide();
            },
            onChange: function (hsb, hex, rgb) {
                $('#site_color').val(hex);
                $('.colorpicker_viewer').css('background-color', '#' + hex);
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
            }
        }).bind('keyup', function(){
            $(this).ColorPickerSetColor(this.value);
        });

        toogleSiteLogo = function(value) {
            if(value == 0) {
                $('#site_logo_block').hide();
            } else {
                $('#site_logo_block').show();
            }
        }

        $('.check-pass').on('click', function(e, ui){
            e.preventDefault();
            var passInput = $('#onm_digest_pass');
            var btn = $(this);
            if (passInput.attr('type') == 'password') {
                passInput.prop('type','text');
            } else {
                passInput.prop('type','password');
            }

            btn.find('i').toggleClass('fa-unlock-alt');
        });

        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });
    });
    </script>
{/block}

{block name="content"}
  <form action="{url name="admin_system_settings_save"}" enctype="multipart/form-data" method="POST" id="formulario">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-cogs fa-lg"></i>
                {t}Settings{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit" value="1">
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

      <div class="grid simple">
        <div class="grid-body no-padding">
          <tabset>
            <tab heading="{t}General{/t}">
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-8">
                    <div class="form-group">
                      <label class="form-label" for="site_name">
                        {t}Site name{/t}
                      </label>
                      <span class="help">
                        {t}This will be displayed as your site name.{/t}
                      </span>
                      <div class="controls">
                        <input class="form-control" id="site_name" name="site_name" required="required" type="text" value="{$configs['site_name']|default:""}" class="input-xlarge">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_agency">
                        {t}Site agency{/t}
                      </label>
                      <span class="help">
                        {t}This will be displayed as the default article signature.{/t}
                      </span>
                      <div class="controls">
                        <input class="form-control"  id="site_agency" name="site_agency" type="text" value="{$configs['site_agency']|default:""}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_color">
                        {t}Site color{/t}
                      </label>
                      <span class="help">
                        {t}Color used for links, menus and some widgets.{/t}
                      </span>
                      <div class="controls">
                        <div class="input-group">
                          <span class="colorpicker_viewer input-group-addon" id="colorpicker_viewer" style="background-color:#{$configs['site_color']}">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                          </span>
                          <input class="form-control colorpicker_input" id="site_color" name="site_color" readonly="readonly" type="text" value="{$configs['site_color']|default:""}">
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_footer">
                        {t}Footer text{/t}
                      </label>
                      <span class="help">
                        {t}Text showed at the bottom of your page. Usually used for copyright notice.{/t}
                      </span>
                      <div class="controls">
                        <textarea class="form-control" onm-editor onm-editor-preset="simple" id="site_footer" name="site_footer">{$configs['site_footer']|default:""}</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="form-label" for="section_settings[allowLogo]" >
                        {t}Use custom logo{/t}
                      </label>
                      <div class="controls">
                        <select id="section_settings[allowLogo]" name="section_settings[allowLogo]" onChange="toogleSiteLogo(this.value);">
                          <option value="0">{t}No{/t}</option>
                          <option value="1" {if $configs['section_settings']['allowLogo'] eq "1"} selected {/if}>{t}Yes{/t}</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group" {if $configs['section_settings']['allowLogo'] eq "0"}style="display:none"{/if}>
                      <label class="form-label" for="site_logo">
                        {t}Site logo{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="site_logo" name="site_logo" type="file">
                        {if !empty($configs['site_logo']) && $configs['section_settings']['allowLogo'] neq "0"}
                        <img src="{$smarty.const.MEDIA_URL}{$smarty.const.MEDIA_DIR}/sections/{$configs['site_logo']}" style="max-height:90px">
                        {/if}
                      </div>
                    </div>
                    <div class="form-group" {if $configs['section_settings']['allowLogo'] eq "0"}style="display:none"{/if}>
                      <label class="form-label" for="favico">
                        {t}Favico{/t}
                      </label>
                      <div class="controls">
                        <input id="favico" name="favico" type="file">
                        {if !empty($configs['favico']) && $configs['section_settings']['allowLogo'] neq "0"}
                        <img src="{$smarty.const.MEDIA_URL}{$smarty.const.MEDIA_DIR}/sections/{$configs['favico']}" style="max-height:20px;">
                        {/if}
                      </div>
                    </div>
                    <div class="form-group" {if $configs['section_settings']['allowLogo'] eq "0"}style="display:none"{/if}>
                      <label class="form-label" for="mobile_logo">{t}Site Mobile logo{/t}</label>
                      <div class="controls">
                        <input id="mobile_logo" name="mobile_logo" type="file">
                        {if !empty($configs['mobile_logo']) && $configs['section_settings']['allowLogo'] neq "0"}
                        <img src="{$smarty.const.MEDIA_URL}{$smarty.const.MEDIA_DIR}/sections/{$configs['mobile_logo']}" style="max-height:30px;">
                        {/if}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </tab>
            <tab heading="{t}SEO{/t}">
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <h4>{t}SEO options{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="site_title">
                        {t}Site title{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="site_title" name="site_title" type="text" value="{$configs['site_title']|default:""}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_description">
                        {t}Site description{/t}
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="site_description" name="site_description">{$configs['site_description']|default:""}</textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_description">
                       {t}Site keywords{/t}
                     </label>
                     <div class="controls">
                        <textarea class="form-control" id="site_keywords" name="site_keywords">{$configs['site_keywords']|default:""}</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h4>{t}Web Master Tools{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="webmastertools_google">
                        {t}Google Web Master Tools{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="webmastertools_google" name="webmastertools_google" type="text" value="{$configs['webmastertools_google']|default:""}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="webmastertools_bing">
                        {t}Bing Web Master Tools{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control"  id="webmastertools_bing" name="webmastertools_bing" class="input-xlarge" type="text" value="{$configs['webmastertools_bing']|default:""}">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </tab>
            <tab heading="{t}Internal{/t}">
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <h4>{t}Cookies agreement{/t}</h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input class="form-control" id="cookies_hint_enabled" name="cookies_hint_enabled" type="checkbox" value="1" {if $configs['cookies_hint_enabled'] == 1}checked{/if}>
                        <label class="form-label" for="cookies_hint_enabled">
                          {t}Enable cookies agreement{/t}
                        </label>
                      </div>
                      <div class="controls">
                        <span class="help">
                          {t}Mark this if you want to show a message to your users that your site is using cookies.{/t}
                        </span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="webmastertools_bing">
                        {t}Cookie agreement page URL{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="cookies_hint_url" name="cookies_hint_url" type="text" value="{$configs['cookies_hint_url']|default:""}">
                      </div>
                    </div>
                    <h4>{t}Language & Time{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="site_language">
                        {t}Default language{/t}
                      </label>
                      <span class="help">
                        {t}Used for displayed messages, interface and measures in your page.{/t}
                      </span>
                      <div class="controls">
                        {html_options name=site_language options=$languages selected=$configs['site_language']}
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="time_zone">
                        {t}Time Zone{/t}
                      </label>
                      <span class="help">
                        {t}Used for all the dates used in your webpage.{/t}
                      </span>
                      <div class="controls">
                        {html_options name=time_zone options=$timezones selected=$configs['time_zone']}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h4>{t}Listing & sorting{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="refresh_interval">
                        {t}Refresh page interval{/t}
                        <small>(seconds)</small>
                      </label>
                      <span class="help">
                        {t}When a user visits pages and stay on it for a while, this setting allows to refresh the loaded page for updated it.{/t}
                      </span>
                      <div class="controls">
                        <input class="form-control" id="refresh_interval" name="refresh_interval" type="number" value="{$configs['refresh_interval']|default:900}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label class="form-label" for="items_per_page">
                          {t}Items per page{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="items_per_page" name="items_per_page" type="number" value="{$configs['items_per_page']|default:20}">
                        </div>
                      </div>
                      {is_module_activated name="FRONTPAGES_LAYOUT"}
                        <div class="col-md-6 form-group">
                          <label class="form-label" for="items_in_blog">
                            {t}Items per blog page{/t}
                          </label>
                          <div class="controls">
                            <input class="form-control" id="items_in_blog" name="items_in_blog" type="number" value="{$configs['items_in_blog']|default:10}">
                          </div>
                        </div>
                      {/is_module_activated}
                    </div>
                    {is_module_activated name="FORM_MANAGER"}
                      <h4>{t}Form Module{/t}</h4>
                      <div class="form-group">
                        <label class="form-label" for="contact_email">
                          {t}Contact email{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="contact_email" name="contact_email" type="text" value="{$configs['contact_email']}">
                        </div>
                      </div>
                    {/is_module_activated}
                  </div>
                </div>
              </div>
            </tab>
            <tab heading="{t}External services{/t}">
              <div class="tab-wrapper">
                <h4>{t}Google Services{/t}</h4>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="google_page">
                        {t}Google+ Page Url{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="google_page" name="google_page" type="text" value="{$configs['google_page']|default:""}">
                        <span class="help">
                          {t escape=off}If you have a <b>Google+ page</b>, please complete this input.{/t}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="google_custom_search_api_key">
                        {t}Google Search API key:{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="google_custom_search_api_key" name="google_custom_search_api_key" type="text" value="{$configs['google_custom_search_api_key']|default:""}">
                        <span class="help">
                          {t escape=off}You can get your Google <strong>Search</strong> API Key from <a href="http://www.google.com/cse/manage/create" target="_blank">Google Search sign up website</a>.{/t}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="google_news_name">
                        {t}Publication name in Google News{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="google_news_name" name="google_news_name" type="text" value="{$configs['google_news_name']|default:""}">
                        <span class="help">
                          {t escape=off}You can get your Publication name in <a href="https://www.google.es/search?num=100&hl=es&safe=off&gl=es&tbm=nws&q={$smarty.server.HTTP_HOST}&oq={$smarty.server.HTTP_HOST}" target="_blank">Google News search</a> for your site.{/t}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="google_maps_api_key">
                        {t}Google Maps API key{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="google_maps_api_key" name="google_maps_api_key" type="text" value="{$configs['google_maps_api_key']|default:""}">
                        <span class="help">
                          {t escape=off}You can get your Google <strong>Maps</strong> API Key from <a href="http://code.google.com/apis/maps/signup.html" target="_blank">Google maps sign up website</a>.{/t}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="youtube_page">
                        {t}YouTube Page Url{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="youtube_page" name="youtube_page" type="text" value="{$configs['youtube_page']|default:""}">
                        <span class="help">
                          {t escape=off}If you have a <b>Youtube page</b>, please complete the form with your youtube page url.{/t}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="google_analytics_api_key">
                        {t}Google Analytics API key{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="google_analytics_api_key" name="google_analytics[api_key]" type="text" value="{$configs['google_analytics']['api_key']|default:""}">
                        <span class="help">
                          {t escape=off}You can get your Google Analytics Site ID from <a href="https://www.google.com/analytics/" target="_blank">GAnalytics site</a> under the General Overview list (should be something like UA-546457-3).{/t}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <h4>{t}Facebook{/t}</h4>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="facebook_page">
                        {t}Facebook Page Url{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="facebook_page" name="facebook_page" type="text" value="{$configs['facebook_page']|default:""}">
                        <span class="help">
                          {t escape=off}If you have a <b>facebook page</b>, please complete the form with your facebook page url and Id.{/t}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="facebook_id">
                        {t}Facebook Id{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="facebook_id" name="facebook_id" type="text" value="{$configs['facebook_id']|default:""}">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="facebook_api_key">
                        {t}APP key{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="facebook_api_key" name="facebook[api_key]" type="text" value="{$configs['facebook']['api_key']|default:""}">
                        <span class="help">
                          {t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers website</a>.{/t}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label" for="facebook_secret_key">
                        {t}Secret key{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="facebook_secret_key" name="facebook[secret_key]" type="text" value="{$configs['facebook']['secret_key']|default:""}">
                      </div>
                    </div>
                  </div>
                </div>
                <h4>{t}Twitter{/t}</h4>
                <div class="form-group">
                  <label class="form-label" for="twitter_page">
                    {t}Twitter Page{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="twitter_page" name="twitter_page" type="text" value="{$configs['twitter_page']|default:""}">
                    <span class="help">
                      {t escape=off}If you also have a <b>twitter page</b>, add your profile name on the form. Default will be set with Opennemas.{/t}
                    </span>
                  </div>
                </div>
                {is_module_activated name="PAYWALL"}
                  <h4>{t}Paypal Settings{/t}</h4>
                  <div class="form-group">
                    <label class="form-label" for="paypal_mail">
                      {t}Account email:{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="paypal_mail" name="paypal_mail" type="text" value="{$configs['paypal_mail']|default:""}">
                      <div class="help">
                        {t escape=off}You can get your PayPal account email from <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_registration-run" target="_blank">PayPal site</a>. This must be a business account for receiving payments{/t}
                      </div>
                    </div>
                  </div>
                {/is_module_activated}

                {is_module_activated name="NEWS_AGENCY_IMPORTER"}
                  <h4>{t}Opennemas News Agency{/t}</h4>
                  <div class="form-group">
                    <label class="form-label" for="onm_digest_user">
                      {t}User{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="onm_digest_user" name="onm_digest_user" type="text" value="{$configs['onm_digest_user']|default:""}">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="onm_digest_pass">
                      {t}Password{/t}
                    </label>
                    <div class="controls">
                      <div class="input-group">
                        <input class="form-control" id="onm_digest_pass" name="onm_digest_pass" type="password" value="{$configs['onm_digest_pass']|default:""}">
                        <div class="input-group-btn">
                          <button class="btn check-pass" type="button">
                            <i class="fa fa-lock"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                {/is_module_activated}
                <h4>{t}Recaptcha{/t}</h4>
                <div class="form-group">
                  <label class="form-label" for="recaptcha_public_key">
                    {t}Public key{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="recaptcha_public_key" name="recaptcha[public_key]" type="text" value="{$configs['recaptcha']['public_key']|default:""}">
                    <span class="help">
                      {t escape=off}Get your reCaptcha key from <a href="https://www.google.com/recaptcha/admin#whyrecaptcha" target="_blank">this page</a>.{/t} {t}Used when we want to test if the user is an human and not a robot.{/t}
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="piwik_token_auth">
                    {t}Private key{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="recaptcha_private_key" name="recaptcha[private_key]" type="text" value="{$configs['recaptcha']['private_key']|default:""}">
                  </div>
                </div>
                  <h4>{t}OJD Statistics{/t}</h4>
                  <div class="form-group">
                    <label class="form-label" for="ojd_page_id">
                      {t}OJD Page ID{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="ojd_page_id" name="ojd[page_id]" type="text" value="{$configs['ojd']['page_id']|default:""}">
                      <div class="help">{t escape=off}If you also have a <b>OJD statistics service</b>, add your page id{/t}</div>
                    </div>
                  </div>
                  <h4>{t}ComScore Statistics{/t}</h4>
                  <div class="form-group">
                    <label class="form-label" for="comscore_page_id">
                      {t}comScore Page ID{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="comscore_page_id" name="comscore[page_id]" type="text" value="{$configs['comscore']['page_id']|default:""}">
                      <div class="help">
                        {t escape=off}If you also have a <b>comScore statistics service</b>, add your page id{/t}
                      </div>
                    </div>
                  </div>
              </div>
            </tab>
          </tabset>
        </div>
      </div>
    </div>
  </form>
{/block}
