{extends file="base/base.tpl"}

{block name="header-css" append}
{css_tag href="/manager.css" common=1}
{/block}

{block name="footer-js"}
{script_tag src="/jquery/jquery-ui-timepicker-addon.js" common=1}
{script_tag src="/jquery/jquery.multiselect.js" common=1}
<script>
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    jQuery('#domain_expire').datetimepicker({
        hourGrid: 4,
        showAnim: 'fadeIn',
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
        minuteGrid: 10
    });

    jQuery('#last_invoice').datetimepicker({
        hourGrid: 4,
        showAnim: 'fadeIn',
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
        minuteGrid: 10
    });

    $('.affix').affix();
    $('#activated_modules').twosidedmultiselect();

    $('.domain-list').on('click', '.domain .del', function(e, ui) {
        e.preventDefault();
        $(this).closest('.domain').remove();
    });
    $('.domain-list').on('click', '.add-domain a', function(e, ui) {
        e.preventDefault();
        console.log('add new')

        new_domain = $(this).closest('.domain-list').find('.domain:first').clone();
        new_domain.find('input').val('');
        new_domain.insertBefore('.add-domain');
    })
    $('.domain-list').on('click', '.visit-domain', function(e, ui) {
        e.preventDefault();

        url = $(this).closest('.domain').find('input').val();
        window.open('http://'+url, '_blank').focus();
    })
});
</script>
{/block}

{block name="content" append}
<form action="{if !isset($instance->id)}{url name=manager_instance_create}{else}{url name=manager_instance_update id=$instance->id}{/if}" method="post" name="formulario" id="formulario">
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

        <div id="instance-edit" class="row ">
            <div class="span3">
                <ul class="nav nav-list sidebar" data-spy="affix" data-offset-top="100">
                    <li class="instance-summary">
                        <h4>{$instance->name}</h4>

                    </li>
                    <li class="instance-summary">
                        <p><strong>Media size:</strong> {$size} Mb</p>
                        <p><strong>Owner mail:</strong> <a href="mailto:{$configs['contact_mail']}" title="Send email to the owner">{$configs['contact_mail']}</a></p>
                        <p><strong>Created at:</strong> {$configs['site_created']}</p>
                        <p><strong>Created from IP:</strong> {$configs['contact_IP']}</p>
                        <p>
                            <label>
                            <input type="checkbox" class="ios-switch green tinyswitch"  /><div><div></div></div>
                            </label>

                            <label>
                                <strong>Activated</strong>
                                <input type="checkbox" class="ios-switch green bigswitch" name="activated" id="activated" value=1 {if $instance->activated == 1}checked{/if} />
                                <div><div></div></div>
                            </label>
                        </p>
                    </li>
                    <li><a href="#general">{t}General information{/t} <i class="icon-chevron-right"></i></a></li>
                    <li><a href="#domains" title="{t}Domains{/t}">{t}Domains{/t} <i class="icon-chevron-right"></i></a></li>
                    <li><a href="#modules">{t}Modules{/t} <i class="icon-chevron-right"></i></a></li>
                    <li><a href="#internals">{t}Internal information{/t} <i class="icon-chevron-right"></i></a> </li>
                    <li><a href="#external">{t}External Services{/t} <i class="icon-chevron-right"></i></a></li>
                </ul>
            </div>
            <div class="span9 settings form-horizontal">
                {render_messages}
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
                            <label for="template_user" class="control-label">{t}Template{/t}</label>
                        </label>
                        <div class="controls">
                            <select name="settings[TEMPLATE_USER]" id="template_user">
                                {foreach $templates as $template_internal_name => $template_info}
                                <option value="{$template_internal_name}" {if $instance->settings['TEMPLATE_USER'] == $template_internal_name}selected{/if}>{$template_info->name}</option>
                                {/foreach}
                            </select>
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
                        <div class="controls">
                            <table class="domain-list">
                                {if $instance && $instance->domains}
                                    {foreach $instance->domains as $domain}
                                    <tr class="domain">
                                        <td>
                                            <input type="text" name="domains[]" value="{$domain}" placeholder="Insert your new domain here">
                                        </td>
                                        <!-- <td class="side-tool" style="width:0px">
                                            <button title="Delete domain" class="mark-main" title="mark as the main domain"><i class="icon icon-certificate"></i> </button>
                                        </td> -->
                                        <td class="side-tool" style="width:0px">
                                            <button title="Delete domain" class="del"><i class="icon icon-trash"></i> </button>
                                        </td>
                                        <td class="side-tool" style="width:0px">
                                            <button title="Visit domain" class="visit-domain"><i class="icon icon-external-link"></i> </button>
                                        </td>
                                    </tr>
                                    {/foreach}
                                {else}
                                    <tr class="domain">
                                        <td>
                                            <input type="text" name="domains[]" placeholder="Insert your new domain here">
                                        </td>
                                        <!-- <td class="side-tool" style="width:0px">
                                            <button title="Delete domain" class="mark-main" title="mark as the main domain"><i class="icon icon-certificate"></i> </button>
                                        </td> -->
                                        <td class="side-tool" style="width:0px">
                                            <button title="Delete domain" class="del"><i class="icon icon-trash"></i> </button>
                                        </td>
                                        <td class="side-tool" style="width:0px">
                                            <button title="Visit domain" class="visit-domain"><i class="icon icon-external-link"></i> </button>
                                        </td>
                                    </tr>
                                {/if}
                                <tfoot class="center add-domain">
                                    <tr><td colspan=3><a href="#" title="Add new domain" ><i class="icon icon-plus"></i></a></td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">
                            <label for="domain_expire" class="control-label">{t}Main domain{/t}</label>
                        </label>
                        <div class="controls">
                            {if $instance}
                                {html_options name=main_domain options=array_merge(array(''),$instance->domains) selected=$instance->main_domain}
                            {else}
                                {html_options name=main_domain options=array() selected=$instance->main_domain}
                            {/if}
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

                <div id="modules">
                    <h4>Modules</h4>
                    <table>
                        <tbody>
                            <tr valign="top" class="control-group">
                                <th scope="row">
                                    <label for="activated_modules" class="control-label"></label>
                                </th>
                                <td class="controls modules-list">
                                    <select id="activated_modules" name="activated_modules[]" size="{count($available_modules)}" multiple="multiple" class="validate-selection">
                                        {foreach $available_modules as $module_key => $module_name}
                                            <option  value="{$module_key}" {if array_key_exists('activated_modules', $configs) && in_array($module_key, $configs['activated_modules'])}selected="selected"{/if}>{$module_name}</option>
                                        {/foreach}
                                    </select>

                                </td>
                            </tr>
                        </tbody>
                    </table>
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
            </div>
        </div>
    </div>
</form>
{/block}
