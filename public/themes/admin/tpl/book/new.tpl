{extends file="base/admin.tpl"}

{block name="footer-js" append}
{include file="media_uploader/media_uploader.tpl"}
<script type="text/javascript">
    jQuery('#title').on('change', function(e, ui) {
        fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
    });

    var mediapicker = $('#media-uploader').mediaPicker({
        upload_url: "{url name=admin_image_create category=0}",
        browser_url : "{url name=admin_media_uploader_browser}",
        months_url : "{url name=admin_media_uploader_months}",
        maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
        handlers: {
            'assign_content' : function( event, params ) {
                var mediapicker = $(this).data('mediapicker');

                if (params['position'] == 'cover-image') {
                    var container = $('.cover-image');
                    var image_element = mediapicker.buildHTMLElement(params, true);
                    var image_data_el = container.find('.image-data');
                    image_data_el.find('.book-cover-image').val(params.content.pk_photo);
                    container.addClass('assigned');

                    image_data_el.find('.image').html(image_element);

                }
            }
        }
    });
</script>

{javascripts src="@AdminTheme/js/onm/jquery.datepicker.js"}
    <script type="text/javascript" src="{$asset_url}"></script>
{/javascripts}
{/block}

{block name="header-css" append}
    <style>
        .contentbox-container { float:none;margin-right:0; }
    </style>
{/block}

{block name="content"}
<form action="{if isset($book)}{url name=admin_books_update id=$book->id}{else}{url name=admin_books_create}{/if}"
    method="POST" name="formulario" id="formulario" enctype="multipart/form-data">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        {t}Books{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{if !isset($book->id)}{t}Creating Book{/t}{else}{t}Editing Book{/t}{/if}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" href="{url name=admin_books category=$category|default:""}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                    {if isset($book->id)}
                        {acl isAllowed="BOOK_UPDATE"}
                        <button class="btn btn-primary" href="{url name=admin_books_update id=$book->id}" name="continue" value="1">
                            <span class="fa fa-save"></span>
                            {t}Save{/t}
                        </button>
                        {/acl}
                    {else}
                        {acl isAllowed="BOOK_CREATE"}
                        <button class="btn btn-primary" href="{url name=admin_books_create}" name="continue" value="1">
                             <img src="{$params.IMAGE_DIR}save.png"><br />{t}Save{/t}
                        </button>
                        {/acl}
                    {/if}
                </ul>
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
                        <label for="title" class="form-label">{t}Title{/t}</label>
                        <div class="controls">
                            <input type="text" id="title" name="title" value="{$book->title|default:""}" required="required" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contentbox" class="form-label"></label>
                        <div class="contentbox-container controls">
                            <div class="contentbox">
                                <h3 class="title">{t}Cover image{/t}</h3>
                                <div class="content cover-image {if !empty($book->cover_id)}assigned{/if}">
                                    <div class="image-data">
                                        <a href="#media-uploader" {acl isAllowed='PHOTO_ADMIN'}data-toggle="modal"{/acl} data-position="inner-image" class="image thumbnail">
                                            {if !is_null($book->cover_img)}
                                                <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$book->cover_img->path_file}{$book->cover_img->name}"/>
                                            {/if}
                                        </a>

                                        <div class="book-resource-footer">
                                            <input type="hidden" name="cover_image" value="{$book->cover_id|default:""}" class="book-cover-image"/>
                                        </div>
                                    </div>


                                    <div class="not-set">
                                        {t}Image not set{/t}
                                    </div>

                                    <div class="btn-group">
                                        <a href="#media-uploader" {acl isAllowed='PHOTO_ADMIN'}data-toggle="modal"{/acl} data-position="cover-image" class="btn btn-small">{t}Set image{/t}</a>
                                        <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="author" class="form-label">{t}Author{/t}</label>
                        <div class="controls">
                            <input type="text" id="author" name="author" value="{$book->author|default:""}" required="required" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="starttime" class="form-label">{t}Date{/t}</label>
                        <div class="controls">
                            <input type="datetime" id="date" name="starttime" value="{$book->starttime}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="position" class="form-label">{t}Position{/t}</label>
                        <div class="controls">
                            <input type="number" id="position" name="position" value="{$book->position}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editorial" class="form-label">{t}Editorial{/t}</label>
                        <div class="controls">
                            <input type="text" id="editorial" name="editorial" value="{$book->editorial|default:""}" required="required" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">{t}Description{/t}</label>
                        <div class="controls">
                            <textarea id="description" name="description" rows="3" class="form-control">{$book->description|clearslash}</textarea>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="action" name="action" value="" />
                <input type="hidden" name="id" id="id" value="{$book->id|default:""}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="grid simple">
                <div class="grid-title">{t}Attributes{/t}</div>
                <div class="grid-body">
                    <div class="form-group">
                        <label for="category" class="form-label">{t}Category{/t}</label>
                        <div class="controls">
                            {include file="common/selector_categories.tpl" name="category" item=$book}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="content_status" class="form-label">{t}Available{/t}</label>
                        <div class="controls">
                            <select name="content_status" id="content_status"
                                class="required" {acl isNotAllowed="BOOK_AVAILABLE"} disabled="disabled" {/acl}>
                                <option value="0" {if $book->content_status eq 0} selected {/if}>{t}No{/t}</option>
                                <option value="1" {if !isset($book) || $book->content_status eq 1} selected {/if}>{t}Yes{/t}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="metadata" class="form-label">{t}Keywords{/t}</label>
                        <div class="controls">
                            <input type="text" id="metadata" name="metadata" value="{$book->metadata|default:""}" required="required" class="form-control"/>
                            <span class="help-block">{t}Separated by coma{/t}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
{/block}
