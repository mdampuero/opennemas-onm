{extends file="base/admin.tpl"}

{block name="footer-js" append}
{include file="media_uploader/media_uploader.tpl"}
<script>
    var mediapicker = $('#media-uploader').mediaPicker({
        upload_url: "{url name=admin_image_create category=0}",
        browser_url : "{url name=admin_media_uploader_browser}",
        months_url : "{url name=admin_media_uploader_months}",
        maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
        // initially_shown: true,
        multi_select: true,
        tags_attached: 'ads',
        handlers: {
            'assign_content' : function( event, params ) {
                var mediapicker = $(this).data('mediapicker');

                var container = $('#related_media').find('.'+params['position']);

                var image_data_el = container.find('.image-data');
                image_data_el.find('.related-element-id').val(params.content.pk_photo);
                container.addClass('assigned');

                if (params.content.type_img == 'swf') {
                    var image_element = mediapicker.getHTMLforSWF(params.content);
                    container.find('.flash-based-warning').show()
                } else {
                    var image_element = mediapicker.buildHTMLElement(params);
                    container.find(".flash-based").hide();
                };

                image_data_el.find('.image').html(image_element);

                // Change the image information to the new one
                container.find(".image_title").html(params.content.filename);
                container.find(".image_size").html(params.content.width + " x "+ params.content.height + " px");
                container.find(".file_size").html(params.content.size + " Kb");
                container.find(".created_time").html(params.content.created);
            }
        }
    });
