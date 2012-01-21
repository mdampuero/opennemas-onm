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
    $('#comment-edit').tabs();
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
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Comment Manager{/t} :: {t}Editing comment{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'update', '{$comment->id}');">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" ="Guardar y salir" alt="Guardar y salir" ><br />{t}Save and exit{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="confirmar(this, '{$comment->id}');">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />{t}Delete{/t}
                    </a>
                </li>
                <li>
                    {if $comment->content_status == 1}
                        <a href="?id={$comment->id}&amp;action=change_status&amp;status=0&amp;category={$comment->category}" title="Publicar">
                            <img src="{$params.IMAGE_DIR}publish_no.gif" border="0" alt="Publicado" /><br />{t}Unpublish{/t}
                        </a>
                    {else}
                        <a href="?id={$comment->id}&amp;action=change_status&amp;status=1&amp;category={$comment->category}" title="Despublicar">
                            <img src="{$params.IMAGE_DIR}publish.gif" border="0" alt="Pendiente" /><br />{t}Publish{/t}
                        </a>
                    {/if}
                </li>
                <li>
                    <a href="#" class="admin_add" rel="iframe" onmouseover="return escape('<u>V</u>er Noticia');" onclick="preview(this, '{$article->category}','{$article->subcategory}','{$article->id}');">
                        <img border="0" src="{$params.IMAGE_DIR}preview.png" title="Ver Noticia" alt="Ver Noticia" ><br />
                        {t}See article{/t}
                    </a>
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
        <div id="comment-edit" class="tabs">
            <ul>
                <li>
                    <a href="#edicion-contenido">{t}Comment content{/t}</a>
                </li>
            </ul>

            <div id="edicion-contenido">
                <fieldset>
                    <legend>Basic information</legend>
                    <div style="display:inline-block; width:80%">
                        <label for="title">{t}Title{/t}</label>
                        <input type="text" id="title" name="title" title="Título de la noticia" onkeyup="countWords(this,document.getElementById('counter_title'))" value="{$comment->title|clearslash|escape:"html"}" class="required" style="width:97%" />
                        <input type="hidden" id="fk_content" name="fk_content" title="pk_article" value="{$comment->fk_content}" />
                    </div><!-- / -->
                    {acl isAllowed="COMMENT_AVAILABLE"}
                    <div style="display:inline-block">
                        <label for="content_status">{t}Published{/t}</label>
                        <select name="content_status" id="content_status" class="required">
                            <option value="1" {if $comment->content_status eq 1} selected {/if}>Si</option>
                            <option value="0" {if $comment->content_status eq 0} selected {/if}>No</option>
                        </select>
                    </div><!-- / -->
                    {/acl}
                </fieldset>

                <fieldset>
                    <legend>Author information</legend>
                    <div style="display:inline-block;">
                        <label for="title">{t} Author nickname{/t}</label>
                        <input type="text" id="author" name="author" title="author" value="{$comment->author|clearslash}" class="required" />
                    </div><!-- / -->
                    <div style="display:inline-block">
                        <label for="title">{t}Email address{/t}</label><input type="text" id="email" name="email" title="email"
                    value="{$comment->email|clearslash}" class="required" />
                    </div><!-- / -->

                    <div style="display:inline-block">
                        <label for="date">{t}Written on{/t}</label>
                        <input type="text" id="date" name="date" title="author"
                            value="{$comment->created}" class="required" size="20" readonly />
                    </div>
                    <div style="display:inline-block">
                        <label for="title">{t}Sent from IP address{/t}</label>
                        <input type="text" id="ip" name="ip" title="author"
                        value="{$comment->ip}" class="required" size="20" readonly /></td>
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
                        title="comment" style="width:100%; height:20em;">{$comment->body|clearslash}</textarea>
                </fieldset>
            </div>
        </div>
    </div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}
