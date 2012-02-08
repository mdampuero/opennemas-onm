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
<script>
jQuery(document).ready(function($) {
    $('#letter-edit').tabs();
});
</script>

<script>
    countWords(document.getElementById('title'), document.getElementById('counter_title'));
    countWords(document.getElementById('body'), document.getElementById('counter_body'));
</script>

{script_tag src="/tiny_mce/opennemas-config.js"}
<script type="text/javascript" language="javascript">
    tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
</script>

<script type="text/javascript" language="javascript">
    OpenNeMas.tinyMceConfig.advanced.elements = "body";
    tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
</script>
{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Letter Manager{/t} :: {t}Editing letter{/t}</h2></div>
            <ul class="old-button">
                {if empty($letter->id)}
                <li>
                    <button value="create" name="action" type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir" ><br />{t}Save and exit{/t}
                    </button>
                </li>
                {else}
                <li>
                    <button value="update" name="action" type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir" ><br />{t}Save and exit{/t}
                    </button>
                </li>
                {/if}
                <li>
                    <button value="validate" name="action" type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y Continuar" alt="Guardar y salir" ><br />{t}Save and continue{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=list" value="{t}Go back{/t}" title="{t}Go back{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" >
                        <br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        <div id="letter-edit" class="tabs">
            <div id="edicion-contenido">
                <fieldset>
                    <legend>Basic information</legend>
                    <div style="display:inline-block; width:80%">
                        <label for="title">{t}Title{/t}</label>
                        <input type="text" id="title" name="title" title="Título de la noticia" onkeyup="countWords(this,document.getElementById('counter_title'))" value="{$letter->title|clearslash|escape:"html"}" class="required" style="width:97%" />
                    </div><!-- / -->
                    {acl isAllowed="LETTER_AVAILABLE"}
                    <div style="display:inline-block">
                        <label for="available">{t}Published{/t}</label>
                        <select name="available" id="available" class="required">
                            <option value="1" {if $letter->available eq 1} selected {/if}>Si</option>
                            <option value="0" {if $letter->available eq 0} selected {/if}>No</option>
                        </select>
                    </div><!-- / -->
                    {/acl}
                </fieldset>

                <fieldset>
                    <legend>Author information</legend>
                    <div style="display:inline-block;">
                        <label for="title">{t} Author nickname{/t}</label>
                        <input type="text" id="author" name="author" title="author" value="{$letter->author|clearslash}" class="required" />
                    </div><!-- / -->
                    <div style="display:inline-block">
                        <label for="title">{t}Email{/t}</label><input type="text" id="email" name="email" title="email"
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
                        value="{$letter->ip}" class="required" size="20" /></td>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>{t}Statistics{/t}</legend>
                    <div style="display:inline-block">
                        <label for="counter_title">{t}Title{/t}</label>
                        <input type="text" id="counter_title" name="counter_title" title="counter_title" disabled=disabled
                            value="0" onkeyup="countWords(document.getElementById('title'),this)"/>
                    </div>
                    <div style="display:inline-block">
                        <label for="counter_body">{t}Inner title{/t} ({t}words{/t})</label>
                        <input type="text" id="counter_body" name="counter_body" title="counter_body" disabled=disabled
                            value="0" onkeyup="countWords(document.getElementById('title_int'),this)"/>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>{t}Body{/t}</legend>
                    <textarea name="body" id="body"
                        title="letter" style="width:100%; height:20em;">{$letter->body|clearslash}</textarea>
                </fieldset>
            </div>
        </div>
    </div>


    <input type="hidden" name="id" id="id" value="{$letter->id|default:""}" />
</form>
{/block}
