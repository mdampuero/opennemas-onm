{extends file="base/admin.tpl"}


{block name="header-css" append}
<style type="text/css">
label {
    display:block;
    color:#666;
    text-transform:uppercase;
}
.utilities-conf label {
    text-transform:none;
}

fieldset {
    border:none;
    border-top:1px solid #ccc;
}
legend {
    color:#666;
    text-transform:uppercase;
    font-size:13px;
    padding:0 10px;
}
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
{script_tag src="/tiny_mce/opennemas-config.js"}

<script>
jQuery(document).ready(function($) {
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
});

tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
OpenNeMas.tinyMceConfig.advanced.elements = "body";
tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );

</script>
{/block}

{block name="content"}
<form action="{if isset($letter->id)}{url name=admin_letter_update id=$letter->id}{else}{url name=admin_letter_create}{/if}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Letter Manager{/t} :: {t}Editing letter{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir" ><br />{t}Save and exit{/t}
                    </button>
                </li>
                <li>
                    <button value="1" name="continue" type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y Continuar" alt="Guardar y salir" ><br />{t}Save and continue{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_letters}"  value="{t}Go back{/t}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" >
                        <br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div id="letter-edit" class="tabs">
            <div id="edicion-contenido">
                    <legend>Basic information</legend>
                    <div style="display:inline-block; width:70%">
                        <label for="title">{t}Title{/t}</label>
                        <input type="text" id="title" name="title" title="Título de la noticia" onkeyup="countWords(this,document.getElementById('counter_title'))" value="{$letter->title|clearslash|escape:"html"}" class="required" style="width:97%" />
                    </div><!-- / -->
                    {acl isAllowed="LETTER_AVAILABLE"}
                    <div style="display:inline-block; width:19%;">
                        <label for="available">{t}Published{/t}</label>
                        <select name="available" id="available" class="required">
                            <option value="1" {if $letter->available eq 1} selected {/if}>Si</option>
                            <option value="0" {if $letter->available eq 0} selected {/if}>No</option>
                        </select>
                    </div><!-- / -->
                    {/acl}
                    <label>Author information</label>
                    <div style="display:inline-block;">
                        <label for="title">{t} Author nickname{/t}</label>
                        <input type="text" id="author" name="author" title="author" value="{$letter->author|clearslash}" class="required" />
                    </div><!-- / -->
                    <div style="display:inline-block">
                        <label for="title">{t}Email{/t}</label><input type="email" id="email" name="email" title="email"
                    value="{$letter->email|clearslash}" class="required" />
                    </div><!-- / -->

                    <div style="display:inline-block">
                        <label for="date">{t}Date{/t}</label>
                        <input type="text" id="created" name="created" title="created"
                            value="{$letter->created}" class="required" size="20" />
                    </div>
                    <div style="display:inline-block">
                        <label for="title">{t}Sent from IP address{/t}</label>
                        <input type="text" id="params[ip]" name="params[ip]" title="author"
                        value="{$letter->ip|default:"127.0.0.1"}" size="20" /></td>
                    </div>

                    <legend>{t}Body{/t}</legend>
                    <textarea name="body" id="body"
                        title="letter" style="width:100%; height:20em;">{$letter->body|clearslash}</textarea>
            </div>
        </div>
    </div>


    <input type="hidden" name="id" id="id" value="{$letter->id|default:""}" />
</form>
{/block}
