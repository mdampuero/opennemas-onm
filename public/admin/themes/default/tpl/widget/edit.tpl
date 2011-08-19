{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script language="Javascript" type="text/javascript">
// FIXME: fix toolbar
submitForm = function() {
    document.getElementById('formulario').submit();
};
</script>
{/block}

{block name="content" append}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{if $smarty.request.action eq "new"}{t}Creating new widget{/t}{else}{t 1=$widget->title}Editing widget «%1»{/t}{/if}</h2>
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
        <input type="hidden" id="id" name="id" value="{$widget->id|default:""}" />
        <input type="hidden" id="action" name="action" value="save" />

        <div id="warnings-validation"></div>

        <table class="adminheading">
            <tbody>
                <tr>
                    <th>{t}Widget data{/t}</th>
                </tr>
            </tbody>
        </table>
        <table class="adminform">
        <tbody>
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="150px">
                <label for="title">{t}Widget name{/t}:</label>
            </td>
            <td>
                <input type="text" id="title" value="{$widget->title|default:""}" name="title" title="Nombre del widget" class="required" size="30" maxlength="60" />
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                <label for="available">{t}Published{/t}:</label>
            </td>
            <td>
                <select name="available" id="available">
                    <option value="1" {if isset($widget) && $widget->available == 1}selected="selected"{/if}>{t}Yes{/t}</option>
                    <option value="0" {if isset($widget) && $widget->available == 0}selected="selected"{/if}>{t}No{/t}</option>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                <label for="renderlet">{t}Widget type{/t}:</label>
            </td>
            <td>
                <select name="renderlet" id="renderlet">
                    <option value="html" {if isset($widget) && $widget->renderlet == 'html'}selected="selected"{/if}>{t}HTML{/t}</option>
                    <option value="php" {if isset($widget) && $widget->renderlet == 'php'}selected="selected"{/if}>{t}PHP{/t}</option>
                    <option value="smarty" {if isset($widget) && $widget->renderlet == 'smarty'}selected="selected"{/if}>{t}Smarty{/t}</option>
                    <option value="intelligentwidget" {if isset($widget) && $widget->renderlet == 'intelligentwidget'}selected="selected"{/if}>{t}Intelligent Widget{/t}</option>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                <label for="metadata">{t}Keywords{/t}:</label>
            </td>
            <td>
                <input type="text" name="metadata" id="metadata" value="{$widget->metadata|default:""}" />
            </td>
        </tr>

        <tr>
            <td valign="top" align="right" style="padding:4px;">
                <label for="description">{t}Description{/t}:</label>
            </td>
            <td>
                <textarea name="description" id="description" cols="80" rows="5">{$widget->description|default:""}</textarea>
            </td>
        </tr>

        <tr class="widget-content">
            <td valign="top" align="right" style="padding:4px;">
                <label>{t}Content{/t}:</label>
            </td>
            <td>
                <textarea cols="80" id="widget_content" rows="20" name="content">{$widget->content|default:""}</textarea>
                <br/><br/>
            </td>
        </tr>

        </tbody>
        </table>
        <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button red">{t}Save{/t}</button>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
<script type="text/javascript" language="javascript">
        tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );

        OpenNeMas.tinyMceConfig.simple.elements = "description";
        tinyMCE.init( OpenNeMas.tinyMceConfig.simple );

        OpenNeMas.tinyMceConfig.advanced.elements = "widget_content";
        tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
</script>
{/block}
