{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/css/colorpicker.css" basepath="/js/jquery/jquery_colorpicker/"}
    <style type="text/css">
    .ui-widget-content a {
        color: #0B55C4 !important;
    }
    .help-block {
        color:#999;
        font-size:.95em;
        margin-top:0px !important;
        margin-bottom:10px !important;
    }
    .settings-header {
        font-weight: bold;
        color: #333;
        font-size:14px;
    }
    .colorpicker_input, colorpicker_viewer {
        display:inline-block;
        float:none;
    }
    </style>
{/block}

{block name="footer-js" append}
    {script_tag src="/jquery/jquery_colorpicker/js/colorpicker.js"}
    {script_tag src="/tiny_mce/opennemas-config.js"}

    <script type="text/javascript">
    tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
    OpenNeMas.tinyMceConfig.footer.elements = "site_footer";
    tinyMCE.init( OpenNeMas.tinyMceConfig.footer );

    jQuery(document).ready(function($) {
        $("#system-settings-tabbed").tabs();
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

        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });
    });
    </script>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}System Wide Settings{/t}</h2></div>
        <div class="buttons" style="display:none">
            <a href="" class="button"><span class="icon home">&nbsp;</span>  </a>
            <a href="" class="button"><span class="icon home">&nbsp;</span></a>
            <a href="" class="button"><span class="icon home">&nbsp;</span></a>
        </div>
    </div>
</div>

