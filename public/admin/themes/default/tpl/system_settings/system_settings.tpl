{extends file="base/admin.tpl"}

{block name="header-css" append}
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
.panel {
    background:White;
}
fieldset {
    border:none;
    border-top:1px solid #ccc;
}
legend {
    color:#666;
    text-transform:uppercase;
    font-size:13px;
    padding:0 10px;
}

.awesome {
    border:0;
}
.panel {
    margin:0;
}
.default-value {
    display:inline;
    color:#666;
    margin-left:10px;
    vertical-align:middle
}
input[type="text"],
textarea{
    width:400px;
    max-height:80%
}
</style>
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

    <form action="{$smarty.server.SCRIPT_NAME}" method="post" name="formulario" id="formulario" {$formAttrs}>


        <ul id="tabs">
            <li>
                <a href="#general">{t}General{/t}</a>
                <a href="#mail">{t}Mail{/t}</a>
                <a href="#log">{t}Log{/t}</a>
                <a href="#external">{t}External Services{/t}</a>
                <a href="#misc">{t}Miscelanous{/t}</a>
                <a href="#modules">{t}Modules{/t}</a>
            </li>
        </ul>

        <div id="general" class="panel">
            <table>
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="site_title">{t}Site title:{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="site_title" name="site_title" value="{$configs['site_title']|default:""}">
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="site_title">{t}Site description:{/t}</label>
                        </th>
                        <td>
                            <textarea id="site_description" name="site_description" cols="50" rows="7">{$configs['site_description']|default:""}</textarea>
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="site_title">{t}Site keywords:{/t}</label>
                        </th>
                        <td>
                            <textarea id="site_keywords" name="site_keywords" cols="50" rows="5">{$configs['site_description']|default:""}</textarea>
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="site_title">{t}Time Zone:{/t}</label>
                        </th>
                        <td>
                            {html_options name=time_zone options=$timezones selected=$configs['time_zone']}
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="site_title">{t}Language{/t}</label>
                        </th>
                        <td>
                            {html_options name=site_language options=$languages selected=$configs['site_language']}
                        </td>
                        <td>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="mail" class="panel">
           <table>
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="mail_server">{t}Mail server{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="mail_server" name="mail_server" value="{$configs['mail_server']|default:""}">
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="mail_username">{t}Username{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="mail_username" name="mail_username" value="{$configs['mail_username']|default:""}">
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="mail_password">{t}Password{/t}</label>
                        </th>
                        <td>
                            <input type="password" id="mail_password" name="mail_password" value="{$configs['mail_password']|default:""}">
                        </td>
                        <td>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="log" class="panel">
            <fieldset>
                <legend>{t}System log{/t}</legend>
                <table>
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="log_enabled">{t}Enable Log:{/t}</label>
                            </th>
                            <td>
                                <input type="checkbox" id="log_enabled" name="log_enabled" {if ($configs['log_enabled'])}checked{/if} />
                                <span class="default-value">{t}Default: true{/t}</span>
                            </td>
                        <td>

                        </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="site_title">{t}Log level:{/t}</label>
                            </th>
                            <td>
                                {html_options name=log_level options=$logLevels selected=$configs['log_level']}
                                <span class="default-value">{t}Default: true{/t}</span>
                            </td>
                        <td>

                        </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <fieldset>
                <legend>{t}Database log{/t}</legend>
                <table>
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="log_db_enabled">{t}Enable Log:{/t}</label>
                            </th>
                            <td>
                                <input type="checkbox" id="log_db_enabled" name="log_db_enabled" {if ($configs['log_db_enabled'])}checked{/if} />
                                <span class="default-value">{t}Default: false{/t}</span>
                            </td>
                        <td>

                        </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <div id="external" class="panel">
            <fieldset>
                <legend>{t}Google Services{/t}</legend>
                <table>
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="google_maps_api_key">{t}Google Maps API key:{/t}</label>
                            </th>
                            <td>
                                <input type="text" id="google_maps_api_key" name="google_maps_api_key" value="{$configs['google_maps_api_key']|default:""}">
                            </td>
                            <td rowspan=2 valign="top">
                                <div class="help-block margin-left-1">
                                    <div class="title"><h4>Get API keys</h4></div>
                                    <div class="content">{t escape=off}You can get your Google Maps API Key from <a href="http://code.google.com/intl/gl-GL/apis/maps/signup.html">Google maps sign up website</a>.{/t}</div>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
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
                        <tr valign="top">
                            <th scope="row">
                                <label for="facebook_api_key">{t}APP key:{/t}</label>
                            </th>
                            <td>
                                <input type="text" id="facebook_api_key" name="facebook[api_key]" value="{$configs['facebook']['api_key']|default:""}">
                            </td>
                            <td rowspan=2>
                                <div class="help-block margin-left-1">
                                    <div class="title"><h4>Get API keys</h4></div>
                                    <div class="content">{t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps">Facebook Developers website</a>.{/t}</div>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
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
                        <tr valign="top">
                            <th scope="row">
                                <label for="google_analytics_api_key">{t}API key:{/t}</label>
                            </th>
                            <td>
                                <input type="text" id="google_analytics_api_key" name="google_analytics[api_key]" value="{$configs['google_analytics']['api_key']|default:""}">
                            </td>
                            <td rowspan=2 valign="top">
                                <div class="help-block margin-left-1">
                                    <div class="title"><h4>Get API keys</h4></div>
                                    <div class="content">{t escape=off}You can get your Google Analytics Site ID from <a href="https://www.google.com/analytics/">GAnalytics site</a> under the General Overview list (should be something like UA-546457-3) you can left blank the base domain field.{/t}</div>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
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

            <fieldset>
                <legend>{t}Piwik Statistics{/t}</legend>
                <table>
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="piwik_page_id">{t}Page ID:{/t}</label>
                            </th>
                            <td>
                                <input type="text" id="piwik_page_id" name="piwik[page_id]" value="{$configs['piwik']['page_id']|default:""}">
                            </td>
                            <td rowspan=2 valign="top">
                                <div class="help-block margin-left-1">
                                    <div class="title"><h4>Get API keys</h4></div>
                                    <div class="content">{t escape=off}You can get your Piwik Site information from <a href="https://piwik.openhost.es/admin">our Piwik server</a>.{/t}</div>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="piwik_server_url">{t}Private key:{/t}</label>
                            </th>
                            <td>
                                <input type="text" id="piwik_server_url" name="piwik[server_url]" value="{$configs['piwik']['server_url']|default:""}">
                            </td>
                        </tr>

                    </tbody>
                </table>
            </fieldset>

            <fieldset>
                <legend>{t}Recaptcha{/t}</legend>
                <table>
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="recaptcha_public_key">{t}Public Key:{/t}</label>
                            </th>
                            <td>
                                <input type="text" id="recaptcha_public_key" name="recaptcha[public_key]" value="{$configs['recaptcha']['public_key']|default:""}">
                            </td>
                            <td rowspan=2 valign="top">
                                <div class="help-block margin-left-1">
                                    <div class="title"><h4>Get API keys</h4></div>
                                    <div class="content">{t escape=off}You can get your recaptcha API Keys from <a href="https://www.google.com/recaptcha/admin/create">reCATPCHA website</a>.{/t}</div>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
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

        <div id="misc" class="panel">
           <table>
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="advertisements_enabled">{t}Enable advertisements:{/t}</label>
                        </th>
                        <td>
                            <input type="checkbox" id="advertisements_enabled" name="advertisements_enabled" {if ($configs['advertisements_enabled'])}checked{/if} />
                            <span class="default-value">{t}Default: true{/t}</span>
                        </td>
                        <td valign="top">

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="items_per_page">{t}Items per page:{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="items_per_page" name="items_per_page" value="{$configs['items_per_page']|default:20}">
                            <span class="default-value">{t}Default: 20 elements{/t}</span>
                        </td>
                        <td valign="top">

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="refresh_interval">{t}Refresh page every (secs):{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="refresh_interval" name="refresh_interval" value="{$configs['refresh_interval']|default:900}">
                            <span class="default-value">{t}Default: 900 secs{/t}</span>
                        </td>
                        <td valign="top">

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="modules" class="panel">
           <table>
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="mail_server">{t}Activated modules{/t}</label>
                        </th>
                        <td>
                            {html_checkboxes name='activated_modules' values=$available_modules output=$available_modules selected=$configs['activated_modules']  separator='<br />'}
                        </td>
                        <td>
                            <div class="help-block warning margin-left-1">
                                <div class="title"><h4>Dragons Ahead!</h4></div>
                                <div class="content">{t escape=off}This section is experimental and could not work as espected{/t}</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
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
