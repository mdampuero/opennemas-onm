{extends file="base/base.tpl"}

{block name="header-css" append}
<style type="text/css">
.form-horizontal .control-group {
    margin-bottom: 0px;
}
.form-horizontal input+.help-block, .form-horizontal select+.help-block, .form-horizontal textarea+.help-block, .form-horizontal .uneditable-input+.help-block, .form-horizontal .input-prepend+.help-block, .form-horizontal .input-append+.help-block {
    margin-top:0px;
    margin-bottom:5px;
}
/* Sidenav for Docs
-------------------------------------------------- */
.sidebar {
    width: 228px;
    margin: 30px 0 0;
    padding: 0;
    background-color: #fff;
    border-radius:6px ;
    box-shadow: 0 1px 4px rgba(0,0,0,.065);
    position:fixed;
    top:100px;
}
.sidebar > li > a,
.sidebar .instance-summary {
    display: block;
    width: 190px \9;
    margin: 0 0 -1px;
    padding: 8px 14px;
    border: 1px solid #e5e5e5;
}
.sidebar > li:first-child > a,
.sidebar .instance-summary {
    border-radius: 6px 6px 0 0;
}
.sidebar > li:last-child > a {
    border-radius: 0 0 6px 6px;
}
.sidebar > .active > a {
    position: relative;
    z-index: 2;
    padding: 9px 15px;
    border: 0;
    text-shadow: 0 1px 0 rgba(0,0,0,.15);
    box-shadow: inset 1px 0 0 rgba(0,0,0,.1), inset -1px 0 0 rgba(0,0,0,.1);
    color:#0088cc;
    font-size:14px;
}
/* Chevrons */
.sidebar .icon-chevron-right {
    float: right;
    margin-top: 2px;
    margin-right: -6px;
    opacity: .25;
}
.sidebar > li > a:hover {
    background-color: #f5f5f5;
}
.sidebar a:hover .icon-chevron-right {
  opacity: .5;
}
.sidebar .active .icon-chevron-right,
.sidebar .active a:hover .icon-chevron-right {
  background-image: url(../img/glyphicons-halflings-white.png);
  opacity: 1;
}
.sidebar.affix {
  top: 40px;
}
.sidebar.affix-bottom {
  position: absolute;
  top: auto;
  bottom: 270px;
}

.settings {
    padding-left:300px;
}
.settings > div:first-child {
    padding-top:20px;
}
.settings > div {
    padding-top:50px;
}
.module_activated {
    border:1px solid #eee;
    display:inline-block;
    margin-right:5px;
    padding:2px 4px;
    line-height:1.7em;
}
</style>
{/block}

{block name="footer-js"}
{script_tag src="/jquery/jquery-ui-timepicker-addon.js" common=1}
<script>
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    jQuery('#domain_expire').datepicker({
        hourGrid: 4,
        showAnim: 'fadeIn',
        dateFormat: 'yy-mm-dd',
    });

    $('.affix').affix()
});
</script>
{/block}

