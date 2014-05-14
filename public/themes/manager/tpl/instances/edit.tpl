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
{script_tag src="/jquery/jquery-ui-timepicker-addon.js" common=1}
<script>
jQuery(document).ready(function($) {
    $('#instance-edit').tabs();

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    jQuery('#domain_expire').datepicker({
        hourGrid: 4,
        showAnim: 'fadeIn',
        dateFormat: 'yy-mm-dd',
    });
});
</script>
{/block}

{block name="content" append}
<form action="{if !isset($instance->id)}{url name=manager_instance_create}{else}{url name=manager_instance_update id=$instance->id}{/if}" method="post" name="formulario" id="formulario">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title">
                <h2>{if !isset($instance->id)}{t}Creating new intance{/t}{else}{t 1=$instance->name}Editing instance "%1"{/t}{/if}</h2>
            </div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img border="0" src="{$params.COMMON_ASSET_DIR}images/save.png"><br />
                        {t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=manager_instances}" class="admin_add" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
                        <img border="0" src="{$params.COMMON_ASSET_DIR}images/previous.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" /><br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        {render_messages}
    </div><!-- / -->
    <div class="wrapper-content">

        <div id="instance-edit" class="tabs">
            <ul>
                <li><a href="#general">{t}General{/t}</a> </li>
                <li><a href="#general-information">{t}Information{/t}</a> </li>
                <li><a href="#internals">{t}Internals{/t}</a> </li>
                <li><a href="#database">{t}Database{/t}</a></li>
                <li><a href="#mail">{t}Newsletter{/t}</a></li>
                <li><a href="#external">{t}External Services{/t}</a></li>
                <li><a href="#modules">{t}Modules{/t}</a></li>
            </ul>
            <div id="general">
                <table>
                    <tbody>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="name" class="control-label">{t}Instance name{/t}:</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="name" name="site_name" value="{$instance->name}" required="required">
                            </td>
                            <td>
                                <span class="default-value">{t}(Human readable name){/t}</span>
                            </td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="activated" class="control-label">{t}Activated{/t}:</label>
                            </th>
                            <td class="controls">
                                <select name="activated" id="activated">
                                    <option value="1" {if isset($instance) && $instance->activated == 1}selected="selected"{/if}>{t}Yes{/t}</option>
                                    <option value="0" {if isset($instance) && $instance->activated == 0}selected="selected"{/if}>{t}No{/t}</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="activated" class="control-label">{t}Max users{/t}:</label>
                            </th>
                            <td class="controls">
                                <input type="number" required="required" name="max_users" value="{$configs['max_users']|default:'0'}">
                            </td>
                            <td></td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="domains" class="control-label">{t}Domains:{/t}</label>
                            </th>
                            <td class="controls">
                                <textarea id="domains" name="domains" cols="50" rows="5" required="required">{$instance->domains}</textarea>
                            </td>
                            <td>
                                <div class="onm-help-block margin-left-1 red">
                                    <div class="title"><h4>List of domains</h4></div>
                                    <div class="content">{t escape=off}List of domains separated by commas. You can use wildcards, i.e. *.example.com{/t}</div>
                                </div>
                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="domain_expire" class="control-label">{t}Domain expire date:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="datetime" id="domain_expire" name="domain_expire" value="{$configs['domain_expire']|default:""}">
                            </td>
                            <td>

                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="contact_IP" class="control-label">{t}User contact IP:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" readonly id="contact_IP" name="contact_IP" value="{$configs['contact_IP']|default:""}">
                            </td>
                            <td>

                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="site_created" class="control-label">{t}Created:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="site_created" name="site_created" value="{if $configs['site_created']}{$configs['site_created']}{else}{$smarty.now|date_format:"%Y-%m-%d - %H:%M:%S"}{/if}">
                            </td>
                            <td>

                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="contact_name" class="control-label">{t}User name:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="contact_name" name="contact_name" value="{$configs['contact_name']|default:""}" required="required">
                            </td>
                            <td>

                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="password" class="control-label">{t}User password:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="password" id="password" class="{if !isset($instance)}required validate-password required="required"{/if}" name="password" value="" {if isset($instance)}readonly="readonly"{/if}>
                            </td>
                            <td>
                                <span class="default-value">{t}(The password must have between 8 and 16 characters){/t}</span>
                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="contact_mail" class="control-label">{t}User contact mail:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="email" id="contact_mail" name="contact_mail" value="{$configs['contact_mail']|default:""}" required="required">
                            </td>
                            <td>

                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div id="general-information">
                <table>
                    <tbody>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="site_title" class="control-label">{t}Site title:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="site_title" name="site_title" value="{$configs['site_title']|default:""}" required="required">
                            </td>
                            <td>

                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="site_description" class="control-label">{t}Site description:{/t}</label>
                            </th>
                            <td class="controls">
                                <textarea id="site_description" name="site_description" cols="50" rows="7">{$configs['site_description']|default:""}</textarea>
                            </td>
                            <td>

                            </td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="site_keywords" class="control-label">{t}Site keywords:{/t}</label>
                            </th>
                            <td class="controls">
                                <textarea id="site_keywords" name="site_keywords" cols="50" rows="5">{$configs['site_keywords']|default:""}</textarea>
                            </td>
                            <td>

                            </td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="site_agency" class="control-label">{t}Site agency:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="site_agency" name="site_agency" value="{$configs['site_agency']|default:""}">
                            </td>
                            <td>

                            </td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="site_title" class="control-label">{t}Time Zone:{/t}</label>
                            </th>
                            <td class="controls">
                                {html_options name=time_zone options=$timezones selected=$configs['time_zone']}
                            </td>
                            <td>

                            </td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="site_title" class="control-label">{t}Language{/t}</label>
                            </th>
                            <td class="controls">
                                {html_options name=site_language options=$languages selected=$configs['site_language']}
                            </td>
                            <td>

                            </td>
                        </tr>


                    </tbody>
                </table>
            </div>
            <div id="internals">
                <table>
                    <tbody>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="internal_name" class="control-label">{t}Internal name:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="internal_name" name="internal_name" value="{$instance->internal_name}" {if isset($instance)}readonly="readonly"{/if}>

                            </td>
                            <td>
                                <div class="onm-help-block margin-left-1 red">
                                    <div class="title"><h4>Alphanumeric, without spaces.</h4></div>
                                    <div class="content">{t escape=off}Used for cache prefixes and internal ONM operations{/t}</div>
                                </div>
                            </td>
                        </tr>

                        <tr class="control-group">
                            <th scope="row">
                                <label for="template_user" class="control-label">{t}Template:{/t}</label>
                            </th>
                            <td class="controls">
                                {html_options name="settings[TEMPLATE_USER]" options=$templates selected=$instance->settings['TEMPLATE_USER']}
                            </td>
                        </tr>

                        <tr class="control-group">
                            <th scope="row">
                                <label for="media_url" class="control-label">{t}External media url:{/t}</label>
                            </th>
                            <td class="controls">
                                <input name="settings[MEDIA_URL]" value="{$instance->settings['MEDIA_URL']}" type="text" />
                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="advertisements_enabled">{t}Enable advertisements:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="checkbox" id="advertisements_enabled" name="advertisements_enabled" {if ($configs['advertisements_enabled'])}checked{/if} />
                                <span class="default-value">{t}Default: true{/t}</span>
                            </td>
                            <td valign="top">

                            </td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="items_per_page" class="control-label">{t}Items per page:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="items_per_page" name="items_per_page" value="{$configs['items_per_page']|default:20}">
                            </td>
                            <td valign="top">
                                <span class="default-value">{t}Default: 20 elements{/t}</span>
                            </td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="refresh_interval" class="control-label">{t}Refresh page every (secs):{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="refresh_interval" name="refresh_interval" value="{$configs['refresh_interval']|default:900}">
                            </td>
                            <td valign="top">
                                <span class="default-value">{t}Default: 900 secs{/t}</span>
                            </td>
                        </tr>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="pass_level" class="control-label">{t}Minimum password level{/t}:</label>
                            </th>
                            <td class="controls">
                                <select name="pass_level" id="pass_level">
                                    <option value="-1" {if !isset($configs['pass_level']) || $configs['pass_level'] eq -1}selected="selected"{/if}>{t}Default{/t}</option>
                                    <option value="0" {if $configs['pass_level'] eq "0"}selected="selected"{/if}>{t}Weak{/t}</option>
                                    <option value="1" {if $configs['pass_level'] eq "1"}selected="selected"{/if}>{t}Good{/t}</option>
                                    <option value="2" {if $configs['pass_level'] eq "2"}selected="selected"{/if}>{t}Strong{/t}</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="database">
                <table>
                    <tbody>
                        <tr class="control-group">
                            <th>
                                <label for="settings_bd_type" class="control-label">{t}Database Type:{/t}</label>
                            </th>
                            <td colspan=2 class="controls">
                                <select name="settings[BD_TYPE]" id="settings_bd_type">
                                    <option value="mysqli"{if $instance->settings["BD_TYPE"] == "mysqli"} selected="selected"{/if}>{t}Mysql/Percona{/t}</option>
                                    <option value="mysql"{if $instance->settings["BD_TYPE"] == "mysql"} selected="selected"{/if} >{t}Mysql/Percona old driver{/t}</option>
                                    <option value="postgres8"{if $instance->settings["BD_TYPE"] == "postgres8"} selected="selected"{/if}>{t}Postgres{/t}</option>
                                    <option value="oracle"{if $instance->settings["BD_TYPE"] == "oracle"} selected="selected"{/if}>{t}Oracle{/t}</option>
                                </select>
                            </td>
                            <td rowspan="5" valign=top>
                                <div class="onm-help-block margin-left-1 red">
                                    <div class="title"><h4>Database connection.</h4></div>
                                    <div class="content">{t escape=off}Please doble check your database settings, those will be used as the default Database data access for this instance.{/t}</div>
                                </div>
                            </td>
                        </tr>
                        <tr class="control-group">
                            <th>
                                <label for="settings_bd_host" class="control-label">{t}Database server:{/t}</label>
                            </th>
                            <td class="controls">
                                <input name="settings[BD_HOST]" value="{$instance->settings['BD_HOST']|default:""}" type="text"></input>
                            </td>
                        </tr>
                        <tr class="control-group">
                            <th>
                                <label for="settings_bd_database" class="control-label">{t}Database name:{/t}</label>
                            </th>
                            <td class="controls">
                                <input name="settings[BD_DATABASE]" id="settings_bd_database" value="{$instance->settings['BD_DATABASE']|default:""}" type="text" />
                            </td>
                        </tr>
                        <tr class="control-group">
                            <th>
                                <label for="settings_bd_user" class="control-label">{t}Database user:{/t}</label>
                            </th>
                            <td class="controls">
                                <input name="settings[BD_USER]" id="settings_bd_user" value="{$instance->settings['BD_USER']|default:""}" type="text" readonly="readonly"></input>
                            </td>
                        </tr>
                        <tr class="control-group">
                            <th>
                                <label for="settings_bd_password" class="control-label">{t}Database password:{/t}</label>
                            </th>
                            <td class="controls">
                                <input name="settings[BD_PASS]" id="settings_bd_password" value="{$instance->settings['BD_PASS']|default:""}" type="password" readonly="readonly"></input>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

             <div id="mail">
                <fieldset>
                    <legend>{t}Newsletter{/t}</legend>
                    <table>
                    <tbody>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="max_mailing" class="control-label">{t}Num Max emails sent by month{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="max_mailing" name="max_mailing" value="{$configs['max_mailing']|default:'12000'}">
                            </td>
                            <td>

                            </td>
                        </tr>

                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="last_invoice" class="control-label">{t}Last invoice date:{/t}</label>
                            </th>
                            <td class="controls">
                                <input type="text" id="last_invoice" name="last_invoice" value="{if $configs['last_invoice']}{$configs['last_invoice']}{else} {/if}">
                            </td>
                            <td>

                            </td>
                        </tr>
                    </tbody>
                    </table>
                </fieldset>
            </div>

            <div id="external">
                <fieldset>
                    <legend>{t}Google Services{/t}</legend>
                    <table>
                        <tbody>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="google_maps_api_key" class="control-label">{t}Google Maps API key:{/t}</label>
                                </th>
                                <td class="controls">
                                    <input type="text" id="google_maps_api_key" name="google_maps_api_key" value="{$configs['google_maps_api_key']|default:""}">
                                </td>
                                <td rowspan=2 valign="top">
                                    <div class="onm-help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your Google Maps API Key from <a href="http://code.google.com/intl/gl-GL/apis/maps/signup.html">Google maps sign up website</a>.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="google_custom_search_api_key" class="control-label">{t}Google Search API key:{/t}</label>
                                </th>
                                <td class="controls">
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
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="facebook_api_key" class="control-label">{t}APP key:{/t}</label>
                                </th>
                                <td class="controls">
                                    <input type="text" id="facebook_api_key" name="facebook[api_key]" value="{$configs['facebook']['api_key']|default:""}">
                                </td>
                                <td rowspan=2>
                                    <div class="onm-help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps">Facebook Developers website</a>.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="facebook_secret_key" class="control-label">{t}Secret key:{/t}</label>
                                </th>
                                <td class="controls">
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
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="google_analytics_api_key" class="control-label">{t}API key:{/t}</label>
                                </th>
                                <td class="controls">
                                    <input type="text" id="google_analytics_api_key" name="google_analytics[api_key]" value="{$configs['google_analytics']['api_key']|default:""}">
                                </td>
                                <td rowspan=2 valign="top">
                                    <div class="onm-help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your Google Analytics Site ID from <a href="https://www.google.com/analytics/">GAnalytics site</a> under the General Overview list (should be something like UA-546457-3) you can left blank the base domain field.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="google_analytics_base_domain" class="control-label">{t}Base domain:{/t}</label>
                                </th>
                                <td class="controls">
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
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="piwik_page_id" class="control-label">{t}Page ID:{/t}</label>
                                </th>
                                <td class="controls">
                                    <input type="text" id="piwik_page_id" name="piwik[page_id]" value="{$configs['piwik']['page_id']|default:""}">
                                </td>
                                <td rowspan=2 valign="top">
                                    <div class="onm-help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your Piwik Site information from <a href="https://piwik.openhost.es/admin">our Piwik server</a>.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="piwik_server_url" class="control-label">{t}Private key:{/t}</label>
                                </th>
                                <td class="controls">
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
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="recaptcha_public_key" class="control-label">{t}Public Key:{/t}</label>
                                </th>
                                <td class="controls">
                                    <input type="text" id="recaptcha_public_key" name="recaptcha[public_key]" value="{$configs['recaptcha']['public_key']|default:""}">
                                </td>
                                <td rowspan=2 valign="top">
                                    <div class="onm-help-block margin-left-1">
                                        <div class="title"><h4>{t}Get API keys{/t}</h4></div>
                                        <div class="content">{t escape=off}You can get your recaptcha API Keys from <a href="https://www.google.com/recaptcha/admin/create">reCATPCHA website</a>.{/t}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="recaptcha_private_key" class="control-label">{t}Private key:{/t}</label>
                                </th>
                                <td class="controls">
                                    <input type="text" id="recaptcha_private_key" name="recaptcha[private_key]" value="{$configs['recaptcha']['private_key']|default:""}">
                                </td>
                            </tr>

                        </tbody>
                    </table>

                </fieldset>
            </div>


            <div id="modules">
               <table>
                    <tbody>
                        <tr valign="top" class="control-group">
                            <th scope="row">
                                <label for="mail_server" class="control-label">{t}Activated modules{/t}</label>
                            </th>
                            <td class="form-inline" class="controls">
                                {html_checkboxes name='activated_modules' values=array_keys($available_modules) output=$available_modules selected=$configs['activated_modules']  separator='<br />'}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
{/block}
