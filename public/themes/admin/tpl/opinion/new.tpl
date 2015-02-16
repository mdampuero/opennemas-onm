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
    <script>
        // var mediapicker = $('#media-uploader').mediaPicker({
        //     upload_url: "{url name=admin_image_create category=0}",
        //     browser_url : "{url name=admin_media_uploader_browser}",
        //     months_url : "{url name=admin_media_uploader_months}",
        //     maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
        //     // initially_shown: true,
        //     handlers: {
        //         'assign_content' : function( event, params ) {
        //             var mediapicker = $(this).data('mediapicker');
        //             var image_element = mediapicker.buildHTMLElement(params);

        //             if (params['position'] == 'body') {
        //                 CKEDITOR.instances.body.insertHtml(image_element);
        //             } else {
        //                 var container = $('#related_media').find('.'+params['position']);
        //                 var image_element = mediapicker.buildHTMLElement(params, true);

        //                 var image_data_el = container.find('.image-data');
        //                 image_data_el.find('.related-element-id').val(params.content.pk_photo);
        //                 image_data_el.find('.related-element-footer').val(params.content.description);
        //                 image_data_el.find('.image').html(image_element);
        //                 container.addClass('assigned');
        //             };

        //         }
        //     }
        // });
    </script>
{/block}

{block name="content"}
<form action="{iF $opinion->id}{url name=admin_opinion_update id=$opinion->id}{else}{url name=admin_opinion_create}{/if}" method="POST" id="formulario">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-quote-right"></i>
                            {t}Opinions{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>
                            {if $opinion->id}
                                {t}Editing opinion{/t}
                            {else}
                                {t}Creating opinion{/t}
                            {/if}
                        </h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <div class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_opinions}" title="{t}Go back{/t}">
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <a class="btn btn-white" href="#" accesskey="P" id="button_preview">
                                <i class="fa fa-desktop"></i>
                                {t}Preview{/t}
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i>
                                {t}Save{/t}
                            </button>
                        </li>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        {render_messages}
        <div class="row">
            <div class="col-md-8">
                <div class="grid simple">
                    <div class="grid-body">
                        <div class="form-group">
                            <label class="form-label" for="title">
                                {t}Title{/t}
                            </label>
                            <div class="controls">
                                <div class="input-group" id="title">
                                    <input class="form-control" name="title" required="required" type="text" value="{$opinion->title|clearslash|escape:"html"}"/>
                                    <span class="input-group-addon add-on"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="summary">
                                {t}Summary{/t}
                            </label>
                            <div class="controls">
                                <textarea class="form-control" onmeditor onmeditor-preset="simple" id="summary" data-preset="simple" name="summary">{$opinion->summary|clearslash|escape:"html"|default:"&nbsp;"}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="body">
                                <span class="pull-left">{t}Body{/t}</span>
                            </label>
                            {acl isAllowed='PHOTO_ADMIN'}
                                <div class="pull-right">
                                    <a href="#media-uploader" data-toggle="modal" data-position="body" class="btn btn-mini"> + {t}Insert image{/t}</a>
                                </div>
                            {/acl}
                            <div class="controls">
                                <textarea name="body" id="body" class="form-control" onmeditor onmeditor-preset="standard">{$opinion->body|clearslash|default:"&nbsp;"}</textarea>
                            </div>
                        </div>
                        <input type="hidden" id="fk_user_last_editor" name="fk_user_last_editor" value="{$publisher|default:""}"/>
                        <input type="hidden" id="category" name="category" title="opinion" value="opinion" />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="grid simple">
                            <div class="grid-title">
                                <h4>{t}Attributes{/t}</h4>
                            </div>
                            <div class="grid-body">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input id="content_status" name="content_status" type="checkbox" {if $opinion->content_status eq 1}checked="checked"{/if}/>
                                        <label for="content_status">
                                            {t}Available{/t}
                                        </label>
                                    </div>
                                </div>
                                {is_module_activated name="COMMENT_MANAGER"}
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($opinion) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($opinion) && $opinion->with_comment eq 1)}checked{/if}  />
                                            <label for="with_comment">
                                                {t}Allow comments{/t}
                                            </label>
                                        </div>
                                    </div>
                                {/is_module_activated}
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input id="in_home" name="in_home" type="checkbox" {if $opinion->in_home eq 1}checked="checked"{/if}>
                                        <label for="in_home">
                                            {t}In homepage{/t}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="type_opinion">
                                        {t}Type{/t}
                                    </label>
                                    <div class="controls">
                                        <select id="type_opinion" name="type_opinion" required="required">
                                            <option value="-1">{t}-- Pick an author --{/t}</option>
                                            <option value="0" {if $opinion->type_opinion eq 0} selected {/if}>{t}Opinion from author{/t}</option>
                                            <option value="1" {if $opinion->type_opinion eq 1} selected {/if}>{t}Opinion from editorial{/t}</option>
                                            <option value="2" {if $opinion->type_opinion eq 2} selected {/if}>{t}Director's letter{/t}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="fk_author">
                                        {t}Author{/t}
                                    </label>
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
                                <div class="form-group">
                                    <label class="form-label">
                                        {t}Tags{/t}
                                    </label>
                                    <div class="controls">
                                        <input class="form-control bootstrap-tagsinput" id="metadata" name="metadata" required="required" type="text" value="{$opinion->metadata|clearslash|escape:"html"}"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="grid simple">
                            <div class="grid-title">
                                <h4>{t}Schedule{/t}</h4>
                            </div>
                            <div class="grid-body">
                                <div class="form-group">
                                    <label class="form-label" for="starttime">
                                        {t}Publication start date{/t}
                                    </label>
                                    <div class="controls">
                                        <input id="starttime" name="starttime" type="datetime" value="{$opinion->starttime}">
                                        <div class="help-block">{t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="endtime">
                                        {t}Publication end date{/t}
                                    </label>
                                    <div class="controls">
                                        <input id="endtime" name="endtime" type="datetime" value="{$opinion->endtime}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {include  file="article/partials/_images.tpl" article=$opinion withoutVideo='true'}
            </div>
        </div>
    </div>
</div>
</form>
{/block}
