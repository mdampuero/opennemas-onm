{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {javascripts src="@Common/js/jquery/jquery.tagsinput.min.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
{include file="media_uploader/media_uploader.tpl"}
<script>
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
                    image_data_el.find('.album-frontpage-image').val(params.content.pk_photo);
                    container.addClass('assigned');

                    image_data_el.find('.image').html(image_element);

                } else {
                    params.class_image = false;
                    var container = $('.list-of-images > ul');

                    var elements = '';
                    $.each(params.content, function(key, elem) {
                        var temp_params = $.extend(params, { 'content': elem });
                        var image_element = '<li class="image thumbnail" id="img'+elem.id+'" >'+
                            '<div class="overlay-image">'+
                                '<div>'+
                                    '<ul class="image-buttons clearfix">'+
                                        '<li><a href="#"  data-id="'+elem.id+'" class="edit-button" title="Editar"><i class="icon-pencil"></i></a></li>'+
                                        '<li><a href="#" class="delete-button" title="{t}Drop{/t}"><i class="icon-trash"></i></a></li>'+
                                    '</ul>'+
                                '</div>'+
                            '</div>'+
                            mediapicker.buildHTMLElement(temp_params, true)+
                            '<textarea name="album_photos_footer[]">'+elem.description+'</textarea>'+
                            '<input type="hidden" name="album_photos_id[]" value="'+elem.id+'">'
                            '</li>' ;
                        elements = elements + image_element;
                    });

                    container.find('.add-image').before(elements);
                }
            }
        }
    });

    jQuery(document).ready(function($){

        var tags_input = $('#metadata').tagsInput({ width: '100%', height: 'auto', defaultText: "{t}Write a tag and press Enter...{/t}"});

        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });

        $("#formulario").on("submit", function(event) {

            var frontpage_image =  $(".album-frontpage-image");
            var album_images =  $("#list-of-images .image");
            if (frontpage_image.val() == "") {
                $("#modal-edit-album-errors").modal('show');
                return false;
            }

            if (album_images.length < 1) {
                $("#modal-edit-album-errors").modal('show');
                return false;
            };
            return true;
        }).on('click', '.cover-image .unset', function (e, ui) {
            e.preventDefault();

            var parent = jQuery(this).closest('.contentbox');

            parent.find('.related-element-id').val('');
            parent.find('.related-element-footer').val('');
            parent.find('.image').html('');

            parent.removeClass('assigned');
        });

        $(".list-of-images ul" ).sortable({
            placeholder: "image-moving",
            contaiment:  "parent",
            cancel: ".add-image"
        }).disableSelection();

        // Delete buttons for drop an image from the album
        $('.list-of-images').on('click', '.delete-button', function(event, ui){
            $(this).parents('.image.thumbnail').remove();
            event.preventDefault();
        }).on('click', '.edit-button', function (event, ui) {
            event.preventDefault();
            var id = jQuery(this).data('id');

            var parent = jQuery(this).closest('.image.thumbnail');
            var image_el = parent.find('img');
            var footer_el = parent.children('textarea');

            $("#modal-edit-album-photo input#id_image").val(id);
            $("#modal-edit-album-photo textarea#footer_image").val(footer_el.html());
            $("#modal-edit-album-photo .article-resource-image").find("img").attr('src', image_el.attr("src"));

            $("#modal-edit-album-photo").modal('show');
        });

        jQuery('#title').on('change', function(e, ui) {
            if (tags_input.val().length == 0) {
                fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
            }
        });

    });
    </script>

{/block}

