{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/css/colorpicker.css" basepath="/js/jquery/jquery_colorpicker/"}
    <style type="text/css">
    table th, table label {
        color: #888;
        text-shadow: white 0 1px 0;
        font-size: 13px;
    }
    th {
        vertical-align: top;
        text-align: left;
        padding: 10px;
        width: 200px;
        font-size: 13px;
    }
    label{
        font-weight:normal;
    }

    .awesome {
        border:0;
    }
    .default-value {
        display:inline;
        color:#666;
        margin-left:10px;
        vertical-align:middle
    }
    input[type="text"],
    td,
    textarea{
        width:400px;
        max-height:80%
    }
    tr {
        vertical-align:top;
    }
    .ui-widget-content a {
        color: #0B55C4 !important;
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

    <form action="{$smarty.server.SCRIPT_NAME}" enctype="multipart/form-data" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
        <div id="system-settings-tabbed" class="tabs">
            <ul>
                <li><a href="#general">{t}General{/t}</a></li>
                <li><a href="#seo">{t}SEO{/t}</a></li>
                <li><a href="#misc">{t}Opennemas Settings{/t}</a></li>
                <li><a href="#external">{t}External Services{/t}</a></li>
            </ul>

            <div id="general">
                <fieldset>
                    <legend>{t}Site options{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="site_name">{t}Site name:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="site_name" name="site_name" value="{$configs['site_name']|default:""}">
                                </td>
                                <td rowspan=7>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Basic parameters{/t}</h4></div>
                                        <div class="content">
                                            <dl>
                                                <dt><strong>{t}Site agency{/t}</strong></dt>
                                                <dd>{t}You can edit the site agency for the articles here. This will be displayed as your article agency{/t}</dd>
                                                <dt><strong>{t}Edit Site name{/t}</strong></dt>
                                                <dd>{t}You can change the name of your site here. This will be displayed as your site name{/t}</dd>
                                                <dt><strong>{t}Show logo and color in frontpages{/t}</strong></dt>
                                                <dd>{t}You can enable Logos and category colors for your site here.{/t}</dd>
                                                <dt><strong>{t}Edit Site color{/t}</strong></dt>
                                                <dd>
                                                    {t}You can edit the site color here.
                                                    This will change the color of the menu bars.
                                                    If you wanna change the categorys color,
                                                    go to the Category Manager and edit a category.{/t}
                                                </dd>
                                                <dt><strong>{t}Add a Logo for the site{/t}</strong></dt>
                                                <dd>{t}You can add an image for your site logo here.{/t}</dd>
                                                <dt><strong>{t}Edit your Site footer{/t}</strong></dt>
                                                <dd>{t}You can edit here the footer of the site.{/t}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="site_agency">{t}Site agency:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="site_agency" name="site_agency" value="{$configs['site_agency']|default:""}">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="site_color">{t}Site color:{/t}</label>
                                </th>
                                <td>
                                    <input readonly="readonly" type="text" class="colorpicker_input" id="site_color" name="site_color" value="{$configs['site_color']}">
                                    <div class="colopicker_viewer" style="background-color:#{$configs['site_color']}"></div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="section_settings[allowLogo]">{t}Show logo and color in frontpages:{/t}</label>
                                </th>
                                <td>
                                    <select name="section_settings[allowLogo]" id="section_settings[allowLogo]" onChange="toogleSiteLogo(this.value);">
                                        <option value="0">{t}No{/t}</option>
                                        <option value="1" {if $configs['section_settings']['allowLogo'] eq "1"} selected {/if}>{t}Yes{/t}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="site_logo_block" {if $configs['section_settings']['allowLogo'] eq "0"}style="display:none"{/if}>
                                <th scope="row">
                                    <label for="site_logo">{t}Site logo:{/t}</label>
                                </th>
                                <td>
                                    <input type="file" id="site_logo" name="site_logo">
                                </td>
                            </tr>
                            {if isset($configs['site_logo'])}
                            <tr>
                                <th scope="row">
                                    <label for="site_title">{t}Logo image:{/t}</label>
                                </th>
                                <td style="height:100px;" colspan=2>
                                    <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/sections/{$configs['site_logo']}">
                                </td>
                            </tr>
                            {/if}
                            <tr>
                                <th scope="row">
                                    <label for="site_footer">{t}Text in footer frontpages:{/t}</label>
<!--                                    <div id="toggle-btn" style="float:right;">
                                        <a title="Habilitar/Deshabilitar editor" data-selector-to-hide="site_footer" class="toggle-tinymce" href="#">
                                            <img alt="" src="{$params.IMAGE_DIR}users_edit.png"></a>
                                    </div>-->
                                </th>
                                <td>
                                    <textarea id="site_footer" name="site_footer" cols="50" rows="7">{$configs['site_footer']|default:""}</textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>

            <div id="seo">
                <fieldset>
                    <legend>{t}SEO options{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="site_title">{t}Site title:{/t}</label>
                                </th>
                                <td>
                                    <textarea id="site_title" name="site_title" cols="50" rows="7">{$configs['site_title']|default:""}</textarea>
                                </td>
                                <td>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Edit your Site title{/t}</h4></div>
                                        <div class="content">{t escape=off}You can edit here the site title. This one will be displayed on the browsers title bar.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="site_description">{t}Site description:{/t}</label>
                                </th>
                                <td>
                                    <textarea id="site_description" name="site_description" cols="50" rows="7">{$configs['site_description']|default:""}</textarea>
                                </td>
                                <td>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Edit your Site description{/t}</h4></div>
                                        <div class="content">{t escape=off}You can edit here the site description. This will be used on meta tag description.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="site_keywords">{t}Site keywords:{/t}</label>
                                </th>
                                <td>
                                    <textarea id="site_keywords" name="site_keywords" cols="50" rows="5">{$configs['site_keywords']|default:""}</textarea>
                                </td>
                                <td>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Edit your Site footer{/t}</h4></div>
                                        <div class="content">{t escape=off}You can edit here the site keywords. This will be used on meta tag keywords.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>{t}Web Master Tools{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="webmastertools_google">{t}Google Web Master Tools:{/t}</label>
                                </th>
                                <td>
                                    <input id="webmastertools_google" name="webmastertools_google" value="{$configs['webmastertools_google']|default:""}" />
                                </td>
                                <td rowspan=2>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="webmastertools_bing">{t}Bing Web Master Tools:{/t}</label>
                                </th>
                                <td>
                                    <input id="webmastertools_bing" name="webmastertools_bing" value="{$configs['webmastertools_bing']|default:""}" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div><!-- /seo -->

            <div id="misc">
                <fieldset>
                    <legend>{t}Opennemas settings{/t}</legend>
                   <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="site_title">{t}Language{/t}</label>
                                </th>
                                <td>
                                    {html_options name=site_language options=$languages selected=$configs['site_language']}
                                </td>
                                <td rowspan=4>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Basic parameters{/t}</h4></div>
                                        <div class="content">
                                            <dl>
                                                <dt><strong>{t}Seconds for refresh pages{/t}</strong></dt>
                                                <dd>{t}The default configuration in Opennemas for refreshing pages is 900 seconds{/t}</dd>
                                                <dt><strong>{t}Number items in admin lists{/t}</strong></dt>
                                                <dd>{t}Default: 20 elements{/t}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="site_title">{t}Time Zone:{/t}</label>
                                </th>
                                <td colspan="2">
                                    {html_options name=time_zone options=$timezones selected=$configs['time_zone']}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="refresh_interval">{t}Refresh page every (secs):{/t}</label>
                                </th>
                                <td colspan="2">
                                    <input type="number" id="refresh_interval" name="refresh_interval" value="{$configs['refresh_interval']|default:900}">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="items_per_page">{t}Items per page:{/t}</label>
                                </th>
                                <td>
                                    <input type="number" id="items_per_page" name="items_per_page" value="{$configs['items_per_page']|default:20}">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </fieldset>
            </div>


            <div id="external">
                <fieldset>
                    <legend>{t}Social networks{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="facebook_page">{t}Facebook Page Url:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="facebook_page" name="facebook_page" value="{$configs['facebook_page']|default:""}">
                                </td>
                                <td rowspan=3>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Customize Social Networks{/t}</h4></div>
                                        <div class="content">{t escape=off}If you have a <b>facebook page</b>, please complete the form with your facebook page url and Id.<br/>
                                                                If you also have a <b>twitter page</b>, add your profile name on the form. <br/>Default will be set with Opennemas.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="facebook_id">{t}Facebook Id:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="facebook_id" name="facebook_id" value="{$configs['facebook_id']|default:""}">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="twitter_page">{t}Twitter Page:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="twitter_page" name="twitter_page" value="{$configs['twitter_page']|default:""}">
                                </td>
                            </tr>

                        </tbody>
                    </table>

                </fieldset>

                <fieldset>
                    <legend>{t}Google Services{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="google_maps_api_key">{t}Google Maps API key:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="google_maps_api_key" name="google_maps_api_key" value="{$configs['google_maps_api_key']|default:""}">
                                </td>
                                <td rowspan=2>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">
                                            {t escape=off}You can get your Google <strong>Maps</strong> API Key from <a href="http://code.google.com/apis/maps/signup.html"  target="_blank">Google maps sign up website</a>.{/t}<br>
                                            {t escape=off}You can get your Google <strong>Search</strong> API Key from <a href="http://www.google.com/cse/manage/create"  target="_blank">Google Search sign up website</a>.{/t}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="google_custom_search_api_key">{t}Google Search API key:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="google_custom_search_api_key" name="google_custom_search_api_key" value="{$configs['google_custom_search_api_key']|default:""}">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>{t}Facebook{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="facebook_api_key">{t}APP key:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="facebook_api_key" name="facebook[api_key]" value="{$configs['facebook']['api_key']|default:""}">
                                </td>
                                <td rowspan=2>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps">Facebook Developers website</a>.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="facebook_secret_key">{t}Secret key:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="facebook_secret_key" name="facebook[secret_key]" value="{$configs['facebook']['secret_key']|default:""}">
                                </td>
                            </tr>

                        </tbody>
                    </table>

                </fieldset>

                <fieldset>
                    <legend>{t}Google Analytics Statistics{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="google_analytics_api_key">{t}API key:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="google_analytics_api_key" name="google_analytics[api_key]" value="{$configs['google_analytics']['api_key']|default:""}">
                                </td>
                                <td rowspan=2>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your Google Analytics Site ID from <a href="https://www.google.com/analytics/">GAnalytics site</a> under the General Overview list (should be something like UA-546457-3) you can left blank the base domain field.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="google_analytics_base_domain">{t}Base domain:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="google_analytics_base_domain" name="google_analytics[base_domain]" value="{$configs['google_analytics']['base_domain']|default:""}">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </fieldset>
                {acl isAllowed="ONLY_MASTERS"}
                <fieldset>
                    <legend>{t}Piwik Statistics{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="piwik_page_id">{t}Page ID:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="piwik_page_id" name="piwik[page_id]" value="{$configs['piwik']['page_id']|default:""}">
                                </td>
                                <td rowspan=2>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your Piwik Site information from <a href="https://piwik.openhost.es/admin">our Piwik server</a>.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="piwik_server_url">{t}Server URL:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="piwik_server_url" name="piwik[server_url]" value="{$configs['piwik']['server_url']|default:""}">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="piwik_token_auth">{t}User Token Auth:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="piwik_token_auth" name="piwik[token_auth]" value="{$configs['piwik']['token_auth']|default:""}">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </fieldset>
                {/acl}
                <fieldset>
                    <legend>{t}Recaptcha{/t}</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="recaptcha_public_key">{t}Public Key:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="recaptcha_public_key" name="recaptcha[public_key]" value="{$configs['recaptcha']['public_key']|default:""}">
                                </td>
                                <td rowspan=2>
                                    <div class="help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your recaptcha API Keys from <a href="https://www.google.com/recaptcha/admin/create">reCATPCHA website</a>.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="recaptcha_private_key">{t}Private key:{/t}</label>
                                </th>
                                <td>
                                    <input type="text" id="recaptcha_private_key" name="recaptcha[private_key]" value="{$configs['recaptcha']['private_key']|default:""}">
                                </td>
                            </tr>

                        </tbody>
                    </table>

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