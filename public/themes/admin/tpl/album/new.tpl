{extends file="base/admin.tpl"}

{block name="footer-js" append}
{include file="media_uploader/media_uploader.tpl"}
<script>
    var mediapicker = $('#media-uploader').mediaPicker({
        upload_url: "{url name=admin_image_create category=0}",
        browser_url : "{url name=admin_media_uploader_browser}",
        months_url : "{url name=admin_media_uploader_months}",
        maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
        multi_select: true,
        handlers: {
            'assign_content' : function( event, params ) {
                var mediapicker = $(this).data('mediapicker');


                if (params['position'] == 'cover-image') {
                    console.log(params);
                    var container = $('.cover-image');
                    var image_element = mediapicker.buildHTMLElement(params);
                    var image_data_el = container.find('.image-data');
                    image_data_el.find('.album-frontpage-image').val(params.content.pk_photo);
                    container.addClass('assigned');

                    image_data_el.find('.image').html(image_element);

                } else {
                    var container = $('.list-of-images > ul');
                    var image_element =
                        '<li class="image thumbnail">'+
                        mediapicker.buildHTMLElement(params)+
                        '<textarea name="album_photos_footer[]">'+params.content.description+'</textarea>'+
                        '<input type="hidden" name="album_photos_id[]" value="'+params.content.id+'">'
                        '</li>' ;

                    container.find('.add-image').before(image_element);
                }
            }
        }
    });

    jQuery(document).ready(function($){
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
            var parent = jQuery(this).parents('.image.thumbnail');
            var element = parent.children('img');

            $("#modal-edit-album-photo input#id_image").val( element.attr('id') );

            var footer_text = parent.children('textarea').html();
            $("#modal-edit-album-photo textarea#footer_image").val(footer_text);

            // Change the image information in the edit modalbox
            var article_info = $("#modal-edit-album-photo .article-resource-image-info");
            article_info.find(".image_size").html(element.data("width") + " x "+ element.data("height") + " px");
            article_info.find(".file_size").html(element.data("filesize") + " Kb");
            article_info.find(".created_time").html(element.data("created"));

            $("#modal-edit-album-photo .article-resource-image").find("img").attr('src', element.attr("src"));
            $("#modal-edit-album-photo").modal('show');
        });

        jQuery('#title').on('change', function(e, ui) {
            fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
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
    <div class="wrapper-content">

        {render_messages}

        <div class="form-horizontal album-edit-form panel">

            <div class="control-group">
                <label for="title" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" value="{$album->title|default:""}" class="input-xlarge" required="required"/>
                </div>
            </div>

            <div class="control-group">
                <label for="title" class="control-label">{t}Available{/t}</label>
                <div class="controls">
                    <input type="checkbox" value="1" id="available" name="available" {if $album->available eq 1}checked="checked"{/if}>
                </div>
            </div>

            <div class="control-group">
                <label for="category" class="control-label">{t}Category{/t}</label>
                <div class="controls">
                    {include file="common/selector_categories.tpl" name="category" item=$album}
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
                <label for="author" class="control-label">{t}Author{/t}</label>
                <div class="controls">
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

            <div class="control-group">
                <label for="description" class="control-label">{t}Description{/t}</label>
                <div class="controls">
                    <textarea name="description" id="description"  rows="8" class="input-xxlarge">{t 1=$album->description|clearslash|escape:"html"}%1{/t}</textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Keywords{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" class="input-xlarge"
                         value="{$album->metadata}" />
                    <div class="help-block">{t}List of terms separated by comas{/t}</div>
                </div>
            </div>

            <div class="control-group" id="album-images">
                <label for="album_photos_id[]" class="control-label">{t}Album images{/t}</label>
                <div id="list-of-images" class="list-of-images clearfix controls resource-container">
                    <ul>
                        {if !empty($photos)}
                        {foreach from=$photos item=photo key=key name=album_photos}
                        <li class="image thumbnail">
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
                                 id="img{$photo['photo']->pk_photo}"
                                 data-id="{$photo['photo']->pk_photo}"
                                 data-title="{$photo['photo']->name}"
                                 data-description="{$photo['photo']->description|escape:"html"}"
                                 data-path="{$photo['photo']->path_file}"
                                 data-width="{$photo['photo']->width}"
                                 data-height="{$photo['photo']->height}"
                                 data-filesize="{$photo['photo']->size}"
                                 data-created="{$photo['photo']->created}"
                                 data-tags="{$photo['photo']->metadata}"
                                 data-footer="{$photo['description']|escape:"html"}"
                                 alt="{$photo->name}"/>
                            <textarea name="album_photos_footer[]">{$photo['description']}</textarea>
                            <input type="hidden" name="album_photos_id[]" value="{$photo['id']}">
                        </li><!-- /image -->
                        {/foreach}
                        {/if}
                        <li class="image add-image thumbnail">
                            <a  href="#media-uploader" data-toggle="modal" data-position="list-of-images" title="{t}Add images{/t}"><i class="icon icon-plus"></i></a>
                        </li><!-- /image -->
                    </ul>
                </div>
            </div>

            <div class="control-group">
                <label for="album_frontpage_image" class="control-label">{t}Cover image{/t}</label>
                <div class="controls cover-image {if isset($album) && $album->cover_id}assigned{/if}">
                    <div class="contentbox" style="display:inline-block; width:auto;">
                        <div class="content">
                            <div class="image-data">
                                <a href="#media-uploader" data-toggle="modal" data-position="inner-image" class="image thumbnail">
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
                                <a href="#media-uploader" data-toggle="modal" data-position="cover-image" class="btn btn-small">{t}Set image{/t}</a>
                                <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="id" id="id" value="{$album->pk_album|default:""}" />

    </div>
</form>
{include file="album/modals/_edit_album_error.tpl"}
{include file="album/modals/_edit_album_photo.tpl"}
{/block}
