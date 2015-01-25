{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if isset($page->id)}{url name=admin_staticpages_update id=$page->id}{else}{url name=admin_staticpages_create}{/if}" method="POST" id="formulario">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Static Pages{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{if !isset($page->id)}{t}Creating static page{/t}{else}{t}Editing page{/t}{/if}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name=admin_staticpages}" title="{t}Go back{/t}">
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
                        <label for="name" class="form-label">{t}Title{/t}</label>
                        <div class="controls">
                            <input type="text" id="title" name="title" value="{$page->title|default:""}"
                                   maxlength="120" tabindex="1" required="required"  class="input-xlarge"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">{t}URL{/t}</label>
                        <span class="help">{t}The slug component in the url{/t}: {$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/slug.html</span>
                        <div class="controls">
                            <input type="text" id="slug" name="slug" value="{$page->slug|default:""}"
                                   maxlength="120" tabindex="2" required="required"  class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="body" class="form-label clearfix">
                            <div class="pull-left">{t}Body{/t}</div>
                            <div class="pull-right">
                                {acl isAllowed='PHOTO_ADMIN'}
                                <a href="#media-uploader" data-toggle="modal" data-position="body" class="btn btn-mini"> + {t}Insert image{/t}</a>
                                {/acl}
                            </div>
                        </label>
                        <div class="controls">
                            <textarea name="body" id="body" tabindex="5" class="onm-editor form-control" rows="10">{$page->body|default:""}</textarea>
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
                        <label class="form-label" class="title">{t}Tags{/t}</h3>
                        <div class="controls">
                            <input  type="text" id="metadata" name="metadata" required="required" value="{$page->metadata|clearslash|escape:"html"}" class="form-control" />
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

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    var previous = null;

    var tags_input = $('#metadata').tagsInput({ width: '100%', height: 'auto', defaultText: "{t}Write a tag and press Enter...{/t}"});

    $('#title').on('change', function(e, ui) {
        if (tags_input.val().length == 0) {
            fill_tags_improved($('#title').val(), tags_input, '{url name=admin_utils_calculate_tags}');
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
{include file="media_uploader/media_uploader.tpl"}
<script>
    var mediapicker = $('#media-uploader').mediaPicker({
        upload_url: "{url name=admin_image_create category=0}",
        browser_url : "{url name=admin_media_uploader_browser}",
        months_url : "{url name=admin_media_uploader_months}",
        maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
        // initially_shown: true,
        handlers: {
            'assign_content' : function( event, params ) {
                var mediapicker = $(this).data('mediapicker');
                var image_element = mediapicker.buildHTMLElement(params);

                if (params['position'] == 'body') {
                    CKEDITOR.instances.body.insertHtml(image_element);
                }
            }
        }
    });
</script>
{/block}
