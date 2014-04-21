{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/onm/jquery.datepicker.js" language="javascript"}
{/block}

{block name="footer-js" append}
{capture assign="language"}{setting name=site_language}{/capture}
{assign var="lang" value=$language|truncate:2:""}
{if !empty($lang)}
    {assign var="js" value="/jquery/jquery_i18n/jquery.ui.datepicker-"|cat:$lang|cat:".js"}
    {script_tag language="javascript" src=$js}
    <script>
    jQuery(document).ready(function() {
        jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "{$lang}" ] );
    });
    </script>
{/if}
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#title').on('change', function(e, ui) {
        fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
    });
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });
});

</script>
{/block}

{block name="content"}
<form id="formulario" name="formulario" action="{if !empty($cover->id)}{url name=admin_cover_update id=$cover->id}{else}{url name=admin_cover_create}{/if}" method="POST"  enctype="multipart/form-data">

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{if !isset($cover->id)}{t}New ePaper{/t}{else}{t}Editing ePaper{/t}{/if}</h2></div>
        <ul class="old-button">
            <li>
                <button type="submit">
                    <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br />{t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_covers category=$category|default:""}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go Back{/t}" alt="{t}Go Back{/t}" ><br />{t}Go Back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="wrapper-content">

    {render_messages}

    <div id="content-wrapper">

        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="title" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" value="{$cover->title|default:""}" required="required" class="input-xxlarge"/>
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Keywords{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" value="{$cover->metadata|default:""}" required="required" class="input-xxlarge"/>
                    <div class="help">{t}List of words separated by commas{/t}.</div>
                </div>
            </div>

            <div class="control-group">
                <label for="price" class="control-label">{t}Price{/t}</label>
                <div class="controls">
                    <input type="number" step="any" id="price" name="price" value="{$cover->price|number_format:2:".":","|default:"0"}" required="required" />
                    <div class="help">{t}Split decimals with a dot{/t}.</div>
                </div>
            </div>

            <div class="control-group">
                <label for="type" class="control-label">{t}Type{/t}</label>
                <div class="controls">
                    <select name="type" id="type" required="required">
                        <option value="0" {if empty($cover) || $cover->type==0}selected{/if}>{t}Item{/t}</option>
                        <option value="1" {if $cover->type==1}selected{/if}>{t}Subscription{/t}</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="category" class="control-label">{t}Category{/t}</label>
                <div class="controls">
                    <select name="category" id="category" required="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                    {section name=as loop=$allcategorys}
                        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                        <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category || $cover->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                        {/acl}
                        {section name=su loop=$subcat[as]}
                            {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                            <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category || $cover->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                            {/acl}
                        {/section}
                    {/section}
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="content_status" class="control-label">{t}Available{/t}</label>
                <div class="controls">
                    <select name="content_status" id="content_status" required="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                        <option value="0" {if $cover->content_status==0}selected{/if}>{t}No{/t}</option>
                        <option value="1" {if empty($cover) || $cover->content_status==1}selected{/if}>{t}Yes{/t}</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="favorite" class="control-label">{t}Favorite{/t}</label>
                <div class="controls">
                    <select name="favorite" id="favorite" required="required" {acl isNotAllowed="KIOSKO_AVAILABLE"} disabled="disabled" {/acl}>
                        <option value="0" {if $cover->favorite==0}selected{/if}>{t}No{/t}</option>
                        <option value="1" {if empty($cover) || $cover->favorite==1}selected{/if}>{t}Yes{/t}</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label for="date" class="control-label">{t}Date{/t}</label>
                <div class="controls">
                    <input type="text" id="date" name="date" value="{$cover->date}" required="required"/>
                </div>
            </div>

            <div class="control-group">
                <label for="date" class="control-label">{t}File{/t}</label>
                <div class="controls">
                    {if is_object($cover)}
                    <div class="thumbnail" style="display:inline-block;">
                        <img src="{$KIOSKO_IMG_URL}{$cover->path}{$cover->name|regex_replace:"/.pdf$/":".jpg"}" title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" />
                    </div>
                    {else}
                    <input type="file" id="file" name="file" required="required" /></div>
                    {/if}
                </div>
            </div>
        </div>

        <input type="hidden" id="id" name="id" value="{$cover->id}" />
    </div><!--fin content-wrapper-->
</form>
{/block}
