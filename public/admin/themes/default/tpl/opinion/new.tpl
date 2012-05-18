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
    table.adminlist img {
        height:auto;
    }
	</style>
{/block}

{block name="footer-js" append}
{script_tag src="/utilsopinion.js"}
{script_tag src="/photos.js"}
{script_tag src="/tiny_mce/opennemas-config.js"}
{script_tag src="/jquery-onm/jquery.inputlength.js"}
<script type="text/javascript">
    countWords(
               document.getElementById('title'),
               document.getElementById('counter_title')
               );

    countWords(
               document.getElementById('body'),
               document.getElementById('counter_body')
               );

    Droppables.add('div_widget', {
        onDrop: function(element) {
                $('widget').src=element.src;
                $('fk_author_img_widget').value=element.id;
        }
    });
    Droppables.add('sel', {
        onDrop: function(element) {
           $('fk_author_img').value=element.id;
           $('seleccionada').src=element.src;
        }
    });

    tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
    OpenNeMas.tinyMceConfig.advanced.elements = "body";
    tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );


    jQuery(document).ready(function ($){
        $('#opinion-form').tabs();
        $('#title').inputLengthControl();
    });
</script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Opinion Manager :: New opinion{/t}</h2></div>
        <ul class="old-button">

            <li>
                <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$opinion->id}', 'formulario');" title="Validar">
                    <img src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                </a>
            </li>
            <li>
                {if isset($opinion->id)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$opinion->id}', 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
                {/if}
                    <img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}"><br />{t}Save and exit{/t}</a>
            </li>
            <li class="separator"></li>
            <li>
                {if $smarty.session.desde eq 'search_advanced'}
                     <a href="/admin/controllers/search_advanced/search_advanced.php?action=search&stringSearch={$smarty.get.stringSearch}">
                        <img src="{$params.IMAGE_DIR}cancel.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" ><br />{t}Cancel{/t}
                     </a>
                {else}
                    <a href="#" onClick="cancel('{$smarty.session.desde}','{$smarty.request.category}','{$smarty.request.page}');" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>

                {/if}
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

        <div id="opinion-form" class="tabs">

            <ul>
                <li>
                    <a href="#edicion-contenido">{t}Opinion content{/t}</a>
                </li>
                <li>
                    <a href="#edicion-extra">{t}Opinion parameters{/t}</a>
                </li>
            </ul>

            <div id="edicion-contenido">
                <table style="width:97%;">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <label for="title">{t}Title{/t}</label>
                                <input type="text" id="title" name="title" title="Titulo de la opinion" onkeyup="countWords(this,document.getElementById('counter_title'))"
                                    value="{$opinion->title|clearslash|escape:"html"}" class="required"  style="width:100%" onBlur="javascript:get_metadata(this.value);" />
                                <input type="hidden" id="category" name="category" title="opinion" value="opinion" />
                            </td>
                            <td rowspan="3">
                                <div class="utilities-conf" style="float:right">
                                    <h3>{t}Opinion parameters{/t}</h3>
                                    <table>
                                        <tr>
                                            <td>
                                                <label for="title">{t}Available:{/t}</label>
                                            </td>
                                            <td>
                                                <select name="available" id="available" class="required">
                                                    <option value="0" {if $opinion->available eq 0} selected {/if}>{t}No{/t}</option>
                                                    <option value="1"  {if $opinion->available eq 1} selected {/if}>{t}Yes{/t}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="title">{t}Put in homepage:{/t}</label>
                                            </td>
                                            <td>
                                                <select name="in_home" id="in_home" class="required">
                                                    <option value="1"  {if $opinion->in_home eq 1} selected {/if}>{t}Yes{/t}</option>
                                                    <option value="0"  {if $opinion->in_home eq 0} selected {/if}>{t}No{/t}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        {is_module_activated name="COMMENT_MANAGER"}
                                        <tr>
                                            <td>
                                                <label for="title">{t}Allow comments:{/t}</label>
                                            </td>
                                            <td>
                                                <select name="with_comment" id="with_comment" class="required">
                                                    <option value="0"  {if $opinion->with_comment eq 0} selected {/if}>{t}No{/t}</option>
                                                    <option value="1" {if $opinion->with_comment eq 1} selected {/if}>{t}Yes{/t}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        {/is_module_activated}
                                        <tr>
                                            <td>
                                                    <label for="title">{t}Title words-count:{/t}</label>
                                            </td>
                                            <td >
                                                <input  type="text" id="counter_title" name="counter_title" title="counter_title"
                                                        value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title'),this)"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                    <label for="title">{t}Body words-count:{/t}</label>
                                            </td>
                                            <td >
                                                    <input type="text" id="counter_body" name="counter_body" title="counter_body"
                                                            value="0" class="required" size="5"  onkeyup="counttiny(document.getElementById('counter_body'));"/>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="title">{t}Type{/t}</label>
                                <select name="type_opinion" id="type_opinion" class="validate-selection"  onChange='show_authors(this.options[this.selectedIndex].value);'>
                                    <option value="-1">{t}-- Pick an author --{/t}</option>
                                    <option value="0" {if $opinion->type_opinion eq 0} selected {/if}>{t}Opinion from author{/t}</option>
                                    <option value="1" {if $opinion->type_opinion eq 1} selected {/if}>{t}Opinion from editorial{/t}</option>
                                    <option value="2" {if $opinion->type_opinion eq 2} selected {/if}>{t}Director's letter{/t}</option>
                                </select>
                            </td>
                            <td>
                                <div id="div_author1"  {if $opinion->type_opinion eq 0} style="display:inline;" {else} style="display:none;"{/if} >
                                    <label for="title">{t}Author{/t}</label>
                                </div>
                                <div id="div_author2" {if $opinion->type_opinion eq 0} style="display:inline;" {else} style="display:none;"{/if}>
                                    <select id="fk_author" name="fk_author" class="validate-selection" onChange='changePhotos(this.options[this.selectedIndex].value);'>
                                        <option value="0" {if isset($author) && $author eq "0"}selected{/if}>{t} - Select one author - {/t}</option>
                                        {section name=as loop=$todos}
                                                <option value="{$todos[as]->pk_author}" {if $opinion->fk_author eq $todos[as]->pk_author}selected{/if}>{$todos[as]->name}</option>
                                        {/section}
                                    </select>
                                </div>
                                <input type="hidden" id="fk_user_last_editor" name="fk_user_last_editor" title="publisher" value="{$publisher|default:""}"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="metadata">{t}Keywords{/t}</label>
                                <input type="text" id="metadata" name="metadata" title="{t}Metadata{/t}" value="{$opinion->metadata|clearslash}" style="width:100%;"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label for="body">{t}Body{/t}</label>
                                <textarea name="body" id="body" title="Opinion" style="width:100%;">{$opinion->body|clearslash}</textarea>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div id="edicion-extra">
                <table>
                    <tbody>
                        <tr>
                            <td colspan="3">
                                <h2>{t}Image selection{/t}</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="min-height:80px; padding:8px;   background-color:#eee">
                                <div id="sel" style="width:100%;">
                                    <b>{t}Inner opinion photo:{/t}</b> <br />
                                    {if !empty($foto->path_img)}
                                        <img src="{$MEDIA_IMG_PATH_URL}{$foto->path_img|default:""}" id="seleccionada"/>
                                    {else}
                                        <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_author.png" id="seleccionada"/>
                                    {/if}
                                    <input type="hidden" id="fk_author_img" name="fk_author_img" value="{$opinion->fk_author_img|default:""}" />
                                </div>
                            </td>
                            <td style="min-height:80px; padding:8px; background-color:#bbb">
                                <div id="div_widget" style="width:100%;">
                                    <b>{t}Widget photo:{/t}</b><br />
                                    {if !empty($fotowidget->path_img)}
                                        <img src="{$MEDIA_IMG_PATH_URL}{$fotowidget->path_img|default:""}" id="widget"/>
                                    {else}
                                        <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_author.png" id="widget"/>
                                    {/if}
                                    <input type="hidden" id="fk_author_img_widget" name="fk_author_img_widget" value="{$opinion->fk_author_img_widget|default:""}" />
                                </div>
                            </td>
                            <td>
                                <b>{t}Choose one available photo for the widget:{/t}</b> <br>
                                <div id="photos" class="photos">
                                     <ul id='thelist'  class="gallery_list">
                                        {section name=as loop=$photos|default:""}
                                        <li>
                                            <img src="{$MEDIA_IMG_PATH_URL}{$photos[as]->path_img|default:""}" id="{$photos[as]->pk_img}" />
                                        </li>

                                          <script type="text/javascript">
                                              new Draggable( '{$photos[as]->pk_img}' ,{ revert:true } );
                                          </script>

                                        {/section}
                                     </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}
