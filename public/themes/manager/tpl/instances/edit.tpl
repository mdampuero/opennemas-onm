<div class="content">
    <div class="page-title clearfix">
        <h3 class="pull-left">
            <i class="fa fa-cubes"></i>
            <span ng-if="!item.id">{t}New instance{/t}</span>
            <span ng-if="item.id">{t}Edit instance{/t}</span>
        </h3>
        <ul class="breadcrumb pull-right">
            <li>
                <p>{t}YOU ARE HERE{/t}</p>
            </li>
            <li>
                <a href="#">{t}Dashboard{/t}</a>
            </li>
            <li>
                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_list') %]">{t}Instances{/t}</a>
            </li>
            <li>
                <a class="active" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_list') %]" ng-if="!item.id">{t}New instance{/t}</a>
                <a class="active" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_list') %]" ng-if="item.id">{t}Edit instance{/t}</a>
            </li>
        </ul>
    </div>
    <div class="grid simple">
        <div class="grid-title clearfix">
            <h3 class="pull-left">
                <span class="semi-bold">
                    Instance test
                </span>
            </h3>
            <h3 class="pull-right">
                <small class="text-right">
                    <i class="fa fa-user"></i>
                    diego@openhost.es
                    |
                    <i class="fa fa-clock-o"></i>
                    2145-4-56 65:45:44
                    |
                    <i class="fa fa-code"></i>
                    192.168.0.1
                    |
                    <i class="fa fa-database"></i>
                    0.00 MB
                </small>
            </h3>
        </div>
        <div class="grid-body">
            <div class="row">
                <div class="col-md-12">
                    <h4>General information</h4>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">{t}Site name{/t}</label>
                        <span class="help">{t}(Human readable name){/t}</span>
                        <div class="controls">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">{t}Internal name{/t}</label>
                        <span class="help">{t}Alphanumeric, without spaces{/t}</span>
                        <div class="controls">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="template">{t}Database{/t}</label>
                        <div class="controls">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row form-row">
                        <div class="col-md-12">
                            <h4>Domains</h4>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="domains" class="form-label">{t}Domains{/t}</label>
                                <div class="controls">
                                    <textarea name="" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="domains" class="form-label">{t}Main domain{/t}</label>
                                <div class="controls">
                                    <select name="main-domain" id="main-domain">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{t}Domain expire date:{/t}</label>
                                <div class="controls">
                                    <input type="datetime" id="domain_expire" name="external[domain_expire]" value="{$instance->external['domain_expire']|default:""}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row form-row">
                        <div class="col-md-12">
                            <h4>Internals</h4>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-md-12">
                             <div class="form-group">
                                <label class="form-label" for="template">{t}Minimum password level{/t}</label>
                                <div class="controls">
                                    <select name="external[pass_level]" id="pass_level">
                                        <option value="-1" >{t}Default{/t}</option>
                                        <option value="0" >{t}Weak{/t}</option>
                                        <option value="1" >{t}Good{/t}</option>
                                        <option value="2" >{t}Strong{/t}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="template">{t}Language{/t}</label>
                                <div class="controls">
                                    {html_options name="external[site_language]" options=$languages selected=$instance->external['site_language']}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="control-group">
        <label class="control-label">
            <label for="last_invoice" class="control-label">{t}Last invoice date{/t}</label>
        </label>
        <div class="controls">
            <input type="text" id="last_invoice" name="external[last_invoice]" value="{if $instance->external['last_invoice']}{$instance->external['last_invoice']}{/if}">
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
                                            <option  value="{$module_key}" {if $instance->external && array_key_exists('activated_modules', $instance->external) && in_array($module_key, $instance->external['activated_modules'])}selected="selected"{/if}>{$module_name}</option>
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
                            <label for="site_title" class="control-label">{t}Time Zone{/t}</label>
                        </label>
                        <div class="controls">
                            {html_options name="external[time_zone]" options=$timezones selected=$instance->external['time_zone']}
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">
                            <label for="max_mailing" class="control-label">{t}Num Max emails sent by month{/t}</label>
                        </label>
                        <div class="controls">
                            <input type="text" id="max_mailing" name="external[max_mailing]" value="{$instance->external['max_mailing']|default:'12000'}">
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
                                    <input type="text" id="piwik_page_id" name="external[piwik][page_id]" value="{$instance->external['piwik']['page_id']|default:""}">
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
                                    <input type="text" id="piwik_server_url" name="external[piwik][server_url]" value="{$instance->external['piwik']['server_url']|default:""}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// jQuery(document).ready(function($) {
//     $('#formulario').onmValidate({
//         'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
//     });

//     jQuery('#domain_expire').datetimepicker({
//         hourGrid: 4,
//         showAnim: 'fadeIn',
//         dateFormat: 'yy-mm-dd',
//         timeFormat: 'hh:mm:ss',
//         minuteGrid: 10
//     });

//     jQuery('#last_invoice').datetimepicker({
//         hourGrid: 4,
//         showAnim: 'fadeIn',
//         dateFormat: 'yy-mm-dd',
//         timeFormat: 'hh:mm:ss',
//         minuteGrid: 10
//     });

//     $('.affix').affix();
//     $('#activated_modules').twosidedmultiselect();

//     $('.domain-list').on('click', '.domain .del', function(e, ui) {
//         e.preventDefault();
//         $(this).closest('.domain').remove();
//     });
//     $('.domain-list').on('click', '.add-domain a', function(e, ui) {
//         e.preventDefault();
//         console.log('add new')

//         new_domain = $(this).closest('.domain-list').find('.domain:first').clone();
//         new_domain.find('input').val('');
//         new_domain.insertBefore('.add-domain');
//     })
//     $('.domain-list').on('click', '.visit-domain', function(e, ui) {
//         e.preventDefault();

//         url = $(this).closest('.domain').find('input').val();
//         window.open('http://'+url, '_blank').focus();
//     })
// });
</script>
