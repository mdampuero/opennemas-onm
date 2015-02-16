{extends file="base/admin.tpl"}

{block name="header-css" append}
    {stylesheets src="@AdminTheme/css/parts/specials.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}">
    {/stylesheets}
    <style>
    .thumbnails>li {
        margin:0;
    }
    .thumbnails {
        margin:0;
    }
    </style>
{/block}

{block name="footer-js" append}
    {javascripts src="@AdminTheme/js/onm/content-provider.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
    <script>
    jQuery(document).ready(function($){
        $("#formulario").on("submit", function(e, ui) {
            var els = [];
            jQuery('#column_right').find('ul.content-receiver li').each(function (index, item) {
                els.push({
                    'id' : jQuery(item).data('id'),
                    'content_type': jQuery(item).data('type'),
                    'position': index
                });
            });

            jQuery('#noticias_right_input').val(JSON.stringify(els));

            els = [];

            jQuery('#column_left').find('ul.content-receiver li').each(function (index, item) {
                els.push({
                    'id' : jQuery(item).data('id'),
                    'content_type': jQuery(item).data('type'),
                    'position': index
                });
            });

            jQuery('#noticias_left_input').val(JSON.stringify(els));
        });

        $('#title').on('change', function(e, ui) {
            fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
        });
    });
    </script>
<script>
jQuery(document).ready(function($){
    $('.article_images .unset').on('click', function (e, ui) {
        e.preventDefault();

        var parent = jQuery(this).closest('.contentbox');

        parent.find('.related-element-id').val('');
        parent.find('.image').html('');

        parent.removeClass('assigned');
    });
});
</script>

{/block}

{block name="content"}
<form action="{if $special->id}{url name=admin_special_update id=$special->id}{else}{url name=admin_special_create}{/if}" method="post" name="formulario" id="formulario">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-star fa-lg"></i>
                        {t}Specials{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{if !isset($special->id)}{t}Creating special{/t}{else}{t}Editing special{/t}{/if}</h5>
                </li>
            </ul>
        </div>

        <div class="all-actions pull-right">
            <ul class="nav quick-section">
                <li>
                    <a class="btn btn-link" href="{url name=admin_specials category=$category}">
                        <span class="fa fa-reply"></span>
                    </a>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    {if !is_null($special->id)}
                    {acl isAllowed="SPECIAL_UPDATE"}
                    <button type="submit" class="btn btn-primary">
                        <span class="fa fa-save"></span>
                        {t}Update{/t}
                    </button>
                    {/acl}
                    {else}
                    {acl isAllowed="SPECIAL_CREATE"}
                    <button type="submit" class="btn btn-primary">
                        <span class="fa fa-save"></span>
                        {t}Save{/t}
                    </button>
                    {/acl}
                    {/if}
                </li>
            </ul>
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
                            <input type="text" id="title" name="title" required="required" class="form-control"
                                    value="{$special->title|clearslash|escape:"html"}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subtitle" class="form-label">{t}Subtitle{/t}</label>
                        <div class="controls">
                            <input type="text" id="subtitle" name="subtitle" class="form-control" value="{$special->subtitle|clearslash|escape:"html"}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="slug" class="form-label">{t}Slug{/t}</label>
                        <div class="controls">
                            <input  type="text" id="slug" name="slug" class="form-control"
                                    value="{$special->slug|clearslash|escape:"html"}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">{t}Description{/t}</label>
                        <div class="controls">
                            <textarea name="description" id="description" onm-editor onm-editor-preset="simple">{$special->description|clearslash}</textarea>
                        </div>
                    </div>

                    {include file="special/partials/_load_images.tpl"}

                    {include file="special/partials/_contents_containers.tpl"}

                </div>
            </div>
        </div>
        <div class="col-md-4">

            <div class="grid simple">
                <div class="grid-title">
                    {t}Attributes{/t}
                </div>
                <div class="grid-body">
                    <div class="form-group">
                        <div class="checkbox">
                          <input type="checkbox" name="content_status" id="content_status" value="1" {if $special->content_status eq 1} checked="checked"{/if}>
                          <label for="content_status" class="form-label">
                            {t}Available{/t}
                          </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="metadata" class="form-label">{t}Tags{/t}</label>
                        <span class="help">{t}List of words separated by commas.{/t}</span>
                        <div class="controls">
                            <input  data-role="tagsinput" id="metadata" name="metadata" required="required" type="text" value="{$special->metadata|clearslash|escape:"html"}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category" class="form-label">{t}Category{/t}</label>
                        <div class="controls">
                            {include file="common/selector_categories.tpl" name="category" item=$special}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<input type="hidden" id="noticias_right_input" name="noticias_right_input" value="">
<input type="hidden" id="noticias_left_input" name="noticias_left_input" value="">

</form>
{/block}
