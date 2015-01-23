{extends file="base/admin.tpl"}

{block name="header-css" append}
    {stylesheets src="@AdminTheme/js/jquery/jquery_colorpicker/css/colorpicker.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}">
    {/stylesheets}
    <style type="text/css">
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
{javascripts src="@AdminTheme/js/jquery/jquery_colorpicker/js/colorpicker.js"}
    <script type="text/javascript" src="{$asset_url}"></script>
{/javascripts}
<script>
    jQuery(document).ready(function($) {

        var color = $('.colorpicker_viewer');
        var inpt  = $('#color');
        var btn   = $('.onm-button');

        inpt.ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                $(el).val(hex);
                $(el).ColorPickerHide();
            },
            onChange: function (hsb, hex, rgb) {
                inpt.val(hex);
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

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Categories{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{if $category->pk_content_category}{t}Editing category{/t}{else}{t}Creating category{/t}{/if}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" href="{url name=admin_categories}" class="btn btn-link" title="{t}Config categories module{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                        <button type="submit" class="btn btn-primary">
                            <span class="fa fa-save"></span>
                            {t}Save{/t}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="content">

    {render_messages}

    <div class="row">
        <div class="col-md-8">
            <div class="grid simple">
                <div class="grid-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{t}Title{/t}</label>
                        <div class="controls">
                            <input type="text" id="title" name="title" value="{$category->title|default:""}" required="required" class="form-control"/>
                        </div>
                    </div>

                    {if isset($category) && !empty($category->name)}
                    <div class="form-group">
                        <label for="name" class="form-label">{t}Slug{/t}</label>
                        <div class="controls">
                            <input type="text" id="name" name="name" readonly value="{$category->name|clearslash|default:""}"  required="required" class="form-control"/>
                        </div>
                    </div>
                    {/if}



                    <div class="form-group">
                        <label for="subcategory" class="form-label">{t}Subsection of{/t}</label>
                        <div class="controls">
                            <select name="subcategory" required="required">
                                <option value="0" {if !isset($category) || (!empty($category->fk_content_category) || $category->fk_content_category eq '0')}selected{/if}> -- </option>
                                {section name=as loop=$allcategorys}
                                     <option value="{$allcategorys[as]->pk_content_category}" {if isset($category) && ($category->fk_content_category eq $allcategorys[as]->pk_content_category)}selected{/if}>{$allcategorys[as]->title}</option>
                                {/section}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        {capture "websiteColor"}
                            {setting name="site_color"}
                        {/capture}
                        <label for="color" class="form-label">{t}Color{/t}</label>
                        <div class="controls">
                            <input readonly="readonly" size="6" type="text" id="color" name="color" value="{$category->color|default:$smarty.capture.websiteColor|trim}">
                            <div id="colorpicker_viewer" class="colorpicker_viewer" style="background-color:#{$category->color|default:$smarty.capture.websiteColor|trim}"></div>

                            <button class="onm-button">{t}Reset color{/t}</button>
                        </div>
                    </div>

                    {if isset($configurations) && !empty($configurations['allowLogo'])}
                    <div class="form-group">
                        <label for="logo_path" class="form-label">{t}Category logo{/t}</label>
                        <div class="controls">
                            <input type="file" id="logo_path" name="logo_path"  />
                            <br>

                            {if !empty($category->logo_path)}
                                <label>{t}Image logo:{/t}</label>
                                <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/sections/{$category->logo_path}" style="max-width:200px;" >
                            {/if}
                        </div>
                    </div>
                    {/if}


                    {if !empty($subcategorys)}
                    <div class="form-group">
                        <label class="form-label">{t}Subsections{/t}</label>
                        <div class="controls ">
                            <table class="table table-hover table-condensed" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{t}Title{/t}</th>
                                        <th>{t}Internal name{/t}</th>
                                        <th>{t}Type{/t}</th>
                                        <th>{t}In menu{/t}</th>
                                        <th class="right">{t}Actions{/t}</th>
                                    </tr>
                                </thead>
                                {section name=s loop=$subcategorys}
                                <tr>
                                    <td class="left">
                                         {$subcategorys[s]->title}
                                    </td>
                                    <td class="left">
                                         {$subcategorys[s]->name}
                                    </td>
                                    <td class="left">
                                      {if $subcategorys[s]->internal_category eq 3}
                                         <img style="width:20px;" src="{$params.IMAGE_DIR}album.png" alt="Sección de Album" />
                                      {elseif $subcategorys[s]->internal_category eq 5}
                                         <img  style="width:20px;" src="{$params.IMAGE_DIR}video.png" alt="Sección de Videos" />
                                      {else}
                                          <img  style="width:20px;" src="{$params.IMAGE_DIR}advertisement.png" alt="Sección Global" />
                                      {/if}
                                    </td>
                                    <td class="left">
                                        {if $subcategorys[s]->inmenu==1} {t}Yes{/t} {else}{t}No{/t}{/if}
                                    </td>
                                    <td class="right">
                                        <div class="btn-group">
                                            <a class="btn btn-mini" href="{url name=admin_category_show id=$subcategorys[s]->pk_content_category}" title="Modificar">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                {/section}
                            </table>
                        </div>
                    </div>
                    {/if}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="grid simple">
                <div class="grid-body">
                    <div class="form-group">
                        <div class="controls">
                            <input type="checkbox" id="inmenu" name="inmenu" value="1" {if $category->inmenu eq 1} checked="checked"{/if}>
                            <label for="inmenu" class="form-label">{t}Available{/t}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="controls">
                            <input type="checkbox" id="params[inrss]" name="params[inrss]" value="1"
                                {if !isset($category->params['inrss']) || $category->params['inrss'] eq 1}checked="checked"{/if}>
                            <label for="params[inrss]" class="form-label">{t}Show in RSS{/t}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="internal_category" class="form-label">{t}Category available for{/t}</label>
                        <div class="controls">
                            <select name="internal_category" id="internal_category"  required="required">
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
                    </div>
                </div>
            </div>
        </div>
    </div>


</div><!--fin wrapper-content-->
</form>
{/block}