</script>

    <script>
    jQuery(document).ready(function($){
        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });

        $("#formulario").on("submit", function(event) {

            var frontpage_image =  $(".album-frontpage-image");
            var album_images =  $("#list-of-images .image");
            if (frontpage_image.val() == "") {
                $("#modal-edit-album-errors").modal('show');
                $("#album-contents").tabs('selected',0);
                return false;
            }
            if (album_images.length < 1) {
                $("#modal-edit-album-errors").modal('show');
                $("#album-contents").tabs('selected',1);
                return false;
            };
            return true;
        });

        $( ".list-of-images ul" ).sortable({
            placeholder: "image-moving",
            contaiment:  "parent"
        }).disableSelection();

        $( ".list-of-images ul" ).droppable({
            accept: "#photos_container #photos img",
            drop: function( event, ui ) {
                var image = ui.draggable;
                var parent = $(this);

                parent.append(
                    "<li class=\"image thumbnail\">" +
                        "<div class=\"overlay-image\"><div>" +
                            "<ul class=\"image-buttons\">" +
                                "<li><a href=\"#\" class=\"edit-button\" title=\"{t}Edit{/t}\"><img src=\"{$params.IMAGE_DIR}edit.png\"></a></li>" +
                                "<li><a href=\"#\" class=\"delete-button\" title=\"{t}Drop{/t}\"><img src=\"{$params.IMAGE_DIR}trash.png\"></a></li>" +
                            "</ul>" +
                        "</div></div>" +
                        "<img " +
                             "src=\"" + image.data("url") + "\"" +
                             "id=\"image" + image.data("id") + "\"" +
                             "data-id=\"" + image.data("id") + "\"" +
                             "data-title=\"" + image.data("created") + "\"" +
                             "data-description=\"" + image.data("created") + "\"" +
                             "data-path=\"" + image.data("created") + "\"" +
                             "data-width=\"" + image.data("width") + "\"" +
                             "data-height=\"" + image.data("height") + "\"" +
                             "data-filesize=\"" + image.data("filesize") + "\"" +
                             "data-created=\"" + image.data("created") + "\"" +
                             "data-tags=\"" + image.data("tags") + "\"" +
                             "data-footer=\"" + image.data("description") + "\"" +
                             "alt=\"" + image.data("description") + "\"/>" +
                        "<textarea name=\"album_photos_footer[]\">" + image.data("description") + "</textarea>" +
                        "<input type=\"hidden\" name=\"album_photos_id[]\" value=\"" + image.data("id") + "\">" +
                    "</li>"
                );

                $('.list-of-images .delete-button').on('click', function(event,ui){
                    $(this).parents('.image.thumbnail').remove();
                    event.preventDefault();
                });
            }
        });

        // Delete buttons for drop an image from the album
        $('.list-of-images .delete-button').on('click', function(event, ui){
            $(this).parents('.image.thumbnail').remove();
            event.preventDefault();
        });

        $("#frontpage-image").droppable({
            accept: "#photos_container #photos img",
            drop: function( event, ui ) {
                var image = ui.draggable;
                var parent = $(this);

                if (image.data('type-img') != 'swf') {
                    // Change the image thumbnail to the new one
                    parent.find('.article-resource-image').html("<img src=\"" + image.data("url") + "\" />");
                } else {
                    parent.find('.article-resource-image').html( "<div id=\"flash-container-replace\"><\/div><script> var flashvars = {}; var params = {}; var attributes = {};" +
                        "swfobject.embedSWF(\"" + image.data("url") + image.data("filename")  + "\",  \"flash-container-replace\", \"270\", \"150\", \"9.0.0\", false, flashvars, params, attributes);<\/script>"
                    );
                };

                // Change the image information to the new one
                var article_info = parent.find(".article-resource-image-info");
                article_info.find(".filename").html(image.data("filename"));
                article_info.find(".image_size").html(image.data("width") + " x "+ image.data("height") + " px");
                article_info.find(".file_size").html(image.data("weight") + " Kb");
                article_info.find(".created_time").html(image.data("created"));
                article_info.find(".description").html(image.data("description"));
                article_info.find(".tags").html(image.data("tags"));

                // Change the form values
                var article_inputs = parent.find(".article-resource-footer");
                article_inputs.find("input[type='hidden']").attr('value', image.data("id"));

            }
        });

        $('#frontpage-image .delete-button').on('click', function () {
            var parent = $(this).parent();
            var elementID = parent.find('.album-frontpage-image');

            if (elementID.val() > 0) {
                elementID.data('id', elementID.val());
                elementID.val(null);
                parent.fadeTo('slow', 0.5);
            } else {
                elementID.val(elementID.data('id'));
                parent.fadeTo('slow', 1);
            };
        });

        jQuery('#album-contents').on('click', '.edit-button', function (event, ui) {
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

            <div id="album-images">
                <h5>{t}Images for this album{/t}</h5>
                <div id="album-contents" style="width:590px;display:inline-block;" class="tabs resource-container clearfix">
                    <ul>
                        <li><a href="#list-of-images">{t}Images in this album{/t}</a></li>
                        <li><a href="#frontpage-image">{t}Cover image{/t}</a></li>
                    </ul>
                    <div id="list-of-images" class="list-of-images clearfix">
                        <ul>
                            {if !empty($photos)}
                                {foreach from=$photos item=photo key=key name=album_photos}
                                    <li class="image thumbnail">
                                        <div class="overlay-image">
                                            <div>
                                                <ul class="image-buttons clearfix">
                                                    <li><a href="#"  data-id="{$photo['photo']->pk_photo}" class="edit-button" title="Editar"><img src="{$params.IMAGE_DIR}edit.png"></a></li>
                                                    <li><a href="#" class="delete-button" title="{t}Drop{/t}"><img src="{$params.IMAGE_DIR}trash.png"></a></li>
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
                        </ul>
                    </div><!-- /list-of-images -->
                    <div id="frontpage-image" class="droppable-video-position droppable-position">
                        <div>
                            <a class="delete-button">
                                <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_video2" alt="Eliminar" title="Eliminar" />
                            </a>
                            <div class="clearfix">
                                <div class="thumbnail article-resource-image">
                                    {if !empty($album->cover_id)}
                                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$album->cover}"/>
                                    {else}
                                        <img src="http://placehold.it/290x226" />
                                    {/if}
                                </div>
                                <div class="article-resource-image-info">
                                    <div><label>{t}File name{/t}</label>     <span class="filename">{$album->cover_image->name|default:'default_img.jpg'}</span></div>
                                    <div><label>{t}Creation date{/t}</label> <span class="created_time">{$album->cover_image->created|default:""}</span></div>
                                    <div><label>{t}Description{/t}</label>   <span class="description">{$album->cover_image->description|escape:'html'}</span></div>
                                    <div><label>{t}Tags{/t}</label>          <span class="tags">{$album->cover_image->metadata|default:""}</span></div>
                                    <div><label>{t}Image size{/t}</label>    <span class="image_size">{$album->cover_image->image_size|default:""}</span></div>
                                    <div><label>{t}File size{/t}</label>     <span class="file_size">{$album->cover_image->file_size|default:""}</span></div>
                                </div>
                            </div>
                            <div class="article-resource-footer">
                                <input type="hidden" name="album_frontpage_image" value="{$album->cover_id}" class="album-frontpage-image"/>
                            </div>
                        </div>
                    </div><!-- /inner-video -->
                </div>

            </div><!-- /album-images -->
        </div>

        <input type="hidden" name="id" id="id" value="{$album->pk_album|default:""}" />

    </div>
</form>
{include file="album/modals/_edit_album_error.tpl"}
{include file="album/modals/_edit_album_photo.tpl"}
{/block}
