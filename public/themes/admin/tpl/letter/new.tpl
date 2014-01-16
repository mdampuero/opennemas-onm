{extends file="base/admin.tpl"}


{block name="footer-js" append}
{script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
{include file="media_uploader/media_uploader.tpl"}
<script>
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    $('#letter-edit').tabs();

    $('#created').datetimepicker({
        hourGrid: 4,
        showAnim: "fadeIn",
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
        minuteGrid: 10,
        onClose: function(dateText, inst) {
            var endDateTextBox = jQuery('#created');
            if (endDateTextBox.val() != '') {
                var testStartDate = new Date(dateText);
                var testEndDate = new Date(endDateTextBox.val());
                if (testStartDate > testEndDate)
                    endDateTextBox.val(dateText);
            }
            else {
                endDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime){
            var start = jQuery(this).datetimepicker('getDate');
            jQuery('#created').datetimepicker('option', 'minDate', new Date(start.getTime()));
        }
    });

    $('#title').on('change', function(e, ui) {
        fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
    });

    var mediapicker = $('#media-uploader').mediaPicker({
        upload_url: "{url name=admin_image_create category=0}",
        browser_url : "{url name=admin_media_uploader_browser}",
        months_url : "{url name=admin_media_uploader_months}",
        maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
        // initially_shown: true,
        handlers: {
            'assign_content' : function( event, params ) {
                var mediapicker = $(this).data('mediapicker');
                if (params['position'] == 'body' || params['position'] == 'summary') {
                    var image_element = mediapicker.buildHTMLElement(params);
                    CKEDITOR.instances[params['position']].insertHtml(image_element, true);
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
});
</script>
{/block}

{block name="content"}
<form action="{if isset($letter->id)}{url name=admin_letter_update id=$letter->id}{else}{url name=admin_letter_create}{/if}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if isset($letter->id)}{t}Editing letter{/t}{else}{t}Creating letter{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button value="1" name="continue" type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" alt="Guardar y salir" ><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_letters}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" >
                        <br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="title" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" value="{$letter->title|clearslash|escape:"html"}" required="required" class="input-xxlarge" />
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Metadata{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" required="required" class="input-xxlarge"
                            value="{$letter->metadata|clearslash|escape:"html"}"/>
                    <div class="help-block">{t}List of words separated by words.{/t}</div>
                </div>
            </div>

            {acl isAllowed="LETTER_AVAILABLE"}
            <div class="control-group">
                <label for="available" class="control-label">{t}Published{/t}</label>
                <div class="controls">
                    <select name="available" id="available" required="required">
                        <option value="1" {if $letter->available eq 1} selected {/if}>Si</option>
                        <option value="0" {if $letter->available eq 0} selected {/if}>No</option>
                    </select>
                </div>
            </div>
            {/acl}

            <div class="control-group">
                <label class="control-label">{t}Author information{/t}</label>
                <div class="controls">
                    <div class="form-inline-block">
                        <div class="control-group">
                            <label for="author" class="control-label">{t}Nickname{/t}</label>
                            <input type="text" id="author" name="author" value="{$letter->author|clearslash}" required="required" class="input-xlarge" />
                        </div>

                        <div class="control-group">
                            <label for="email" class="control-label">{t}Email{/t}</label>
                            <div class="controls">
                                <input type="email" id="email" name="email" value="{$letter->email|clearslash}" required="required" class="input-xlarge" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="control-group">
                <label for="params[ip]" class="control-label">{t}Sent from IP{/t}</label>
                <div class="controls">
                    <input type="text" id="params[ip]" name="params[ip]" value="{$letter->ip}"/>
                </div>
            </div>

            <div class="control-group">
                <label for="created" class="control-label">{t}Created at{/t}</label>
                <div class="controls">
                    <input type="text" id="created" name="created" value="{$letter->created}"class="input-xxlarge" />
                </div>
            </div>

            <div class="control-group">
                <label for="body" class="control-label">{t}Body{/t}</label>
                <div class="controls">
                    <textarea name="body" id="body"   class="onm-editor">{$letter->body|clearslash}</textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="url" class="control-label">{t}Related url{/t}</label>
                <div class="controls">
                    <input type="text" id="url" name="url" value="{$letter->url}" class="input-xxlarge"/>
                </div>
            </div>

            {acl isAllowed='IMAGE_ADMIN'}
            {is_module_activated name="IMAGE_MANAGER"}
            <div id="related_media" class="control-group">
                <label for="special-image" class="control-label">{t}Image for Special{/t}</label>
                <div class="controls">
                    <ul class="related-images thumbnails">
                        <li class="contentbox frontpage-image {if isset($photo1) && $photo1->name}assigned{/if}">
                            <h3 class="title">{t}Frontpage image{/t}</h3>
                            <div class="content">
                                <div class="image-data">
                                    <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="image thumbnail">
                                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}"/>
                                    </a>
                                    <input type="hidden" name="img1" value="{$special->img1|default:""}" class="related-element-id" />
                                </div>

                                <div class="not-set">
                                    {t}Image not set{/t}
                                </div>

                                <div class="btn-group">
                                    <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="btn btn-small">{t}Set image{/t}</a>
                                    <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            {/is_module_activated}
            {/acl}


        </div>
    </div>


    <input type="hidden" name="id" id="id" value="{$letter->id|default:""}" />
</form>
{/block}