{block name="content" append}
<form action="{if !isset($instance->id)}{url name=manager_instance_create}{else}{url name=manager_instance_update id=$instance->id}{/if}" method="post" name="formulario" id="formulario"  data-spy="scroll" data-target=".sidebar">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title">
                <h4>{if !isset($instance->id)}{t}Creating new intance{/t}{else}{t}Editing instance {/t}{/if}</h4>
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

        <div id="instance-edit" class="row ">
            <div class="span3">
                <ul class="nav nav-list sidebar" data-spy="affix" data-offset-top="100">
                    <li class="instance-summary">
                        <h4>{$instance->name}</h4>
                        <p><strong>Media size:</strong> {$size} Mb</p>
                        <p><strong>Owner mail:</strong> {$configs['contact_mail']}</p>
                        <p><strong>Created at:</strong> {$configs['site_created']}</p>
                    </li>
                    <li><a href="#general">{t}General information{/t} <i class="icon-chevron-right"></i></a></li>
                    <li><a href="#domains" title="{t}Domains{/t}">{t}Domains{/t} <i class="icon-chevron-right"></i></a></li>
                    <li><a href="#internals">{t}Internal information{/t} <i class="icon-chevron-right"></i></a> </li>
                    <li><a href="#external">{t}External Services{/t} <i class="icon-chevron-right"></i></a></li>
                    <li><a href="#modules">{t}Modules{/t} <i class="icon-chevron-right"></i></a></li>
                    <li><a href="#owner-information" title="{t}Owner information{/t}">{t}Owner information{/t} <i class="icon-chevron-right"></i></a></li>
                </ul>
            </div>
            <div class="span9 settings form-horizontal">
                <!-- <div id="summary">
                    <h3>{$instance->name}</h3>
                    <p>
                        <strong>Modules activated: ({count($configs['activated_modules'])})</strong><br>
                        {foreach $configs['activated_modules'] as $activated_module}
                            <span class="module_activated">{$available_modules[$activated_module]}</span>
                        {/foreach}
                    </p>
                </div> -->
                <div id="general">
                    <h4>General information</h4>

                    <div class="control-group">
                        <label class="control-label">
                            <label for="name" class="control-label">{t}Site name{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="text" id="name" name="site_name" value="{$instance->name}" required="required">
                            <div class="help-block">
                                {t}(Human readable name){/t}
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="activated" class="control-label">{t}Activated{/t}</label>
                        </label>
                        <div class="controls">
                            <select name="activated" id="activated">
                                <option value="1" {if isset($instance) && $instance->activated == 1}selected="selected"{/if}>{t}Yes{/t}</option>
                                <option value="0" {if isset($instance) && $instance->activated == 0}selected="selected"{/if}>{t}No{/t}</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="template_user" class="control-label">{t}Template{/t}</label>
                        </label>
                        <div class="controls">
                            {html_options name="settings[TEMPLATE_USER]" options=$templates selected=$instance->settings['TEMPLATE_USER']}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="last_invoice" class="control-label">{t}Last invoice date{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="text" id="last_invoice" name="last_invoice" value="{if $configs['last_invoice']}{$configs['last_invoice']}{else} {/if}">
                        </div>
                    </div>
                </div>

                <div id="domains">
                    <h4>Domains</h4>

                    <div class="control-group">
                        <label class="control-label">
                            <label for="domains" class="control-label">{t}Domains:{/t}</label>
                        </label>
                        <div class="controls">
                            <textarea id="domains" name="domains" cols="50" rows="5" required="required">{$instance->domains}</textarea>
                            <div class="help-block">
                                {t escape=off}List of domains separated by commas. You can use wildcards, i.e. *.example.com{/t}
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">
                            <label for="domain_expire" class="control-label">{t}Domain expire date:{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="datetime" id="domain_expire" name="domain_expire" value="{$configs['domain_expire']|default:""}">
                        </div>
                    </div>
                </div>

                <div id="internals">
                    <h4>Internal information</h4>


                    <div class="control-group">
                        <label class="control-label">
                            <label for="internal_name" class="control-label">{t}Internal name{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="text" id="internal_name" name="internal_name" value="{$instance->internal_name}" {if isset($instance)}readonly="readonly"{/if}>
                                    <div class="help-block">{t}Alphanumeric, without spaces{/t}. {t escape=off}Used for cache prefixes and internal ONM operations{/t}</div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="settings_bd_database" class="control-label">{t}Database name{/t}</label>
                        </label>
                        <div class="controls">
                            <input name="settings[BD_DATABASE]" id="settings_bd_database" value="{$instance->settings['BD_DATABASE']|default:""}" type="text" />
                        </div>
                    </div>


                    <div class="control-group">
                        <label class="control-label">
                            <label for="pass_level" class="control-label">{t}Minimum password level{/t}</label>
                        </label>
                        <div class="controls">
                            <select name="pass_level" id="pass_level">
                                <option value="-1" {if !isset($configs['pass_level']) || $configs['pass_level'] eq -1}selected="selected"{/if}>{t}Default{/t}</option>
                                <option value="0" {if $configs['pass_level'] eq "0"}selected="selected"{/if}>{t}Weak{/t}</option>
                                <option value="1" {if $configs['pass_level'] eq "1"}selected="selected"{/if}>{t}Good{/t}</option>
                                <option value="2" {if $configs['pass_level'] eq "2"}selected="selected"{/if}>{t}Strong{/t}</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">
                            <label for="site_title" class="control-label">{t}Time Zone{/t}</label>
                        </label>
                        <div class="controls">
                            {html_options name=time_zone options=$timezones selected=$configs['time_zone']}
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">
                            <label for="site_title" class="control-label">{t}Language{/t}</label>
                        </label>
                        <div class="controls">
                            {html_options name=site_language options=$languages selected=$configs['site_language']}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="max_mailing" class="control-label">{t}Num Max emails sent by month{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="text" id="max_mailing" name="max_mailing" value="{$configs['max_mailing']|default:'12000'}">
                        </div>
                    </div>

                </div>

                <div id="external">
                    <h4>External services</h4>
                    <table>
                        <tbody>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="piwik_page_id" class="control-label">{t}Piwik Statistics{/t} - {t}Page ID:{/t}</label>
                                </th>
                                <td class="controls">
                                    <input type="text" id="piwik_page_id" name="piwik[page_id]" value="{$configs['piwik']['page_id']|default:""}">
                                    <div class="help-block">
                                        {t escape=off}You can get your Piwik Site information from <a href="https://piwik.openhost.es/admin">our Piwik server</a>.{/t}
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="piwik_server_url" class="control-label">{t}Piwik Statistics{/t} - {t}Server url{/t}</label>
                                </th>
                                <td class="controls">
                                    <input type="text" id="piwik_server_url" name="piwik[server_url]" value="{$configs['piwik']['server_url']|default:""}">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div id="modules">
                    <h4>Modules</h4>
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

                <div id="owner-information">
                    <h4>{t}Owner information{/t}</h4>

                    <div class="control-group">
                        <label class="control-label">
                            <label for="site_created" class="control-label">{t}Created{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="text" id="site_created" name="site_created" value="{if $configs['site_created']}{$configs['site_created']}{else}{$smarty.now|date_format:"%Y-%m-%d - %H:%M:%S"}{/if}">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="contact_name" class="control-label">{t}User name{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="text" id="contact_name" name="contact_name" value="{$configs['contact_name']|default:""}" required="required">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="password" class="control-label">{t}User password{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="password" id="password" class="{if !isset($instance)}required validate-password required="required"{/if}" name="password" value="" {if isset($instance)}readonly="readonly"{/if}>
                            <div class="help-block">
                                {t}(The password must have between 8 and 16 characters){/t}
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="contact_IP" class="control-label">{t}User contact IP{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="text" readonly id="contact_IP" name="contact_IP" value="{$configs['contact_IP']|default:""}">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <label for="contact_mail" class="control-label">{t}User contact mail:{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="email" id="contact_mail" name="contact_mail" value="{$configs['contact_mail']|default:""}" required="required">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
{/block}
