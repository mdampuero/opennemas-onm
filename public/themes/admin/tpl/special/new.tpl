{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/parts/specials.css"}
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
    {script_tag src="/onm/content-provider.js"}
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

            jQuery('input#noticias_right').val(JSON.stringify(els));

            els = [];

            jQuery('#column_left').find('ul.content-receiver li').each(function (index, item) {

                els.push({
                    'id' : jQuery(item).data('id'),
                    'content_type': jQuery(item).data('type'),
                    'position': index
                });
            });

            jQuery('input#noticias_left').val(JSON.stringify(els));
        });

        $('#title').on('change', function(e, ui) {
            fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
        });
    });
    </script>
{/block}

{block name="content"}
<form action="{if $special->id}{url name=admin_special_update id=$special->id}{else}{url name=admin_special_create}{/if}" method="post" name="formulario" id="formulario">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if !isset($special->id)}{t}Creating special{/t}{else}{t}Editing special{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                {if isset($special->id)}
                    {acl isAllowed="SPECIAL_UPDATE"}
                    <button type="submit" name="continue" value=1>
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                    </button>
                    {/acl}
                {else}
                    {acl isAllowed="SPECIAL_CREATE"}
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                    </button>
                    {/acl}
                {/if}
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_specials category=$category}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
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
                    <input type="text" id="title" name="title" required="required" class="input-xxlarge"
                            value="{$special->title|clearslash|escape:"html"}"/>
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Metadata{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" required="required" class="input-xxlarge"
                            value="{$special->metadata|clearslash|escape:"html"}"/>
                    <div class="help-block">{t}List of words separated by words.{/t}</div>
                </div>
            </div>
            <div class="control-group">
                <label for="category" class="control-label">{t}Category{/t}</label>
                <div class="controls">
                    {include file="common/selector_categories.tpl" name="category" item=$special}
                </div>
            </div>
            <div class="control-group">
                <label for="available" class="control-label">{t}Available{/t}</label>
                <div class="controls">
                    <input type="checkbox" name="available" id="available" value="1" {if $special->available eq 1} checked="checked"{/if}>
                </div>
            </div>
            <div class="control-group">
                <label for="subtitle" class="control-label">{t}Subtitle{/t}</label>
                <div class="controls">
                    <input type="text" id="subtitle" name="subtitle" class="input-xxlarge" value="{$special->subtitle|clearslash|escape:"html"}" />
                </div>
            </div>
            <div class="control-group">
                <label for="slug" class="control-label">{t}Slug{/t}</label>
                <div class="controls">
                    <input  type="text" id="slug" name="slug" class="input-xlarge"
                            value="{$special->slug|clearslash|escape:"html"}" />
                </div>
            </div>
            <div class="control-group">
                <label for="description" class="control-label">{t}Description{/t}</label>
                <div class="controls">
                    <textarea name="description" id="description" class="onm-editor">{t 1=$special->description|clearslash}%1{/t}</textarea>
                </div>
            </div>
            {include file="special/partials/_load_images.tpl"}

            {include file="special/partials/_contents_containers.tpl"}
        </div>
    </div>
    <input type="hidden" id="noticias_right" name="noticias_right" value="">
    <input type="hidden" id="noticias_left" name="noticias_left" value="">

</form>
{/block}
