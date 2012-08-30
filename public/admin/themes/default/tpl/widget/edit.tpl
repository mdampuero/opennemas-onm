{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    label {
        float:right;
    }
    td {
        padding:10px;
    }
    </style>
{/block}

{block name="content" append}
<form action="{if isset($widget)}{url name=admin_widget_update id=$widget->id page=$page}{else}{url name=admin_widget_create}{/if}" method="post" name="formulario" id="formulario">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {if $action eq "new"}
                        {t}Creating new widget{/t}
                    {else}
                        {t 1=$widget->title}Editing widget "%1"{/t}
                    {/if}
                </h2>
            </div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br>{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_widgets}" class="admin_add" title="{t}Cancel{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Cancel{/t}" /><br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="metadata" class="control-label">{t}Widget name{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" value="{$widget->title|default:""}" required="required" size="30" maxlength="60"/>
                </div>
            </div>

            <div class="control-group">
                <label for="available" class="control-label">{t}Published{/t}</label>
                <div class="controls">
                    <select name="available" id="available">
                        <option value="1" {if isset($widget) && $widget->available == 1}selected="selected"{/if}>{t}Yes{/t}</option>
                        <option value="0" {if isset($widget) && $widget->available == 0}selected="selected"{/if}>{t}No{/t}</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="renderlet" class="control-label">{t}Widget type{/t}</label>
                <div class="controls">
                    <select name="renderlet" id="renderlet">
                        <option value="intelligentwidget" {if isset($widget) && $widget->renderlet == 'intelligentwidget'}selected="selected"{/if}>{t}Intelligent Widget{/t}</option>
                        <option value="html" {if isset($widget) && $widget->renderlet == 'html'}selected="selected"{/if}>{t}HTML{/t}</option>
                        <option value="php" {if isset($widget) && $widget->renderlet == 'php'}selected="selected"{/if}>{t}PHP{/t}</option>
                        <option value="smarty" {if isset($widget) && $widget->renderlet == 'smarty'}selected="selected"{/if}>{t}Smarty{/t}</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Keywords{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" value="{$widget->metadata|default:""}" size="30" maxlength="60" class="input-xxlarge"/>
                </div>
            </div>

            <div class="control-group">
                <label for="description" class="control-label">{t}Description{/t}</label>
                <div class="controls">
                    <textarea name="description" id="description" class="input-xxlarge">{$widget->description|default:""}</textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="description" class="control-label">{t}Content{/t}</label>
                <div class="controls">
                    <div id="widget_textarea" style="{if isset($widget) && $widget->renderlet == 'intelligentwidget' || $action eq 'new'}display:none{else}display:inline{/if}">
                        <textarea cols="80" id="widget_content" rows="20" name="content" class="input-xxlarge">{$widget->content|default:""}</textarea>
                    </div>

                    <div id="select-widget" style="{if isset($widget) && $widget->renderlet == 'intelligentwidget' || $action eq 'new'}display:inline{else}display:none{/if}">
                        <select name="content" id="all-widgets" {if isset($widget)}disabled="disabled"{/if}>
                            {foreach from=$all_widgets item=w}
                            <option value="{$w}" {if isset($widget) && $widget->content == $w}selected="selected"{/if}>{$w}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
{/block}


{block name="footer-js" append}
{script_tag src="/tiny_mce/opennemas-config.js"}
<script type="text/javascript">
//TinyMce scripts
tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
OpenNeMas.tinyMceConfig.advanced.elements = "widget_content";
{if isset($widget) && $widget->renderlet == 'html'}
    tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
{/if}

jQuery(document).ready(function($) {

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    $('#renderlet').on('change', function() {
        var value = $(this).find('option:selected').val();
        if(value == 'html') {
            $('#widget_textarea').show();
            $('#select-widget').hide();
            tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
        } else if (value == 'intelligentwidget') {
            $('widget_textarea').hide();
            $('select-widget').show();
        } else {
            $('#widget_textarea').show();
            $('#select-widget').hide();
            OpenNeMas.tinyMceFunctions.destroy( 'widget_content' );
        }
    });
});
</script>
{/block}