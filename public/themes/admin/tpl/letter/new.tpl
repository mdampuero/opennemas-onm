{extends file="base/admin.tpl"}


{block name="footer-js" append}
{script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
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

    load_ajax_in_container('{url name=admin_images_content_provider_gallery category=$category}', $('#photos'));

    $('#stringImageSearch, #category_imag').on('change', function(e, ui) {
        var category = $('#category_imag option:selected').val();
        var text = $('#stringImageSearch').val();
        var url = '{url name=admin_images_content_provider_gallery}?'+'category='+category+'&metadatas='+encodeURIComponent(text);
        load_ajax_in_container(
            url,
            $('#photos')
        );
    });

    $('#photos').on('click', '.pager a', function(e, ui) {
        e.preventDefault();
        var link = $(this);
        load_ajax_in_container(link.attr('href'), $('#photos'));
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

            {include file="special/partials/_load_images.tpl"}

        </div>
    </div>


    <input type="hidden" name="id" id="id" value="{$letter->id|default:""}" />
</form>
{/block}
