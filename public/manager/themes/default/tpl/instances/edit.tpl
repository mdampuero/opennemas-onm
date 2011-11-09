{extends file="base/base.tpl"}

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
table.adminform {
    padding:10px;
    width:100%;
}
</style>
{/block}

{block name="footer-js"}
    
{/block}

{block name="content" append}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{if $smarty.request.action eq "new"}{t}Creating new intance{/t}{else}{t 1=$instance->name}Editing instance «%1»{/t}{/if}</h2>
        </div>
        <ul class="old-button">
            <li>
                <a href="?action=list" class="admin_add" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" /><br />
                    {t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    {render_messages}
</div><!-- / -->
<form action="{$smarty.server.PHP_SELF}" method="post" name="formulario" id="formulario">
    <div class="wrapper-content">
        <input type="hidden" id="id" name="id" value="{$instance->id|default:""}" />
        <input type="hidden" id="action" name="action" value="save" />


        <ul id="tabs">
            <li><a href="#general">{t}General{/t}</a> </li>
            <li><a href="#general-information">{t}Information{/t}</a> </li>
            <li><a href="#internals">{t}Internals{/t}</a> </li>
            <li><a href="#database">{t}Database{/t}</a></li>
            <li><a href="#mail">{t}Mail{/t}</a></li>
            <li><a href="#log">{t}Log{/t}</a></li>
            <li><a href="#external">{t}External Services{/t}</a></li>
            <li><a href="#modules">{t}Modules{/t}</a></li>
        </ul>
 
        <div id="general" class="panel">
            <table>
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="name">{t}Instance name{/t}:</label>
                        </th>
                        <td>
                            <input type="text" id="name" name="site_name" value="{$instance->name}">
                        </td>
                        <td>
                            <span class="default-value">{t}(Human readable name){/t}</span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="activated">{t}Activated{/t}:</label>
                        </th>
                        <td>
                            <select name="activated" id="activated">
                                <option value="1" {if isset($instance) && $instance->activated == 1}selected="selected"{/if}>{t}Yes{/t}</option>
                                <option value="0" {if isset($instance) && $instance->activated == 0}selected="selected"{/if}>{t}No{/t}</option>
                            </select>
                        </td>
                        <td>
    
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="domains">{t}Domains:{/t}</label>
                        </th>
                        <td>
                            <textarea id="domains" name="domains" cols="50" rows="5">{$instance->domains}</textarea>
                        </td>
                        <td>
                            <div class="help-block margin-left-1 red">
                                <div class="title"><h4>List of domains</h4></div>
                                <div class="content">{t escape=off}List of domains separated by commas. You can use wildcards, i.e. *.example.com{/t}</div>
                            </div>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="contact">{t}User contact IP:{/t}</label>
                        </th>
                        <td>
                            <input type="text" readonly id="contact_IP" name="contact_IP" value="{$configs['contact_IP']|default:""}">
                        </td>
                        <td>

                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row">
                            <label for="site_title">{t}Created:{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="site_created" name="site_created" value="{$configs['site_created']|default:$smarty.now|date_format:"%d-%m-%Y"}">
                        </td>
                        <td>

                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="contact">{t}User name:{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="contact_name" name="contact_name" value="{$configs['contact_name']|default:""}">
                        </td>
                        <td>

                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="contact">{t}User password:{/t}</label>
                        </th>
                        <td>
                            <input type="password" id="password" name="password" value="" {if isset($instance)}readonly="readonly"{/if}>
                        </td>
                        <td>

                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="contact">{t}User contact mail:{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="contact_mail" name="contact_mail" value="{$configs['contact_mail']|default:""}">
                        </td>
                        <td>

                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        <div id="general-information" class="panel">
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
                            <textarea id="site_keywords" name="site_keywords" cols="50" rows="5">{$configs['site_keywords']|default:""}</textarea>
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="site_title">{t}Site agency:{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="site_agency" name="site_agency" value="{$configs['site_agency']|default:""}">
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
        <div class="panel" id="internals">
            <table>
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="internal_name">{t}Internal name:{/t}</label>
                        </th>
                        <td>
                            <input type="text" id="internal_name" name="internal_name" value="{$instance->internal_name}" {if isset($instance)}readonly="readonly"{/if}>
                                
                        </td>
                        <td>
                            <div class="help-block margin-left-1 red">
                                <div class="title"><h4>Alphanumeric, without spaces.</h4></div>
                                <div class="content">{t escape=off}Used for cache prefixes and internal ONM operations{/t}</div>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="template_user">{t}Template:{/t}</label>
                        </th>
                        <td>
                            {html_options name="settings[TEMPLATE_USER]" options=$templates selected=$instance->settings['TEMPLATE_USER']}
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="media_url">{t}External media url:{/t}</label>
                        </th>
                        <td><input name="settings[MEDIA_URL]" value="{$instance->settings['MEDIA_URL']}" type="text" /></td>
                    </tr>

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
        <div class="panel" id="database">
            <table>
                <tbody>
                    <tr>
                        <th>
                            <label for="settings_bd_type">{t}Database Type:{/t}</label>
                        </th>
                        <td colspan=2>
                            <select name="settings[BD_TYPE]" id="settings_bd_type">
                                <option value="mysqli"{if $instance->settings["BD_TYPE"] == "mysqli"} selected="selected"{/if}>{t}Mysql/Percona{/t}</option>
                                <option value="mysql"{if $instance->settings["BD_TYPE"] == "mysql"} selected="selected"{/if} >{t}Mysql/Percona old driver{/t}</option>
                                <option value="postgres8"{if $instance->settings["BD_TYPE"] == "postgres8"} selected="selected"{/if}>{t}Postgres{/t}</option>
                                <option value="oracle"{if $instance->settings["BD_TYPE"] == "oracle"} selected="selected"{/if}>{t}Oracle{/t}</option>
                            </select>
                        </td>
                        <td rowspan="5" valign=top>
                            <div class="help-block margin-left-1 red">
                                <div class="title"><h4>Database connection.</h4></div>
                                <div class="content">{t escape=off}Please doble check your database settings, those will be used as the default Database data access for this instance.{/t}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="settings_bd_host">{t}Database server:{/t}</label>
                        </th>
                        <td>
                            <input name="settings[BD_HOST]" value="{$instance->settings['BD_HOST']|default:$defaultDatabaseAuth['BD_HOST']}" type="text"></input>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="settings_bd_database">{t}Database name:{/t}</label>
                        </th>
                        <td><input name="settings[BD_DATABASE]" id="settings_bd_database" value="{$instance->settings['BD_DATABASE']|default:$defaultDatabaseAuth['BD_DATABASE']}" type="text" /></td>
                    </tr>
                    <tr>
                        <th>
                            <label for="settings_bd_user">{t}Database user:{/t}</label>
                        </th>
                        <td>
                            <input name="settings[BD_USER]" id="settings_bd_user" value="{$instance->settings['BD_USER']|default:$defaultDatabaseAuth['BD_USER']}" type="text" readonly="readonly"></input>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="settings_bd_password">{t}Database password:{/t}</label>
                        </th>
                        <td><input name="settings[BD_PASS]" id="settings_bd_password" value="{$instance->settings['BD_PASS']|default:$defaultDatabaseAuth['BD_PASS']}" type="password" readonly="readonly"></input></td>
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
                                    <div class="title"><h4>{t}Get API keys{/t}</h4></div>
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
                                    <div class="title"><h4>{t}Get API keys{/t}</h4></div>
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
                                    <div class="title"><h4>{t}Get API keys{/t}</h4></div>
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
                                    <div class="title"><h4>{t}Get API keys{/t}</h4></div>
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
                                    <div class="title"><h4>{t}Get API keys{/t}</h4></div>
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
                                <div class="title"><h4>{t}Dragons Ahead!{/t}</h4></div>
                                <div class="content">{t escape=off}This section is experimental and could not work as espected{/t}</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button red">{t}Save{/t}</button>
            </div>
        </div>
    </div>
</form>
{/block}
