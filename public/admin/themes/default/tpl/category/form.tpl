{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/css/colorpicker.css" basepath="/js/jquery/jquery_colorpicker/"}
    <style type="text/css">
    label {
        display:block;
        color:#666;
        text-transform:uppercase;
    }
    #cates td {
        text-align:center;
    }
    td {
        padding:10px;
    }
    .utilities-conf label {
        text-transform:none;
    }
    </style>
{/block}

{block name="header-js" append}
    {script_tag src="/jquery/jquery.min.js"}
    {script_tag src="/jquery/jquery_colorpicker/js/colorpicker.js"}
    {script_tag src="/utilscategory.js"}
    <script type="text/javascript">
    // <![CDATA[
        Sortable.create('subcates',{
            tag:'table',
            dropOnEmpty: true,
            containment:["subcates"],
            constraint:false});
    // ]]>
    </script>
{/block}

{block name="content"}

<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">

            <div class="title"><h2>{t}Category manager{/t} :: {t}Editing category{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="javascript:savePriority();sendFormValidate(this, '_self', 'validate', '{$category->pk_content_category|default:""}', 'formulario');" title="Validar">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                <li>
                {if isset($category->pk_content_category)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$category->pk_content_category|default:""}, 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0, 'formulario');">
                {/if}
                        <img src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />{t}Save and exit{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?desde={$smarty.session.desde}" class="admin_add" title="{t}Go Back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go Back{/t}" alt="{t}Go Back{/t}" ><br />{t}Go Back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        <table class="adminheading">
             <tr>
                 <th>{t}Editing category{/t}</th>
             </tr>
        </table>
        <table class="adminform">
            <tbody>
                <tr>
                    <td colspan="2">
                        <label for="title">{t}Name{/t}</label>
                        <input type="text" id="title" name="title" title="Título" value="{$category->title|clearslash|default:""}"
                            class="required" size="80" />
                    </td>
                    <td rowspan="3">
                        <div class="help-block margin-left-1">
                            <div class="title"><h4>Sections</h4></div>
                            <div class="content">
                                {t}Title for short title name {/t}<br />
                                {t}Internal name for calculate slugs and uri {/t}<br />
                                {t}Title page for the long title used for seo & in title bar, widgets, menues...{/t}
                                {t}If title page empty Opennemas get short title{/t}
                            </div>
                        </div>
                    </td>
                </tr>

                {if isset($category) && !empty($category->name)}
                <tr>
                    <td colspan="2" >
                         <label for="name">{t}Slug{/t}</label>
                        <input type="text" id="name" name="name" title="slug categoria" readonly
                              value="{$category->name|clearslash|default:""}" class="required" size="80" />
                    </td>
                </tr>
                {/if}
                <tr>
                    <td colspan="2">
                        <label for="params[title]">{t}Page Title {/t}</label>

                        <input type="text" id="params[title]" name="params[title]" title="Título" value="{$category->params['title']}"
                             size="80" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <label for="internal_category">{t}Category available for:{/t}</label>
                            <select name="internal_category" id="internal_category" >
                                <option value="1"
                                    {if  (empty($category->internal_category) || $category->internal_category eq 1)} selected="selected"{/if}>{t}All contents{/t}</option>
                                {is_module_activated name="ALBUM_MANAGER"}
                                    <option value="7"
                                        {if isset($category) && ($category->internal_category eq 7)} selected="selected"{/if}>{t}Albums{/t}</option>
                                {/is_module_activated}
                                {is_module_activated name="VIDEO_MANAGER"}
                                    <option value="9"
                                        {if isset($category) && ($category->internal_category eq 9)} selected="selected"{/if}>{t}Video{/t}</option>
                                {/is_module_activated}
                                {is_module_activated name="POLL_MANAGER"}
                                    <option value="11"
                                        {if isset($category) && ($category->internal_category eq 11)} selected="selected"{/if}>{t}Poll{/t}</option>
                                {/is_module_activated}
                                {is_module_activated name="KIOSKO_MANAGER"}
                                    <option value="14"
                                        {if isset($category) && ($category->internal_category eq 14)} selected="selected"{/if}>{t}ePaper{/t}</option>
                                {/is_module_activated}
                                {is_module_activated name="SPECIAL_MANAGER"}
                                    <option value="10"
                                        {if isset($category) && ($category->internal_category eq 10)} selected="selected"{/if}>{t}Special{/t}</option>
                                {/is_module_activated}
                                {is_module_activated name="BOOK_MANAGER"}
                                    <option value="15"
                                        {if isset($category) && ($category->internal_category eq 15)} selected="selected"{/if}>{t}Book{/t}</option>
                                {/is_module_activated}
                            </select>
                        </div>
                    </td>
                    <td>
                        <div>
                            <label>{t}Subsection of:{/t}</label>
                            <select name="subcategory" class="required" size="12" style="height:100px;">
                                <option value="0" {if !isset($category) || (!empty($category->fk_content_category) || $category->fk_content_category eq '0')}selected{/if}> -- </option>
                                {section name=as loop=$allcategorys}
                                     <option value="{$allcategorys[as]->pk_content_category}" {if isset($category) && ($category->fk_content_category eq $allcategorys[as]->pk_content_category)}selected{/if}>{$allcategorys[as]->title}</option>
                                {/section}
                            </select>
                        </div>
                    </td>
                    <td>
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <label for="inmenu">{t}Show in menu:{/t}</label>
                        <input type="checkbox" id="inmenu" name="inmenu"
                               value="{if empty($category->fk_content_category) || $category->inmenu eq 1}1{else}0{/if}"
                            {if empty($category->fk_content_category) || $category->inmenu eq 1} checked="checked"{/if}>
                            {t}If this option is activated this category will be showed in menu{/t}
                    </td>
                </tr>

                {if isset($configurations) && !empty($configurations['allowLogo'])}

                 <tr>
                    <td colspan="3">
                        <label for="logo_path">{t}Frontpage logo:{/t}</label>
                        <input type="file" id="logo_path" name="logo_path"  />
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <label>{t}Image logo:{/t}</label>
                        {if !empty($category->logo_path)}
                            <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/sections/{$category->logo_path}" style="max-width:200px;" >
                        {/if}
                    </td>
                </tr>
                {/if}
                <tr>
                    <td colspan="3">
                        <label for="site_color">{t}Color:{/t}</label>
                        <input readonly="readonly" size="6" type="text" id="site_color" name="site_color" value="{$category->color|default:"0000ff"}">
                        <div class="colopicker_viewer" style="background-color:#{$category->color|default:"0000ff"}"></div>
                    </td>
                </tr>

                {if !empty($subcategorys)}
                <tr>
                    <td colspan="3">
                        <label>{t}Subsections:{/t}</label>
                        <table class="adminlist" id="cates">
                            <thead>
                                <tr>
                                    <th>{t}Title{/t}</th>
                                    <th>{t}Internal name{/t}</th>
                                    <th>{t}Type{/t}</th>
                                    <th>{t}In menu{/t}</th>
                                    <th>{t}Actions{/t}</th>
                                </tr>
                            </thead>
                            {section name=s loop=$subcategorys}
                            <tr>
                                <td>
                                     {$subcategorys[s]->title}
                                </td>
                                <td>
                                     {$subcategorys[s]->name}
                                </td>
                                <td>
                                  {if $subcategorys[s]->internal_category eq 3}
                                     <img style="width:20px;" src="{$params.IMAGE_DIR}album.png" alt="Sección de Album" />
                                  {elseif $subcategorys[s]->internal_category eq 5}
                                     <img  style="width:20px;" src="{$params.IMAGE_DIR}video.png" alt="Sección de Videos" />
                                  {else}
                                      <img  style="width:20px;" src="{$params.IMAGE_DIR}advertisement.png" alt="Sección Global" />
                                  {/if}
                                </td>
                                <td>
                                    {if $subcategorys[s]->inmenu==1} {t}Yes{/t} {else}{t}No{/t}{/if}
                                </td>
                                <td>
                                    <ul class="action-buttons">
                                        <li>
                                            <a href="{$smarty.server.PHP_SELF}?action=read&id={$subcategorys[s]->pk_content_category}" title="Modificar">
                                                <img src="{$params.IMAGE_DIR}edit.png" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" onClick="javascript:confirmar(this, {$subcategorys[s]->pk_content_category});" title="Eliminar">
                                                <img src="{$params.IMAGE_DIR}trash.png" />
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            {/section}
                        </table>
                    </td>
                </tr>
                {/if}
            </tbody>
        </table>
            
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div><!--fin wrapper-content-->
</form>
{/block}

{block name="footer-js" append}
       <script type="text/javascript">
        try {
                // Activar la validación
                new Validation('form_upload', { immediate : true });
        } catch(e) {
                // Escondemos los errores
                //console.log( e );
        }
        //Color Picker jQuery
        $.noConflict();
        jQuery('#site_color').ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                jQuery(el).val(hex);
                jQuery(el).ColorPickerHide();
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('.colopicker_viewer').css('background-color', '#' + hex);
            },
            onBeforeShow: function () {
                jQuery(this).ColorPickerSetColor(this.value);
            }
        })
        .bind('keyup', function(){
            jQuery(this).ColorPickerSetColor(this.value);
        });
    </script>
{/block}
