{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript">
        jQuery.noConflict();
    </script>
    {script_tag src="/utilsGallery.js"}
{/block}

{block name="header-css" append}
 {css_tag href="/parts/specials.css"}
{/block}

{block name="footer-js" append}
    {script_tag src="/onm/content-provider.js"}
    {script_tag src="/tiny_mce/opennemas-config.js"}

    <script>
    tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
    OpenNeMas.tinyMceConfig.simple.elements = "description";
    tinyMCE.init( OpenNeMas.tinyMceConfig.simple );

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
<form action="{if $special->id}{url name=admin_special_update id=$special->id}{else}{url name=admin_special_create}{/if}" method="post" name="formulario" id="formulario">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Special manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating Special{/t}{else}{t}Editing Special{/t}{/if}</h2></div>
            <ul class="old-button">

                 <li>
                    {acl isAllowed="SPECIAL_CREATE"}
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </button>
                    {/acl}
                </li>
                <li>
                {if isset($special->id)}
                    {acl isAllowed="SPECIAL_UPDATE"}
                    <button type="submit">
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
        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="title" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" required="required" class="input-xxlarge"
                            value="{$special->title|clearslash|escape:"html"}"/>
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" required="required" class="input-xxlarge"
                            value="{$special->metadata|clearslash|escape:"html"}"/>
                    <div class="help-block">{t}List of words separated by words.{/t}</div>
                </div>
            </div>
            <div class="control-group">
                <label for="category" class="control-label">{t}Category{/t}</label>
                <div class="controls">
                    <select name="category" id="category">
                    {section name=as loop=$allcategorys}
                        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                        <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                        {/acl}
                        {section name=su loop=$subcat[as]}
                            {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                            <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                            {/acl}
                        {/section}
                    {/section}
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label for="available" class="control-label">{t}Available{/t}</label>
                <div class="controls">
                    <input type="checkbox" name="avilable" id="available" value="1" {if $special->available eq 1}checked="checked"{/if}>
                </div>
            </div>
            <div class="control-group">
                <label for="subtitle" class="control-label">{t}Subtitle{/t}</label>
                <div class="controls">
                    <input type="text" id="subtitle" name="subtitle" class="input-xxlarge" required="required" />
                </div>
            </div>
            <div class="control-group">
                <label for="slug" class="control-label">{t}Slug{/t}</label>
                <div class="controls">
                    <input  type="text" id="slug" name="slug" class="input-xlarge" required="required"
                            value="{$special->slug|clearslash|escape:"html"}" />
                </div>
            </div>
            <div class="control-group">
                <label for="description" class="control-label">{t}Description{/t}</label>
                <div class="controls">
                    <textarea name="description" id="description" required="required" class="input-xxlarge">{t 1=$special->description|clearslash}%1{/t}</textarea>
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
