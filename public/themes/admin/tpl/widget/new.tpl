{extends file="base/admin.tpl"}

{block name="content" append}
<form action="{if isset($widget)}{url name=admin_widget_update id=$widget->id}{else}{url name=admin_widget_create}{/if}" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{if !isset($widget->id)}{t}Creating widget{/t}{else}{t}Editing widget{/t}{/if}</h2>
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

    <div class="wrapper-content contentform clearfix">

        {render_messages}

        <div class="form-vertical contentform-inner">

            <div class="contentform-main">
                <div class="control-group">
                    <label for="metadata" class="control-label">{t}Widget name{/t}</label>
                    <div class="controls">
                        <input type="text" id="title" name="title" value="{$widget->title|default:""}" required="required" class="input-xxlarge"/>
                    </div>
                </div>
                <div class="control-group">
                    <label for="renderlet" class="control-label">{t}Widget type{/t}</label>
                    <div class="controls">
                        <select name="renderlet" id="renderlet">
                            <option value="intelligentwidget" {if isset($widget) && $widget->renderlet == 'intelligentwidget'}selected="selected"{/if}>{t}Intelligent Widget{/t}</option>
                            <option value="html" {if isset($widget) && $widget->renderlet == 'html'}selected="selected"{/if}>{t}HTML{/t}</option>
                            <option value="smarty" {if isset($widget) && $widget->renderlet == 'smarty'}selected="selected"{/if}>{t}Smarty{/t}</option>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label for="description" class="control-label">{t}Description{/t}</label>
                    <div class="controls">
                        <textarea name="description" id="description" class="input-xxlarge">{$widget->description|default:""}</textarea>
                    </div>
                </div>
            </div>

            <div class="contentbox-container">
                <div class="contentbox">
                    <h3 class="title">{t}Attributes{/t}</h3>
                    <div class="content">
                        <div class="control-group">
                            <label for="available" class="control-label">{t}Published{/t}</label>
                            <div class="controls">
                                <select name="content_status" id="content_status">
                                    <option value="1" {if isset($widget) && $widget->content_status == 1}selected="selected"{/if}>{t}Yes{/t}</option>
                                    <option value="0" {if isset($widget) && $widget->content_status == 0}selected="selected"{/if}>{t}No{/t}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contentbox">
                    <h3 class="title">{t}Tags{/t}</h3>
                    <div class="content">
                        <div class="control-group">
                            <div class="controls">
                                <input  type="text" id="metadata" name="metadata" required="required" value="{$widget->metadata|clearslash|escape:"html"}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contentform-main">
                <div class="control-group">
                    <label for="description" class="control-label clearfix">
                        <div class="pull-left">{t}Content{/t}</div>
                        <div class="pull-right">
                            {acl isAllowed='PHOTO_ADMIN'}
                            <a href="#media-uploader" data-toggle="modal" data-position="body" class="btn btn-mini insert-image" style="{if !isset($widget) || $widget->renderlet != 'html'}display:none{/if}"> + {t}Insert image{/t}</a>
                            {/acl}
                        </div>
                    </label>
                    <div class="controls">
                        <div id="widget_textarea" style="{if isset($widget) && $widget->renderlet == 'intelligentwidget' || $action eq 'new'}display:none{else}display:inline{/if}">
                            <textarea id="widget_content" name="content" class="onm-editor">{$widget->content|default:""}</textarea>
                        </div>

                        <div id="select-widget" style="{if isset($widget) && $widget->renderlet == 'intelligentwidget' || $action eq 'new'}display:inline{else}display:none{/if}">
                            <select name="intelligent-type" id="all-widgets" {if isset($widget)}disabled="disabled"{/if}>
                                {foreach from=$all_widgets item=w}
                                <option value="{$w}" {if isset($widget) && $widget->content == $w}selected="selected"{/if}>{$w}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                {acl isAllowed="ONLY_MASTERS"}
                <div class="control-group">
                    <label for="parameters" class="control-label">{t}Parameters{/t}</label>
                    <div class="controls">
                        <textarea name="parameters" id="parameters" class="input-xxlarge" data-preset="simple">{$widget->parameters|clearslash}</textarea>
                    </div>
                </div>
                {/acl}
            </div>
        </div>
    </div>
</form>
{/block}


{block name="footer-js" append}
{script_tag src="/jquery/jquery.tagsinput.min.js" common=1}
<script type="text/javascript">
var tags_input = $('#metadata').tagsInput({ width: '100%', height: 'auto', defaultText: "{t}Write a tag and press Enter...{/t}"});

$('#title').on('change', function(e, ui) {
    fill_tags_improved($('#title').val(), tags_input, '{url name=admin_utils_calculate_tags}');
});


jQuery(document).ready(function($) {

    {if isset($widget) && $widget->renderlet !== 'html'}
        CKEDITOR.instances.widget_content.destroy();
    {/if}

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    $('#renderlet').on('change', function() {
        var value = $(this).find('option:selected').val();
        if (value == 'html') {
            $('.insert-image').show();
            $('#widget_textarea').show();
            $('#select-widget').hide();
            $.onmEditor();
        } else if (value == 'intelligentwidget') {
            $('.insert-image').hide();
            $('widget_textarea').hide();
            $('select-widget').show();
        } else {
            $('.insert-image').hide();
            $('#widget_textarea').show();
            $('#select-widget').hide();
            CKEDITOR.instances.widget_content.destroy();
        }
    });
});
</script>
{include file="media_uploader/media_uploader.tpl"}
<script>
    $(function(){
        var mediapicker = $('#media-uploader').mediaPicker({
            upload_url: "{url name=admin_image_create category=0}",
            browser_url : "{url name=admin_media_uploader_browser}",
            months_url : "{url name=admin_media_uploader_months}",
            maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
            // initially_shown: true,
            handlers: {
                'assign_content' : function( event, params ) {
                    var mediapicker = $(this).data('mediapicker');
                    var image_element = mediapicker.buildHTMLElement(params);

                    if (params['position'] == 'body') {
                        CKEDITOR.instances.widget_content.insertHtml(image_element);
                    }
                }
            }
        });
    })
</script>
{/block}
