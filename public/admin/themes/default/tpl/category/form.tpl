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
    div#match-color {
        cursor: pointer;
    }
    .match_viewer {
        height:28px;
        border: 1px solid #B5B8C8;
        border-right:0 !important;
        width:30px !important;
        display:inline-block;
        border-top-left-radius: 3px;
        border-bottom-left-radius: 3px;
        float: left;
        margin-left:-2px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) inset;
    }
    .onm-button {
        height: 30px;
    }
    </style>
{/block}

{block name="footer-js" append}
    {script_tag src="/jquery/jquery_colorpicker/js/colorpicker.js"}
<script>
    try {
        new Validation('formulario', { immediate : true });
    } catch(e) { }

    jQuery(document).ready(function($) {

        var color = $('.colopicker_viewer');
        var inpt  = $('#site_color, #colopicker_viewer');
        var btn   = $('.onm-button');

        inpt.ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                $(el).val(hex);
                $(el).ColorPickerHide();
            },
            onChange: function (hsb, hex, rgb) {
                color.css('background-color', '#' + hex);
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
            }
        })
        .bind('keyup', function(){
            $(this).ColorPickerSetColor(this.value);
        });

        btn.on('click', function(e, ui){
            inpt.val( '{setting name="site_color"}' );
            color.css('background-color', '#' + '{setting name="site_color"}');
            e.preventDefault();
        });
    });
</script>
{/block}

{block name="content"}
<form action="{if $category->pk_content_category}{url name=admin_category_update id=$category->pk_content_category}{else}{url name=admin_category_create}{/if}" method="POST" name="formulario" id="formulario" enctype="multipart/form-data">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">

            <div class="title"><h2>{t}Category manager{/t} :: {if $category->pk_content_category}{t}Editing category{/t}{else}{t}Creating new category{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />{t}Save and continue{/t}
                    </button>
                </li>
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />{t}Save and exit{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_categories}" title="{t}Go Back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go Back{/t}" ><br />{t}Go Back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <table class="adminform">
            <tbody>
                <tr>
                    <td colspan=2>
                        <label for="title">{t}Name{/t}</label>
                        <input type="text" id="title" name="title" value="{$category->title|clearslash|default:""}" class="required" style="width:100%" />
                    </td>
                    <td class="right">
                        <input type="checkbox" id="inmenu" name="inmenu" value="1" {if $category->inmenu eq 1} checked="checked"{/if}>
                        <label for="inmenu" style="display:inline-block">{t}Available{/t}</label>
                        <br>
                        <input type="checkbox" id="params[inrss]" name="params[inrss]" value="1"
                            {if !isset($category->params['inrss']) || $category->params['inrss'] eq 1} checked="checked"{/if}>
                        <label for="params[inrss]" style="display:inline-block">{t}Show in rss{/t}</label>
                    </td>
                </tr>


                <tr>
                    <td colspan=2>
                        {if isset($category) && !empty($category->name)}
                            <label for="name">{t}Slug{/t}</label>
                            <input type="text" id="name" name="name" title="slug categoria" readonly
                                  value="{$category->name|clearslash|default:""}" class="required" style="width:90%" />
                        {/if}
                        <br>
                        <label for="params[title]">{t}Page Title {/t}</label>
                        <input type="text" id="params[title]" name="params[title]" title="Título" value="{$category->params['title']}" style="width:90%" />
                    </td>
                    <td>
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

                    </td>
                </tr>

                <tr>
                    <td>
                        {capture "websiteColor"}
                            {setting name="site_color"}
                        {/capture}
                        <label>{t}Color:{/t}</label>
                        <input readonly="readonly" size="6" type="text" id="site_color" name="color" value="{$category->color|default:$smarty.capture.websiteColor|trim}">
                        <div id="colopicker_viewer" class="colopicker_viewer" style="background-color:#{$category->color|default:$smarty.capture.websiteColor|trim}"></div>
                        <br><br>
                        <button class="onm-button" >{t}Reset color{/t}</button>
                    </td>
                    <td>
                        {if isset($configurations) && !empty($configurations['allowLogo'])}
                            <label for="logo_path">{t}Frontpage logo:{/t}</label>
                            <input type="file" id="logo_path" name="logo_path"  />
                            <br>

                            {if !empty($category->logo_path)}
                                <label>{t}Image logo:{/t}</label>
                                <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/sections/{$category->logo_path}" style="max-width:200px;" >
                            {/if}
                        {/if}
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
                                <td class="right">
                                    <div class="btn-group">
                                        <a class="btn btn-mini" href="{url name=admin_category_show id=$subcategorys[s]->pk_content_category}" title="Modificar">
                                            <i class="icon-pencil"></i>
                                        </a>
                                        <a class="btn btn-mini btn-danger" href="{url name=admin_category_show id=$subcategorys[s]->pk_content_category}" title="Eliminar">
                                            <i class="icon-trash icon-white"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            {/section}
                        </table>
                    </td>
                </tr>
                {/if}
            </tbody>
        </table>

    </div><!--fin wrapper-content-->
</form>
{/block}