{block name="content"}
<form action="{if isset($album->id)}{url name=admin_album_update id=$album->id}{else}{url name=admin_album_create}{/if}" method="POST" id="formulario">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if isset($album->id)}{t}Editing album{/t}{else}{t}Creating Album{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    {if isset($album->id)}
                        {acl isAllowed="ALBUM_UPDATE"}
                        <button type="submit" id="form-send-button"  name="continue" value="true">
                            <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                        </button>
                        {/acl}
                    {else}
                        {acl isAllowed="ALBUM_CREATE"}
                        <button type="submit" id="form-send-button"  name="continue" value="true">
                            <img src="{$params.IMAGE_DIR}save.png" alt="Guardar y continuar" ><br />{t}Save{/t}
                        </button>
                        {/acl}
                    {/if}
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_albums category=$category}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content contentform">

        {render_messages}

        <div class="form-vertical album-edit-form">

        <div class="contentform-inner clearfix">
            <div class="contentform-main">

                <div class="control-group">
                    <label for="title" class="control-label">{t}Title{/t}</label>
                    <div class="controls">
                        <input type="text" id="title" name="title" value="{$album->title|default:""}" class="input-xxlarge" required="required"/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="agency" class="control-label">{t}Agency{/t}</label>
                    <div class="controls">
                        <input type="text" id="agency" name="agency"
                            value="{$album->agency|clearslash|escape:"html"}" class="input-xlarge"/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="description" class="control-label">{t}Description{/t}</label>
                    <div class="controls">
                        <textarea name="description" id="description" class="onm-editor" data-preset="simple">{$album->description|clearslash}</textarea>
                    </div>
                </div>
            </div>

            <div class="contentbox-container">

                <div class="contentbox">
                    <h3 class="title">{t}Attributes{/t}</h3>
                    <div class="content">
                        <label for="content_status" >{t}Available{/t}</label>
                        <input type="checkbox" value="1" id="content_status" name="content_status" {if $album->content_status eq 1}checked="checked"{/if}>
                        <br/>

                        <h4>{t}Category{/t}</h4>
                        {include file="common/selector_categories.tpl" name="category" item=$album}
                        <br/>

                        <hr class="divisor" style="margin-top:8px;">
                        <h4>{t}Author{/t}</h4>
                        {acl isAllowed="CONTENT_OTHER_UPDATE"}
                            <select name="fk_author" id="fk_author">
                                {html_options options=$authors selected=$album->fk_author}
                            </select>
                        {aclelse}
                            {if !isset($album->author->name)}{t}No author assigned{/t}{else}{$album->author->name}{/if}
                            <input type="hidden" name="fk_author" value="{$album->fk_author}">
                        {/acl}
                    </div>
                </div>

                <div class="contentbox">
                    <h3 class="title">{t}Tags{/t}</h3>
                    <div class="content">
                        <div class="control-group">
                            <div class="controls">
                                <input  type="text" id="metadata" name="metadata" required="required" value="{$album->metadata}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="contentform-main">

                <div class="control-group" id="album-images">
                    <label for="album_photos_id[]" class="control-label"><h5>{t}Album images{/t}</h5></label>
                    <div id="list-of-images" class="list-of-images clearfix controls">
                        <ul>
                            {if !empty($photos)}
                            {foreach from=$photos item=photo key=key name=album_photos}
                            <li class="image thumbnail" id="img{$photo['photo']->pk_photo}">
                                <div class="overlay-image">
                                    <div>
                                        <ul class="image-buttons clearfix">
                                            <li><a href="#"  data-id="{$photo['photo']->pk_photo}" class="edit-button" title="Editar"><i class="icon-pencil"></i></a></li>
                                            <li><a href="#" class="delete-button" title="{t}Drop{/t}"><i class="icon-trash"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <img
                                     src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo['photo']->path_file}{$photo['photo']->name}"
                                     data-id="{$photo['photo']->pk_photo}"
                                     alt="{$photo->name}"/>
                                <textarea name="album_photos_footer[]">{$photo['description']}</textarea>
                                <input type="hidden" name="album_photos_id[]" value="{$photo['id']}">
                            </li><!-- /image -->
                            {/foreach}
                            {/if}
                            <li class="image add-image thumbnail">
                                <a  href="#media-uploader" data-toggle="modal" data-multiselect="true" data-position="list-of-images" title="{t}Add images{/t}"><i class="icon icon-plus"></i></a>
                            </li><!-- /image -->
                        </ul>
                    </div>
                </div>


            </div>

            <div class="contentbox-container">
                <div class="contentbox" >
                    <h3 class="title">{t}Cover image{/t}</h3>
                    <div class="content cover-image {if isset($album) && $album->cover_id}assigned{/if}">
                        <div class="image-data">
                            <a href="#media-uploader" {acl isAllowed='PHOTO_ADMIN'}data-toggle="modal"{/acl} data-position="inner-image" class="image thumbnail">
                                {if !empty($album->cover_id)}
                                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$album->cover}"/>
                                {/if}
                            </a>
                            <div class="article-resource-footer">
                                <input type="hidden" name="album_frontpage_image" value="{$album->cover_id}" class="album-frontpage-image"/>
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

            <input type="hidden" name="id" id="id" value="{$album->id|default:""}" />
        </div><!-- contentform-inner -->
    </div>
</form>
{include file="album/modals/_edit_album_error.tpl"}
{include file="album/modals/_edit_album_photo.tpl"}
{/block}
