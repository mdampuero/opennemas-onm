{extends file="base/admin.tpl"}

{block name="header-css" append}
    {stylesheets src="@AdminTheme/css/jquery/colorbox.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}" media="screen">
    {/stylesheets}
{/block}

{block name="footer-js" append}
    {javascripts src="@AdminTheme/js/onm/jquery.datepicker.js,
        @AdminTheme/js/jquery/jquery-ui-timepicker-addon.js,
        @AdminTheme/js/jquery/jquery.colorbox-min.js,
        @AdminTheme/js/jquery-onm/jquery.inputlength.js,
        @Common/js/jquery/jquery.tagsinput.min.js
        "}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
    <script>
        $('.tabs').tabs();

        var opinions_urls = {
            preview : '{url name=admin_opinion_preview}',
            get_preview : '{url name=admin_opinion_get_preview}'
        };

        jQuery(document).ready(function ($){
            var tags_input = $('#metadata').tagsInput({ width: '100%', height: 'auto', defaultText: "{t}Write a tag and press Enter...{/t}"});

            $('#title').inputLengthControl();

            $('#title input').on('change', function(e, ui) {
                if (tags_input.val().length == 0) {
                    fill_tags_improved($('#title input').val(), tags_input, '{url name=admin_utils_calculate_tags}');
                }
            });
            $('#formulario').onmValidate({
                'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
            });
            $('#type_opinion').on('change', function() {
                var selected = $(this).find('option:selected').val();
                if (selected != 0) {
                    $('#author').hide();
                } else {
                    $('#author').show();
                }
            });
            $('#button_preview').on('click', function(e, ui) {
                e.preventDefault();

                CKEDITOR.instances.body.updateElement();
                CKEDITOR.instances.summary.updateElement();

                var form = $('#formulario');
                var contents = form.serializeArray();

                $.ajax({
                    type: 'POST',
                    url: opinions_urls.preview,
                    data: {
                        'contents': contents
                    },
                    success: function(data) {
                        $.colorbox({ href: opinions_urls.get_preview, iframe : true, width: '95%', height: '95%' });
                        $('#warnings-validation').html('');
                    }
                });

                return false;
            });
        });

    </script>
    {include file="media_uploader/media_uploader.tpl"}
    <script>
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
                        CKEDITOR.instances.body.insertHtml(image_element);
                    } else {
                        var container = $('#related_media').find('.'+params['position']);
                        var image_element = mediapicker.buildHTMLElement(params, true);

                        var image_data_el = container.find('.image-data');
                        image_data_el.find('.related-element-id').val(params.content.pk_photo);
                        image_data_el.find('.related-element-footer').val(params.content.description);
                        image_data_el.find('.image').html(image_element);
                        container.addClass('assigned');
                    };

                }
            }
        });
    </script>
{/block}

