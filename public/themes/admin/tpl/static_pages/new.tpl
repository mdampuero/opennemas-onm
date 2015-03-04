{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if isset($page->id)}{url name=admin_staticpages_update id=$page->id}{else}{url name=admin_staticpages_create}{/if}" method="POST" id="formulario">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-file-o"></i>
                        {t}Static Pages{/t}
                    </h4>
                </li>
                <li class="quicklinks hidden-xs">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks hidden-xs">
                    <h5>
                        {if !isset($page->id)}
                            {t}Creating static page{/t}
                        {else}
                            {t}Editing page{/t}
                        {/if}
                    </h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" href="{url name=admin_staticpages}" title="{t}Go back{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                        <button class="btn btn-primary" type="submit">
                            <span class="fa fa-save"></span> {t}Save{/t}
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
                        <label for="name" class="form-label">
                            {t}Title{/t}
                        </label>
                        <div class="controls">
                            <input class="form-control" id="title" name="title" type="text" value="{$page->title|default:""}" maxlength="120" tabindex="1" required="required"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">
                            {t}URL{/t}
                        </label>
                        <span class="help">
                            {t}The slug component in the url{/t}: {$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/slug.html
                        </span>
                        <div class="controls">
                            <input class="form-control" id="slug" name="slug" type="text" value="{$page->slug|default:""}" maxlength="120" tabindex="2" required="required"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="body">
                            {t}Body{/t}
                        </label>
                        {acl isAllowed='PHOTO_ADMIN'}
                            <div class="pull-right">
                                <div class="btn btn-mini" media-picker media-picker-selection="true" media-picker-max-size="5" media-picker-target="summary">
                                    <i class="fa fa-plus"></i>
                                    {t}Insert image{/t}
                                </div>
                            </div>
                        {/acl}
                        <div class="controls">
                            <textarea class="form-control" id="body" name="body" onm-editor onm-editor-preset="standard" rows="10" tabindex="5">{$page->body|default:""}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="grid simple">
                <div class="grid-body">
                    {acl isAllowed="STATIC_PAGE_AVAILABLE"}
                    <div class="form-group">
                        <label for="content_status" class="form-label">{t}Published{/t}</label>
                        <div class="controls">
                            <select name="content_status" id="content_status" tabindex="3">
                                <option value="1"{if isset($page->content_status) && $page->content_status eq 1} selected="selected"{/if}>{t}Yes{/t}</option>
                                <option value="0"{if isset($page->content_status) && $page->content_status eq 0} selected="selected"{/if}>{t}No{/t}</option>
                            </select>
                        </div>
                    </div>
                    {/acl}
                    <div class="form-group">
                        <label class="form-label" class="title">
                            {t}Tags{/t}
                        </label>
                        <div class="controls">
                            <input data-role="tagsinput" id="metadata" name="metadata" required="required" type="text" value="{$page->metadata|clearslash|escape:"html"}"/>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="filter[title]" value="{$smarty.request.filter.title|default:""}" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}

{block name="footer-js" append}
    {javascripts src="@Common/js/jquery/jquery.tagsinput.min.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
<script type="text/javascript">
/* <![CDATA[ */

jQuery(document).ready(function($){
    var previous = null;

    $('#title').on('change', function(e, ui) {
        if (!$('#metadata').val()) {
            fill_tags($('#title').val(), '#metadata', '{url name=admin_utils_calculate_tags}');
        }

        var slugy = jQuery.trim(jQuery('#slug').attr('value'));
        if ((slugy.length <= 0) && (previous!=slugy)) {

            jQuery.ajax({
                url:  "{url name=admin_staticpages_build_slug id=$page->id|default:0}",
                type: "POST",
                data: { action:"buildSlug", id:'{$page->id|default:0}', slug:slugy, title:jQuery('#title').attr('value') },
                success: function(data){
                    jQuery('#slug').attr('value', data);
                    previous = jQuery('#slug').value;
                }
            });
        }
    });
});
/* ]]> */
</script>
{/block}