<div class="wrapper-content">

    {render_messages}

    <form action="{url name="admin_system_settings_save"}" enctype="multipart/form-data" method="POST" id="formulario">
        <div id="system-settings-tabbed" class="tabs">
            <ul>
                <li><a href="#general">{t}General{/t}</a></li>
                <li><a href="#seo">{t}SEO{/t}</a></li>
                <li><a href="#misc">{t}Internal{/t}</a></li>
                <li><a href="#external">{t}External Services{/t}</a></li>
            </ul>

            <div id="general" class="form-horizontal">
                <fieldset>
                    <div class="control-group">
                        <label for="site_name" class="control-label">{t}Site name{/t}</label>
                        <div class="controls">
                            <input type="text" id="site_name" name="site_name" value="{$configs['site_name']|default:""}" class="input-xlarge" required="required">
                            <div class="help-block">{t}This will be displayed as your site name.{/t}</div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="site_agency" class="control-label">{t}Site agency{/t}</label>
                        <div class="controls">
                            <input type="text" id="site_agency" name="site_agency" value="{$configs['site_agency']|default:""}" class="input-xlarge">
                            <div class="help-block">{t}This will be displayed as the default article signature.{/t}</div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="site_color" class="control-label">{t}Site color{/t}</label>
                        <div class="controls">
                            <input type="text" id="site_color" name="site_color" class="colorpicker_input" value="{$configs['site_color']|default:""}" class="input-xlarge">
                            <div class="colorpicker_viewer" style="background-color:#{$configs['site_color']}"></div>
                            <div class="help-block">{t}Color used for links, menus and some widgets.{/t}</div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="section_settings[allowLogo]" class="control-label">{t}Use custom logo{/t}</label>
                        <div class="controls">
                            <select name="section_settings[allowLogo]" id="section_settings[allowLogo]" onChange="toogleSiteLogo(this.value);">
                                <option value="0">{t}No{/t}</option>
                                <option value="1" {if $configs['section_settings']['allowLogo'] eq "1"} selected {/if}>{t}Yes{/t}</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group" {if $configs['section_settings']['allowLogo'] eq "0"}style="display:none"{/if}>
                        <label for="site_logo" class="control-label">{t}Site logo{/t}</label>
                        <div class="controls">
                            <input type="file" id="site_logo" name="site_logo">
                        </div>
                    </div>

                    <div class="control-group">
                        {if isset($configs['site_logo']) && $configs['section_settings']['allowLogo'] neq "0"}
                            <label for="site_logo"></label>
                            <div class="controls" >
                                <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/sections/{$configs['site_logo']}" style="max-height:100px">
                            </div>
                        {/if}
                    </div>

                    <div class="control-group">
                        <label for="site_footer" class="control-label">{t}Footer text{/t}</label>
                        <div class="controls">
                            <textarea id="site_footer" name="site_footer" cols="50" rows="7">{$configs['site_footer']|default:""}</textarea>
                            <div class="help-block">{t}Text showed at the bottom of your page. Usually used for copyright notice.{/t}</div>
                        </div>
                    </div>

                </fieldset>
            </div>

            <div id="seo" class="form-horizontal">
                <fieldset>
                    <h3 class="settings-header">{t}SEO options{/t}</h3>

                    <div class="control-group">
                        <label for="site_title" class="control-label">{t}Site title{/t}</label>
                        <div class="controls">
                            <input type="text" id="site_title" name="site_title" class="input-xxlarge" value="{$configs['site_title']|default:""}">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="site_description" class="control-label">{t}Site description{/t}</label>
                        <div class="controls">
                            <textarea id="site_description" name="site_description" class="input-xxlarge" cols=50 rows=5>{$configs['site_description']|default:""}</textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="site_description" class="control-label">{t}Site keywords{/t}</label>
                        <div class="controls">
                            <textarea id="site_keywords" name="site_keywords" class="input-xxlarge">{$configs['site_keywords']|default:""}</textarea>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <fieldset>
                    <h3 class="settings-header">{t}Web Master Tools{/t}</h3>

                    <div class="control-group">
                        <label for="webmastertools_google" class="control-label">{t}Google Web Master Tools{/t}</label>
                        <div class="controls">
                            <input type="text" id="webmastertools_google" name="webmastertools_google" class="input-xlarge" value="{$configs['webmastertools_google']|default:""}">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="webmastertools_bing" class="control-label">{t}Bing Web Master Tools{/t}</label>
                        <div class="controls">
                            <input type="text" id="webmastertools_bing" name="webmastertools_bing" class="input-xlarge" value="{$configs['webmastertools_bing']|default:""}">
                        </div>
                    </div>
                </fieldset>
            </div><!-- /seo -->

            <div id="misc" class="form-horizontal">
                <fieldset>
                    <h3 class="settings-header">{t}Language & Time{/t}</h3>

                    <div class="control-group">
                        <label for="site_language" class="control-label">{t}Default language{/t}</label>
                        <div class="controls">
                            {html_options name=site_language options=$languages selected=$configs['site_language']}
                            <div class="help-block">{t}Used for displayed messages, interface and measures in your page.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="time_zone" class="control-label">{t}Time Zone{/t}</label>
                        <div class="controls">
                            {html_options name=time_zone options=$timezones selected=$configs['time_zone']}
                            <div class="help-block">{t}Used for all the dates used in your webpage.{/t}</div>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <fieldset>
                    <h3 class="settings-header">{t}Listing & sorting{/t}</h3>

                    <div class="control-group">
                        <label for="refresh_interval" class="control-label">{t}Refresh page interval{/t}</label>
                        <div class="controls">
                            <input type="number" id="refresh_interval" name="refresh_interval" value="{$configs['refresh_interval']|default:900}">
                            <small>seconds</small>
                            <div class="help-block">{t}When a user visits pages and stay on it for a while, this setting allows to refresh the loaded page for updated it.{/t}</div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="items_per_page" class="control-label">{t}Items per page{/t}</label>
                        <div class="controls">
                            <input type="number" id="items_per_page" name="items_per_page" value="{$configs['items_per_page']|default:20}">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <h3 class="settings-header">{t}Sessions{/t}</h3>
                    <div class="control-group">
                        <label for="max_session_lifetime" class="control-label">{t}Max session lifetime{/t}</label>
                        <div class="controls">
                            <input type="number" id="max_session_lifetime" name="max_session_lifetime" class="input-xlarge" value="{$configs['max_session_lifetime']|default:"30"}">
                            <div class="help-block">{t}Minutes after a user session is considered as invalid.{/t}</div>
                        </div>
                    </div>
                </fieldset>
                <hr>
            </div>


            <div id="external" class="form-horizontal">

                <fieldset>
                    <h3 class="settings-header">{t}Google Services{/t}</h3>

                    <div class="control-group">
                        <label for="google_page" class="control-label">{t}Google+ Page Url{/t}</label>
                        <div class="controls">
                            <input type="text" id="google_page" name="google_page" value="{$configs['google_page']|default:""}" class="input-xxlarge">
                            <div class="help-block">{t escape=off}If you have a <b>Google+ page</b>, please complete this input.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="google_maps_api_key" class="control-label">{t}Google Maps API key{/t}</label>
                        <div class="controls">
                            <input type="text" id="google_maps_api_key" name="google_maps_api_key" value="{$configs['google_maps_api_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Google <strong>Maps</strong> API Key from <a href="http://code.google.com/apis/maps/signup.html" target="_blank">Google maps sign up website</a>.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="google_custom_search_api_key" class="control-label">{t}Google Search API key:{/t}</label>
                        <div class="controls">
                            <input type="text" id="google_custom_search_api_key" name="google_custom_search_api_key" value="{$configs['google_custom_search_api_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Google <strong>Search</strong> API Key from <a href="http://www.google.com/cse/manage/create" target="_blank">Google Search sign up website</a>.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="google_analytics_api_key" class="control-label">{t}Google Analytics API key{/t}</label>
                        <div class="controls">
                            <input type="text" id="google_analytics_api_key" name="google_analytics[api_key]" value="{$configs['google_analytics']['api_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Google Analytics Site ID from <a href="https://www.google.com/analytics/" target="_blank">GAnalytics site</a> under the General Overview list (should be something like UA-546457-3).{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="google_news_name" class="control-label">{t}Publication name in Google News{/t}</label>
                        <div class="controls">
                            <input type="text" id="google_news_name" name="google_news_name" value="{$configs['google_news_name']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Publication name in <a href="https://www.google.es/search?num=100&hl=es&safe=off&gl=es&tbm=nws&q={$smarty.server.HTTP_HOST}&oq={$smarty.server.HTTP_HOST}" target="_blank">Google News search</a> for your site.{/t}</div>
                        </div>
                    </div>

                </fieldset>
                <hr>
                <fieldset>
                    <h3 class="settings-header">{t}Facebook{/t}</h3>

                    <div class="control-group">
                        <label for="facebook_page" class="control-label">{t}Facebook Page Url{/t}</label>
                        <div class="controls">
                            <input type="text" id="facebook_page" name="facebook_page" value="{$configs['facebook_page']|default:""}" class="input-xxlarge">
                            <div class="help-block">{t escape=off}If you have a <b>facebook page</b>, please complete the form with your facebook page url and Id.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="facebook_id" class="control-label">{t}Facebook Id{/t}</label>
                        <div class="controls">
                            <input type="text" id="facebook_id" name="facebook_id" value="{$configs['facebook_id']|default:""}" class="input-xlarge">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="facebook_api_key" class="control-label">{t}APP key{/t}</label>
                        <div class="controls">
                            <input type="text" id="facebook_api_key" name="facebook[api_key]" value="{$configs['facebook']['api_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers website</a>.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="facebook_secret_key" class="control-label">{t}Secret key{/t}</label>
                        <div class="controls">
                            <input type="text" id="facebook_secret_key" name="facebook[secret_key]" value="{$configs['facebook']['secret_key']|default:""}" class="input-xlarge">
                        </div>
                    </div>

                </fieldset>
                <hr>
                <fieldset>
                    <h3 class="settings-header">{t}Twitter{/t}</h3>

                    <div class="control-group">
                        <label for="twitter_page" class="control-label">{t}Twitter Page{/t}</label>
                        <div class="controls">
                            <input type="text" id="twitter_page" name="twitter_page" value="{$configs['twitter_page']|default:""}" class="input-xxlarge">
                            <div class="help-block">{t escape=off}If you also have a <b>twitter page</b>, add your profile name on the form. <br/>Default will be set with Opennemas.{/t}</div>
                        </div>
                    </div>

                </fieldset>
                {acl isAllowed="ONLY_MASTERS"}
                <hr>
                <fieldset>
                    <h3 class="settings-header">{t}Piwik Statistics{/t}</h3>

                    <div class="control-group">
                        <label for="piwik_page_id" class="control-label">{t}Page ID{/t}</label>
                        <div class="controls">
                            <input type="text" id="piwik_page_id" name="piwik[page_id]" value="{$configs['piwik']['page_id']|default:""}" class="input-xlarge">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="piwik_server_url" class="control-label">{t}Server URL{/t}</label>
                        <div class="controls">
                            <input type="text" id="piwik_server_url" name="piwik[server_url]" value="{$configs['piwik']['server_url']|default:""}" class="input-xlarge">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="piwik_token_auth" class="control-label">{t}User token auth{/t}</label>
                        <div class="controls">
                            <input type="text" id="piwik_token_auth" name="piwik[token_auth]" value="{$configs['piwik']['token_auth']|default:""}" class="input-xlarge">
                        </div>
                    </div>
                </fieldset>
                {/acl}
                <hr>
                <fieldset>
                    <h3 class="settings-header">{t}OJD Statistics{/t}</h3>

                    <div class="control-group">
                        <label for="ojd_page_id" class="control-label">{t}OJD Page ID{/t}</label>
                        <div class="controls">
                            <input type="text" id="ojd_page_id" name="ojd[page_id]" value="{$configs['ojd']['page_id']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}If you also have a <b>OJD statistics service</b>, add your page id{/t}</div>
                        </div>
                    </div>
                </fieldset>

                <hr>
                <fieldset>
                    <h3 class="settings-header">{t}Recaptcha{/t}</h3>

                    <div class="control-group">
                        <label for="recaptcha_public_key" class="control-label">{t}Public key{/t}</label>
                        <div class="controls">
                            <input type="text" id="recaptcha_public_key" name="recaptcha[public_key]" value="{$configs['recaptcha']['public_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}Get your reCaptcha key from <a href="http://www.google.com/recaptcha/whyrecaptcha" target="_blank">this page</a>.{/t}<br>{t}Used when we want to test if the user is an human and not a robot.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="piwik_token_auth" class="control-label">{t}Private key{/t}</label>
                        <div class="controls">
                            <input type="text" id="recaptcha_private_key" name="recaptcha[private_key]" value="{$configs['recaptcha']['private_key']|default:""}" class="input-xlarge">
                        </div>
                    </div>

                </fieldset>

                <hr>
                <fieldset>

                    <h3 class="settings-header">{t}Paypal Settings{/t}</h3>

                    <div class="control-group">
                        <label for="paypal_mail" class="control-label">{t}Account email:{/t}</label>
                        <div class="controls">
                            <input type="text" id="paypal_mail" name="paypal_mail" value="{$configs['paypal_mail']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your PayPal account email from <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_registration-run" target="_blank">PayPal site</a>. This must be a business account for receiving payments{/t}</div>
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>


        <div class="action-bar clearfix">
            <div class="right">
                <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button red">
            </div>
        </div>

    </form>
</div>
{/block}