{block name="content"}
<form action="{iF $opinion->id}{url name=admin_opinion_update id=$opinion->id}{else}{url name=admin_opinion_create}{/if}" method="POST" id="formulario">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{if $opinion->id}{t}Editing opinion{/t}{else}{t}Creating opinion{/t}{/if}</h2></div>
        <ul class="old-button">
            <li>
                <button type="submit" name="continue" value="1">
                    <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br />{t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="#" accesskey="P" id="button_preview">
                    <img src="{$params.IMAGE_DIR}preview.png" alt="{t}Preview{/t}" /><br />{t}Preview{/t}
                </a>
            </li>
            <li>
                <a href="{url name=admin_opinions}" title="{t}Go back{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="wrapper-content contentform">

    {render_messages}

    <div class="form-vertical clearfix">
        <div class="contentform-inner form-vertical clearfix">
            <div class="contentform-main">
                <div class="control-group">
                    <label for="title" class="control-label">{t}Title{/t}</label>
                    <div class="controls">
                        <div class="input-append" id="title">
                            <input type="text" name="title" value="{$opinion->title|clearslash|escape:"html"}"
                                required="required" class="input-xxlarge" />
                            <span class="add-on"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contentbox-container">
                <div class="contentbox">
                    <h3 class="title">{t}Attributes{/t}</h3>
                    <div class="content">
                        <input type="checkbox" name="content_status" id="content_status" {if $opinion->content_status eq 1}checked="checked"{/if} />
                        <label for="content_status">{t}Available{/t}</label>

                        <hr class="divisor">

                        {is_module_activated name="COMMENT_MANAGER"}
                            <input type="checkbox" name="with_comment" id="with_comment" {if (!isset($opinion) && ($commentsConfig['with_comments'])) || (isset($opinion) && $opinion->with_comment eq 1)}checked{/if}  />
                            <label for="with_comment">{t}Allow comments{/t}</label>
                        {/is_module_activated}

                        <hr class="divisor">

                        <input type="checkbox" name="in_home" id="in_home" {if $opinion->in_home eq 1}checked="checked"{/if}>
                        <label for="in_home">{t}In homepage{/t}</label>
                        <br>
                        <hr class="divisor">
                        <label for="type_opinion" class="control-label">{t}Type{/t}</label>
                        <div class="controls">
                            <select name="type_opinion" id="type_opinion" required="required">
                                <option value="-1">{t}-- Pick an author --{/t}</option>
                                <option value="0" {if $opinion->type_opinion eq 0} selected {/if}>{t}Opinion from author{/t}</option>
                                <option value="1" {if $opinion->type_opinion eq 1} selected {/if}>{t}Opinion from editorial{/t}</option>
                                <option value="2" {if $opinion->type_opinion eq 2} selected {/if}>{t}Director's letter{/t}</option>
                            </select>
                        </div>
                        <label for="fk_author" class="control-label">{t}Author{/t}</label>
                        <div class="controls">
                            {acl isAllowed="CONTENT_OTHER_UPDATE"}
                            <select id="fk_author" name="fk_author" required="required">
                                <option value="0" {if is_null($author->id)}selected{/if}>{t} - Select one author - {/t}</option>
                                {foreach from=$all_authors item=author}
                                <option value="{$author->id}" {if $opinion->fk_author eq $author->id}selected{/if}>{$author->name} {if $author->meta['is_blog'] eq 1} (Blogger) {/if}</option>
                                {/foreach}
                            </select>
                            {aclelse}
                            <select id="fk_author" name="fk_author" required="required">
                                <option value="{$smarty.session.id}" selected >{$smarty.session.realname}</option>
                            </select>
                            {/acl}
                        </div>
                    </div>
                </div>
                <div class="contentbox">
                    <h3 class="title">{t}Tags{/t}</h3>
                    <div class="content">
                        <div class="control-group">
                            <div class="controls">
                                <input  type="text" id="metadata" name="metadata" required="required" value="{$opinion->metadata|clearslash|escape:"html"}"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contentbox">
                    <h3 class="title">{t}Schedule{/t}</h3>
                    <div class="content form-vertical">
                        <div class="control-group">
                            <label for="starttime" class="control-label">{t}Publication start date{/t}</label>
                            <div class="controls">
                                <input type="datetime" id="starttime" name="starttime" value="{$opinion->starttime}">
                                <div class="help-block">{t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</div>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="endtime" class="control-label">{t}Publication end date{/t}</label>
                            <div class="controls">
                                <input type="datetime" id="endtime" name="endtime" value="{$opinion->endtime}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contentform-main">
                <div class="control-group">
                    <label for="summary" class="control-label">{t}Summary{/t}</label>
                    <div class="controls">
                        <textarea name="summary" id="summary" class="onm-editor" data-preset="simple">{$opinion->summary|clearslash|escape:"html"|default:"&nbsp;"}</textarea>
                    </div>
                </div>
                <div class="form-vertical">
                <div class="control-group">
                    <label for="body" class="control-label clearfix">
                        <div class="pull-left">{t}Body{/t}</div>
                        <div class="pull-right">
                            {acl isAllowed='PHOTO_ADMIN'}
                            <a href="#media-uploader" data-toggle="modal" data-position="body" class="btn btn-mini"> + {t}Insert image{/t}</a>
                            {/acl}
                        </div>
                    </label>
                    <div class="controls">
                        <textarea name="body" id="body" class="onm-editor">{$opinion->body|clearslash|default:"&nbsp;"}</textarea>
                    </div>
                </div>
            </div><!-- /contentform-main -->
            </div><!-- /contentform-main -->
        </div><!-- /contentform-inner -->
        <div class="contentform-inner wide">
            <div id="related_media" class="clearfix">
                {include  file="article/partials/_images.tpl" article=$opinion withoutVideo='true'}
            </div>
        </div><!-- /contentform-inner -->

        <input type="hidden" id="fk_user_last_editor" name="fk_user_last_editor" value="{$publisher|default:""}"/>
        <input type="hidden" id="category" name="category" title="opinion" value="opinion" />
    </div>
</div>
</form>
{/block}
