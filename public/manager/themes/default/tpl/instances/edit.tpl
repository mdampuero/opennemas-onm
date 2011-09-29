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
<form action="{$smarty.server.PHP_SELF}" method="post" name="formulario" id="formulario">
    <div class="wrapper-content">
        <input type="hidden" id="id" name="id" value="{$instance->id|default:""}" />
        <input type="hidden" id="action" name="action" value="save" />


        <ul id="tabs">
            <li>
                <a href="#general">{t}General{/t}</a>
                <a href="#internals">{t}Internals{/t}</a>
                <a href="#database">{t}Database{/t}</a>
            </li>
        </ul>
        <div id="general" class="panel">
            <table>
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="name">{t}Instance name{/t}:</label>
                        </th>
                        <td>
                            <input type="text" id="name" name="name" value="{$instance->name}">
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
                            <input type="text" id="internal_name" name="internal_name" value="{$instance->internal_name|default:""}">
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
                            <label for="internal_name">{t}Template:{/t}</label>
                        </th>
                        <td><input name="settings[TEMPLATE_USER]" value="{$instance->settings['TEMPLATE_USER']}" type="text"></input></td>
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
                        </td>
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
                            <input name="settings[BD_HOST]" value="{$instance->settings['BD_HOST']|default:"localhost"}" type="text"></input>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="settings_bd_database">{t}Database name:{/t}</label>
                        </th>
                        <td><input name="settings[BD_DATABASE]" id="settings_bd_database" value="{$instance->settings['BD_DATABASE']}" type="text"></input></td>
                    </tr>
                    <tr>
                        <th>
                            <label for="settings_bd_user">{t}Database user:{/t}</label>
                        </th>
                        <td>
                            <input name="settings[BD_USER]" id="settings_bd_user" value="{$instance->settings['BD_USER']}" type="text"></input>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="settings_bd_password">{t}Database password:{/t}</label>
                        </th>
                        <td><input name="settings[BD_PASS]" id="settings_bd_password" value="{$instance->settings['BD_PASS']}" type="password"></input></td>
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
<script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
<script type="text/javascript" language="javascript">
{if isset($instance) && $instance->renderlet == 'html'}
        tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );

        OpenNeMas.tinyMceConfig.advanced.elements = "widget_content";
        tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
{/if}
</script>
{/block}
