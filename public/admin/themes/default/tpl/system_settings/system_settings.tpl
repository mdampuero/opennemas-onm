{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/css/colorpicker.css" basepath="/js/jquery/jquery_colorpicker/"}
    <style type="text/css">
    .ui-widget-content a {
        color: #0B55C4 !important;
    }
    legend {
        font-size: 14px;
        font-weight: bold;
    }
    </style>
{/block}

{block name="footer-js" append}
    {script_tag src="/jquery/jquery.min.js"}
    {script_tag src="/jquery/jquery_colorpicker/js/colorpicker.js"}
    {script_tag src="/tiny_mce/opennemas-config.js"}

    <script type="text/javascript">
    tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
    OpenNeMas.tinyMceConfig.footer.elements = "site_footer";
    tinyMCE.init( OpenNeMas.tinyMceConfig.footer );

    jQuery(document).ready(function() {
        jQuery("#system-settings-tabbed").tabs();
        //Color Picker jQuery
        jQuery('#site_color').ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                jQuery(el).val(hex);
                jQuery(el).ColorPickerHide();
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('.colopicker_viewer').css('background-color', '#' + hex);
            },
            onBeforeShow: function () {
                jQuery(this).ColorPickerSetColor(this.value);
            }
        }).bind('keyup', function(){
            jQuery(this).ColorPickerSetColor(this.value);
        });

        toogleSiteLogo = function(value) {
            if(value == 0) {
                jQuery('#site_logo_block').hide();
            } else {
                jQuery('#site_logo_block').show();
            }
        }
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

    <form action="{url name="admin_system_settings_save"}" enctype="multipart/form-data" method="post">
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
                            <input type="text" id="site_name" name="site_name" value="{$configs['site_name']|default:""}" class="input-xlarge">
                            <div class="help-block">{t}You can change the name of your site here. This will be displayed as your site name{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="site_agency" class="control-label">{t}Site agency{/t}</label>
                        <div class="controls">
                            <input type="text" id="site_agency" name="site_agency" value="{$configs['site_agency']|default:""}" class="input-xlarge">
                            <div class="help-block">{t}You can edit the site agency for the articles here. This will be displayed as your article agency{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="site_color" class="control-label">{t}Site color{/t}</label>
                        <div class="controls">
                            <input type="text" id="site_color" name="site_color" class="colorpicker_input" value="{$configs['site_color']|default:""}" class="input-xlarge">
                            <div class="colopicker_viewer" style="background-color:#{$configs['site_color']}"></div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="section_settings[allowLogo]" class="control-label">{t}Use logo in frontpage{/t}</label>
                        <div class="controls">

                            <select name="section_settings[allowLogo]" id="section_settings[allowLogo]" onChange="toogleSiteLogo(this.value);">
                                <option value="0">{t}No{/t}</option>
                                <option value="1" {if $configs['section_settings']['allowLogo'] eq "1"} selected {/if}>{t}Yes{/t}</option>
                            </select>
                            <div class="help-block">
                                {t}Change the color of the menu bars. If you wanna change the categorys color, go to the Category Manager and edit a category.{/t}
                            </div>
                        </div>
                    </div>

                    <div class="control-group" id="site_logo_block" {if $configs['section_settings']['allowLogo'] eq "0"}style="display:none"{/if}>
                        <label for="site_logo" class="control-label">{t}Site logo{/t}</label>
                        <div class="controls">
                            <input type="file" id="site_logo" name="site_logo">
                            <div class="help-block">{t}You can enable Logos and category colors for your site here.{/t}</div>
                        </div>
                    </div>

                    {if isset($configs['site_logo'])}
                    <div class="control-group" id="site_logo_block" {if $configs['section_settings']['allowLogo'] eq "0"}style="display:none"{/if}>
                        <label for="site_logo"></label>
                        <div class="controls" >
                            <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/sections/{$configs['site_logo']}" style="max-height:100px">
                        </div>
                    </div>
                    {/if}

                    <div class="control-group" id="site_logo_block" >
                        <label for="site_footer" class="control-label">{t}Footer text{/t}</label>
                        <div class="controls">
                            <textarea id="site_footer" name="site_footer" cols="50" rows="7">{$configs['site_footer']|default:""}</textarea>
                            <div class="help-block">{t}You can edit here the footer of the site.{/t}</div>
                        </div>
                    </div>

                </fieldset>
            </div>

            <div id="seo" class="form-horizontal">
                <fieldset>
                    <legend>{t}SEO options{/t}</legend>

                    <div class="control-group">
                        <label for="site_title" class="control-label">{t}Site title{/t}</label>
                        <div class="controls">
                            <input type="text" id="site_title" name="site_title" class="input-xlarge" value="{$configs['site_title']|default:""}">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="site_description" class="control-label">{t}Site description{/t}</label>
                        <div class="controls">
                            <textarea id="site_description" name="site_description" class="input-xlarge" cols=50 rows=5>{$configs['site_description']|default:""}</textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="site_description" class="control-label">{t}Site keywords{/t}</label>
                        <div class="controls">
                            <textarea id="site_keywords" name="site_keywords" class="input-xlarge">{$configs['site_keywords']|default:""}</textarea>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>{t}Web Master Tools{/t}</legend>

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
                    <legend>{t}Opennemas settings{/t}</legend>

                    <div class="control-group">
                        <label for="site_language" class="control-label">{t}Language{/t}</label>
                        <div class="controls">
                            {html_options name=site_language options=$languages selected=$configs['site_language']}
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="time_zone" class="control-label">{t}Time Zone{/t}</label>
                        <div class="controls">
                            {html_options name=time_zone options=$timezones selected=$configs['time_zone']}
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="time_zone" class="control-label">{t}Refresh page interval{/t}</label>
                        <div class="controls">
                            <input type="number" id="refresh_interval" name="refresh_interval" value="{$configs['refresh_interval']|default:900}">
                            <small>(seconds)</small>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="time_zone" class="control-label">{t}Items per page{/t}</label>
                        <div class="controls">
                            <input type="number" id="items_per_page" name="items_per_page" value="{$configs['items_per_page']|default:20}">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="max_session_lifetime" class="control-label">{t}Max session lifetime{/t}</label>
                        <div class="controls">
                            <input type="number" id="max_session_lifetime" name="max_session_lifetime" value="{$configs['max_session_lifetime']|default:30}">
                            <small>(minutes)</small>
                        </div>
                    </div>
                </fieldset>
            </div>


            <div id="external" class="form-horizontal">
                <fieldset>
                    <legend>{t}Social networks{/t}</legend>

                    <div class="control-group">
                        <label for="facebook_page" class="control-label">{t}Facebook Page Url{/t}</label>
                        <div class="controls">
                            <input type="text" id="facebook_page" name="facebook_page" value="{$configs['facebook_page']|default:""}" class="input-xlarge">
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
                        <label for="twitter_page" class="control-label">{t}Twitter Page{/t}</label>
                        <div class="controls">
                            <input type="text" id="twitter_page" name="twitter_page" value="{$configs['twitter_page']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}If you also have a <b>twitter page</b>, add your profile name on the form. <br/>Default will be set with Opennemas.{/t}</div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>{t}Google Services{/t}</legend>

                    <div class="control-group">
                        <label for="google_maps_api_key" class="control-label">{t}Google Maps API key{/t}</label>
                        <div class="controls">
                            <input type="text" id="google_maps_api_key" name="google_maps_api_key" value="{$configs['google_maps_api_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Google <strong>Maps</strong> API Key from <a href="http://code.google.com/apis/maps/signup.html"  target="_blank">Google maps sign up website</a>.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="google_custom_search_api_key" class="control-label">{t}Google Search API key:{/t}</label>
                        <div class="controls">
                            <input type="text" id="google_custom_search_api_key" name="google_custom_search_api_key" value="{$configs['google_custom_search_api_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Google <strong>Search</strong> API Key from <a href="http://www.google.com/cse/manage/create"  target="_blank">Google Search sign up website</a>.{/t}</div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>{t}Facebook{/t}</legend>

                    <div class="control-group">
                        <label for="facebook_api_key" class="control-label">{t}APP key{/t}</label>
                        <div class="controls">
                            <input type="text" id="facebook_api_key" name="facebook[api_key]" value="{$configs['facebook']['api_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps">Facebook Developers website</a>.{/t}</div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="facebook_secret_key" class="control-label">{t}Secret key{/t}</label>
                        <div class="controls">
                            <input type="text" id="facebook_secret_key" name="facebook[secret_key]" value="{$configs['facebook']['secret_key']|default:""}" class="input-xlarge">
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>{t}Google Analytics Statistics{/t}</legend>

                    <div class="control-group">
                        <label for="google_analytics_api_key" class="control-label">{t}API key{/t}</label>
                        <div class="controls">
                            <input type="text" id="google_analytics_api_key" name="google_analytics[api_key]" value="{$configs['google_analytics']['api_key']|default:""}" class="input-xlarge">
                            <div class="help-block">{t escape=off}You can get your Google Analytics Site ID from <a href="https://www.google.com/analytics/">GAnalytics site</a> under the General Overview list (should be something like UA-546457-3).{/t}</div>
                        </div>
                    </div>
                </fieldset>
                {acl isAllowed="ONLY_MASTERS"}
                <fieldset>
                    <legend>{t}Piwik Statistics{/t}</legend>

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
                <fieldset>
                    <legend>{t}Recaptcha{/t}</legend>

                    <div class="control-group">
                        <label for="recaptcha_public_key" class="control-label">{t}Public key{/t}</label>
                        <div class="controls">
                            <input type="text" id="recaptcha_public_key" name="recaptcha[public_key]" value="{$configs['recaptcha']['public_key']|default:""}" class="input-xlarge">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="piwik_token_auth" class="control-label">{t}Private key{/t}</label>
                        <div class="controls">
                            <input type="text" id="recaptcha_private_key" name="recaptcha[private_key]" value="{$configs['recaptcha']['private_key']|default:""}" class="input-xlarge">
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

        <input type="hidden" id="action" name="action" value="save"/>

    </form>
</div>
{/block}