{extends file="base/admin.tpl"}

{block name="header-js" append}
    {javascripts src="@AdminTheme/js/swfobject.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
{/block}

{block name="header-css" append}
    {stylesheets src="@AdminTheme/css/jquery/colorbox.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}" media="screen">
    {/stylesheets}
<style type="text/css">
    div#content-provider .content-provider-block .content-provider-element {
        margin: 5px;
        border: 1px solid #AAA;
        padding: 5px;
        background:
        white;
    }
    .content-provider-element .content-action-buttons,
    .content-provider-element input[type="checkbox"] {
        display:none;
    }
</style>
{/block}

{block name="footer-js" append}
    {javascripts src="@AdminTheme/js/onm/jquery.datepicker.js,
        @AdminTheme/js/jquery/jquery-ui-timepicker-addon.js,
        @AdminTheme/js/jquery/jquery.colorbox-min.js,
        @AdminTheme/js/onm/article.js,
        @AdminTheme/js/onm/content-provider.js,
        @AdminTheme/js/jquery-onm/jquery.inputlength.js,
        @Common/js/jquery/jquery.tagsinput.min.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
    <script>
        var article_urls = {
            preview : '{url name=admin_article_preview}',
            get_preview : '{url name=admin_article_get_preview}'
        };

        jQuery(document).ready(function($){
            $('#formulario').onmValidate({
                'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
            });

            $('#article-form').tabs();
            $('#title, #title_int, #subtitle').inputLengthControl();
            var tags_input = $('#metadata').tagsInput({ width: '100%', height: 'auto', defaultText: "{t}Write a tag and press Enter...{/t}"});

            $('#title_input, #category').on('change', function() {
                var title = $('#title_input');
                var category = $('#category option:selected');
                var metatags = $('#metadata');
                var title_int_element = $('#title_int_input');
                if (title_int_element.val().length == 0) {
                    title_int_element.val(title.val());
                };
                if (tags_input.val().length == 0) {
                    fill_tags_improved(title.val() + " " + category.data('name') + " " + metatags.val(), tags_input, '{url name=admin_utils_calculate_tags}');
                }
            });
            $('#formulario').on('submit', function(){
                save_related_contents();
            });
        });
    </script>
    {include file="media_uploader/media_uploader.tpl"}
    <script>
    jQuery(document).ready(function($){
        var mediapicker = $('#media-uploader').mediaPicker({
            upload_url: "{url name=admin_image_create category=0}",
            browser_url : "{url name=admin_media_uploader_browser}",
            months_url : "{url name=admin_media_uploader_months}",
            maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
            // initially_shown: true,
            handlers: {
                'assign_content' : function( event, params ) {
                    var mediapicker = $(this).data('mediapicker');
                    if (params['position'] == 'body' || params['position'] == 'summary') {
                        var image_element = mediapicker.buildHTMLElement(params);
                        CKEDITOR.instances[params['position']].insertHtml(image_element, true);
                    } else {
                        var container = $('#related_media').find('.'+params['position']);
                        var image_element = mediapicker.buildHTMLElement(params, true);

                        var image_data_el = container.find('.image-data');
                        image_data_el.find('.related-element-id').val(params.content.pk_photo);
                        image_data_el.find('.related-element-footer').val(params.content.description);
                        image_data_el.find('.image').html(image_element);
                        container.addClass('assigned');
                    };
                }
            }
        });
    });
    </script>
{/block}

{block name="content"}
<form action="{if isset($article->id)}{url name=admin_article_update id=$article->id}{else}{url name=admin_article_create}{/if}" method="POST" name="formulario" id="formulario">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-file-text-o"></i>
                            {t}Articles{/t}
                        </h4>
                    </li>
                    <li class="quicklinks seperate">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>
                            {if !isset($article->id)}{t}Creating article{/t}{else}{t}Editing article{/t}{/if}
                        </h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_articles}" title="{t}Go back{/t}">
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <a href="#" accesskey="P" id="button_preview" class="btn btn-white">
                                <i class="fa fa-desktop"></i> {t}Preview{/t}
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        {if isset($article->id)}
                            {acl isAllowed="ARTICLE_UPDATE"}
                                <li class="quicklinks">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-save"></i>
                                        {t}Update{/t}
                                    </button>
                                </li>
                            {/acl}
                        {else}
                            {acl isAllowed="ARTICLE_CREATE"}
                                <li class="quicklinks">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-save"></i>
                                        {t}Save{/t}
                                    </button>
                                </li>
                            {/acl}
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="btn btn-primary" media-picker media-picker-explore-url="/ws/contents" media-picker-selection="true" media-picker-max-size="5" media-picker-upload-url="{url name=admin_image_create category=0}">
            Media picker
        </div>
        {render_messages}
        <div class="row">
            <div class="col-md-8">
                <div class="grid simple">
                    <div class="grid-title">
                        <h4>{t}Content{/t}</h4>
                    </div>
                    <div class="grid-body">
                        <div class="form-group">
                            <label class="form-label" for="title">
                                {t}Title{/t}
                            </label>
                            <div class="controls">
                                <div class="input-group" id="title">
                                    <input class="form-control" id="title_input" maxlength="256" name="title" required="required" type="text" value="{$article->title|clearslash|escape:"html"}"/>
                                    <span class="input-group-addon"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="title_int">
                                {t}Inner title{/t}
                            </label>
                            <div class="controls">
                                <div class="input-group" id="title_int">
                                    <input class="form-control" id="title_int_input" maxlength="256" type="text" name="title_int" value="{$article->title_int|clearslash|escape:"html"|default:$article->title}" required="required" />
                                    <span class="input-group-addon"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="form-label" for="agency">
                                    {t}Signature{/t}
                                </label>
                                <div class="controls">
                                    <input class="form-control" id="agency" name="agency" type="text"
                                        {if is_object($article)}
                                            value="{$article->agency|clearslash|escape:"html"}"
                                        {else}
                                            value="{setting name=site_agency}"
                                        {/if} />
                                </div>
                            </div>
                            {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                                <div class="form-group col-sm-4">
                                    <label class="form-label" for="agency_bulletin">
                                        {t}Signature{/t} #2
                                    </label>
                                    <div class="controls">
                                        <input class="form-control" id="agency_bulletin" name="params[agencyBulletin]" type="text"
                                            {if is_object($article)}
                                                value="{$article->params['agencyBulletin']|clearslash|escape:"html"}"
                                            {else}
                                                value="{setting name=site_agency}"
                                            {/if} />
                                    </div>
                                </div>
                            {/is_module_activated}
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="subtitle">
                                {t}Pretitle{/t}
                            </label>
                            <div class="controls">
                                <div class="input-group" id="subtitle">
                                    <input class="form-control" name="subtitle" type="text" value="{$article->subtitle|clearslash|escape:"html"}"/>
                                    <span class="input-group-addon"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label clearfix" for="summary">
                                <div class="pull-left">
                                    {t}Summary{/t}
                                </div>
                            </label>
                            {acl isAllowed='PHOTO_ADMIN'}
                                <div class="pull-right">
                                    <a href="#media-uploader" data-toggle="modal" data-position="summary" class="btn btn-mini">{t}Insert image{/t}</a>
                                </div>
                            {/acl}
                            <div class="controls">
                                <textarea class="onm-editor form-control" data-preset="simple" id="summary" name="summary" rows="5">{$article->summary|clearslash|escape:"html"|default:"&nbsp;"}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label clearfix" for="metadata">
                                <div class="pull-left">{t}Body{/t}</div>
                            </label>
                            {acl isAllowed='PHOTO_ADMIN'}
                                <div class="pull-right">
                                    <a href="#media-uploader" data-toggle="modal" data-position="body" class="btn btn-mini">{t}Insert image{/t}</a>
                                </div>
                            {/acl}
                            <div class="controls">
                                <textarea name="body" id="body" class="onm-editor form-control" rows="15">{$article->body|clearslash|default:"&nbsp;"}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="grid simple">
                            <div class="grid-title">
                                <h4>{t}Attributes{/t}</h4>
                            </div>
                            <div class="grid-body">
                                <div class="form-group">
                                    {acl isAllowed="ARTICLE_AVAILABLE"}
                                        <div class="checkbox">
                                            <input id="content_status" name="content_status" {if (isset($article) && $article->content_status eq 1)}checked{/if}  value="1" type="checkbox"/>
                                            <label for="content_status">
                                                {t}Available{/t}
                                            </label>
                                        </div>
                                    {/acl}
                                    {is_module_activated name="COMMENT_MANAGER"}
                                        <div class="checkbox">
                                            <input {if (!isset($article) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($article) && $article->with_comment eq 1)}checked{/if} id="with_comment" name="with_comment" type="checkbox" value="1"/>
                                            <label class="form-label" for="with_comment">
                                                {t}Allow coments{/t}
                                            </label>
                                        </div>
                                    {/is_module_activated}
                                </div>
                                <div class="form-group">
                                    {acl isAllowed="ARTICLE_FRONTPAGE"}
                                        <div class="checkbox">
                                            <input {if (isset($article) && $article->promoted_to_category_frontpage == true)}checked{/if} id="promoted" name="promoted_to_category_frontpage" type="checkbox" value="1"/>
                                            <label class="form-label" for="promoted">
                                                {t}Put in category frontpage{/t}
                                            </label>
                                        </div>
                                    {/acl}
                                    {acl isAllowed="ARTICLE_HOME"}
                                        <div class="checkbox">
                                            <input {if (isset($article) && $article->frontpage eq '1')} checked {/if}  id="frontpage" name="frontpage" type="checkbox" value="1"/>
                                            <label class="form-label" for="frontpage">
                                                {t}Suggested for frontpage{/t}
                                            </label>
                                        </div>
                                    {/acl}
                                </div>
                                {acl isAllowed="CONTENT_OTHER_UPDATE"}
                                    <div class="form-group">
                                        <label class="form-label" for="fk_author">
                                            {t}Author{/t}
                                        </label>
                                        <div class="controls">
                                            <select id="fk_author" name="fk_author">
                                                {html_options options=$authors selected=$article->fk_author}
                                            </select>
                                        </div>
                                    </div>
                                {aclelse}
                                    {if !isset($article->fk_author)}
                                        {$smarty.session.realname}
                                        <input type="hidden" name="fk_author" value="{$smarty.session.userid}">
                                    {else}
                                        {$authors[$article->fk_author]}
                                        <input type="hidden" name="fk_author" value="{$article->fk_author}">
                                    {/if}
                                {/acl}
                                <div class="form-group">
                                    <label class="form-label" for="category">
                                        {t}Category{/t}
                                    </label>
                                    <div class="controls">
                                        <select id="category" name="category">
                                            {section name=as loop=$allcategorys}
                                                {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                                <option value="{$allcategorys[as]->pk_content_category}" data-name="{$allcategorys[as]->title}"
                                                    {if $allcategorys[as]->inmenu eq 0} class="unavailable" {/if}
                                                    {if (($category == $allcategorys[as]->pk_content_category) && !is_object($article)) || $article->category eq $allcategorys[as]->pk_content_category}selected{/if}>
                                                        {$allcategorys[as]->title}</option>
                                                {/acl}
                                                {section name=su loop=$subcat[as]}
                                                    {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                                    {if $subcat[as][su]->internal_category eq 1}
                                                        <option value="{$subcat[as][su]->pk_content_category}" data-name="{$subcat[as][su]->title}"
                                                        {if $subcat[as][su]->inmenu eq 0} class="unavailable" {/if}
                                                        {if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} >
                                                        &nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                                    {/if}
                                                    {/acl}
                                                {/section}
                                            {/section}
                                            <option value="20" data-name="{t}Unknown{/t}" class="unavailable" {if ($category eq '20')}selected{/if}>{t}Unknown{/t}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="metadata">
                                        {t}Tags{/t}
                                    </label>
                                    <div class="controls">
                                        <input class="form-control" id="metadata" name="metadata" required="required" type="text" value="{$article->metadata|clearslash|escape:"html"}"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="slug">
                                        {t}Slug{/t}
                                    </label>
                                    <div class="controls">
                                        <input class="form-control" id="slug" name="slug" type="text" value="{$article->slug|clearslash}">
                                        {if $article}
                                            {assign var=uri value="\" "|explode:$article->uri}
                                            <span class="help-block">&nbsp;{$smarty.const.SITE_URL}{$uri.0|clearslash}</span>
                                        {/if}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="bodyLink">
                                        {t}External link{/t}
                                    </label>
                                    <div class="controls">
                                        <input class="form-control" id="bodyLink" name="params[bodyLink]" type="text" value="{$article->params['bodyLink']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="grid simple">
                            <div class="grid-title">
                                <h4>{t}Schedule{/t}</h4>
                            </div>
                            <div class="grid-body">
                                <div class="form-group">
                                    <label class="form-label" for="starttime">
                                        {t}Publication start date{/t}
                                    </label>
                                    <div class="controls">
                                        <input class="form-control" id="starttime" name="starttime" type="datetime" value="{$article->starttime}">
                                        <span class="help-block">
                                            {t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="endtime">
                                        {t}Publication end date{/t}
                                    </label>
                                    <div class="controls">
                                        <input class="form-control" id="endtime" name="endtime" type="datetime" value="{$article->endtime}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {is_module_activated name="PAYWALL"}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="grid simple">
                                <div class="grid-title">
                                    <h4>{t}Paywall{/t}</h4>
                                </div>
                                <div class="grid-body">
                                    <div class="checkbox">
                                        <input {if $article->params["only_subscribers"] == "1"}checked=checked{/if} id="only_subscribers" name="params[only_subscribers]" type="checkbox" value="1">
                                        <label for="only_subscribers">
                                            {t}Only available for subscribers{/t}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/is_module_activated}
            </div>
        </div>

        {include  file="article/partials/_images.tpl"}

        {is_module_activated name="CRONICAS_MODULES"}
            {include file ="article/partials/_article_advanced_customize.tpl"}
        {/is_module_activated}

        <div id="related-contents">
            {include file ="article/related/_related_list.tpl"}
            <input type="hidden" id="relatedFrontpage" name="relatedFront" value="" />
            <input type="hidden" id="relatedInner" name="relatedInner" value="" />

            <input type="hidden" id="withGallery" name="params[withGallery]" value="" />
            <input type="hidden" id="withGalleryInt" name="params[withGalleryInt]" value="" />

            <input type="hidden" id="relatedHome" name="relatedHome" value="" />
            <input type="hidden" id="withGalleryHome" name="params[withGalleryHome]" value="" />
        </div>

        <div id="article-form" class="tabs">
            <ul>
                {is_module_activated name="CRONICAS_MODULES"}
                <li>
                    <a id="avanced-custom-button" href="#avanced-custom">{t}Customize{/t}</a>
                </li>
                {/is_module_activated}
                <li>
                    <a href="#related-contents">{t}Related contents{/t}</a>
                </li>
            </ul>





            <input type="hidden" id="action" name="action" value="{$action}" />
            <input type="hidden" name="id" id="id" value="{$article->id|default:""}" />
        </div><!-- tabs -->
    </div><!-- /wrapper-content contentform -->
</form>
{/block}
