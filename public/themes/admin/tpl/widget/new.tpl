{extends file="base/admin.tpl"}

{block name="content" append}
<form action="{if isset($widget)}{url name=admin_widget_update id=$widget->id}{else}{url name=admin_widget_create}{/if}" method="post" name="formulario" id="formulario">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-gamepad"></i>
                            {t}Widgets{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>
                            {if !isset($widget->id)}
                                {t}Creating widget{/t}
                            {else}
                                {t}Editing widget{/t}
                            {/if}
                        </h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_widgets}" title="{t}Go back to list{/t}">
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="content">

        {render_messages}

        <div class="grid simple">
            <div class="grid-body">
            <div class="row">
                <div class="col-sm-8">
                    <div class="form-group">
                        <label for="metadata" class="form-label">{t}Widget name{/t}</label>
                        <div class="controls">
                            <input type="text" id="title" name="title" value="{$widget->title|default:""}" required="required" class="form-input"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="renderlet" class="form-label">{t}Widget type{/t}</label>
                        <div class="controls">
                            <select name="renderlet" id="renderlet" class="form-input">
                                <option value="intelligentwidget" {if isset($widget) && $widget->renderlet == 'intelligentwidget'}selected="selected"{/if}>{t}Intelligent Widget{/t}</option>
                                <option value="html" {if isset($widget) && $widget->renderlet == 'html'}selected="selected"{/if}>{t}HTML{/t}</option>
                                <option value="smarty" {if isset($widget) && $widget->renderlet == 'smarty'}selected="selected"{/if}>{t}Smarty{/t}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">{t}Description{/t}</label>
                        <div class="controls">
                            <textarea name="description" id="description" class="input-xxlarge">{$widget->description|default:""}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">
                            {t}Content{/t}
                        </label>
                        <div class="pull-right">
                            {acl isAllowed='PHOTO_ADMIN'}
                            <a href="#media-uploader" data-toggle="modal" data-position="body" class="btn btn-mini insert-image" style="{if !isset($widget) || $widget->renderlet != 'html'}display:none{/if}"> + {t}Insert image{/t}</a>
                            {/acl}
                        </div>
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
                </div>

                <div class="col-sm-4">
                    <div class="contentbox">
                        <h3 class="title">{t}Attributes{/t}</h3>
                        <div class="content">
                            <div class="form-group">
                                <label for="available" class="form-label">{t}Published{/t}</label>
                                <div class="controls">
                                    <select name="content_status" id="content_status">
                                        <option value="1" {if isset($widget) && $widget->content_status == 1}selected="selected"{/if}>{t}Yes{/t}</option>
                                        <option value="0" {if isset($widget) && $widget->content_status == 0}selected="selected"{/if}>{t}No{/t}</option>
                                    </select>
                                </div>
                            </div>
                            {if isset($widget) && $widget->renderlet == 'intelligentwidget'}
                                <button class="btn btn-params" type="button">
                                   Parameters
                                </button>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="modal hide fade" id="modal-params">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3>{t}Parameters{/t}</h3>
        </div>
        <div class="modal-body">
            <p>{t}Use this option only if you are avanced user{/t}</p>
            <div id="params">
                {foreach $widget->params as $item => $value}
                    <div class="widget-param">
                        <div class="input-append" style="display:inline-block">
                            <input type="text" name="items[]" value="{$item}"/>
                            <input type="text" name="values[]" value="{$value}">
                            <div class="btn addon del">
                                <i class="icon-trash"></i>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
            <br>
        </div>
        <div class="modal-footer">
            <a id="add_param" class="btn">
                <i class="icon-plus"></i>
                {t}Add parameter{/t}
            </a>
            <a id="save" class="btn" data-dismiss="modal" >
                {t}Close{/t}
            </a>
        </div>
    </div>
</form>

{/block}


{block name="footer-js" append}
{javascripts src="@Common/js/jquery/jquery.tagsinput.min.js"}
    <script type="text/javascript" src="{$asset_url}"></script>
{/javascripts}
<script id="param-template" type="text/x-handlebars-template">
<div class="widget-param">
    <div class="input-append">
        <input type="text" name="items[]" value=""/>
        <input type="text" name="values[]" value=""/>
        <div class="btn addon del">
            <i class="icon-trash"></i>
        </div>
    </div>
</div>
</script>

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

    $('#params').on('click', '.del', function() {
        var button = $(this);
        button.closest('.widget-param').each(function(){
            $(this).remove();
        });
    });

    $('#add_param').on('click', function(){
        var source = $('#param-template').html();
        $('#params').append(source);
    });
    $(".btn-params").on('click', function () {
        $("#modal-params").modal({
            backdrop: 'static', //Show a grey back drop
            keyboard: true,
        });
    });
    $('#modal-params a.btn.yes').on('click', function(e, ui) {
        e.preventDefault();
        var url = '';
        if (url) {
            $.ajax({
                url:  url,
                success: function(){
                    $("#modal-params").modal('hide');
                }
            });
        }

        e.preventDefault();
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
