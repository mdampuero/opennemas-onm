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
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if !isset($article->id)}{t}Creating article{/t}{else}{t}Editing article{/t}{/if}</h2></div>
            <ul class="old-button">
                {if isset($article->id)}
                    {acl isAllowed="ARTICLE_UPDATE"}
                    <li>
                        <button type="submit" name="continue" value="1">
                            <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Update{/t}
                        </button>
                    </li>
                    {/acl}
                {else}
                    {acl isAllowed="ARTICLE_CREATE"}
                    <li>
                        <button type="submit" name="continue" {acl isAllowed="ARTICLE_UPDATE"}value="1"{/acl}
                                                              {acl isNotAllowed="ARTICLE_UPDATE"}value="0"{/acl}>
                            <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                        </button>
                    </li>
                    {/acl}
                {/if}

                <li>
                    <a href="#" accesskey="P" id="button_preview">
                        <img src="{$params.IMAGE_DIR}preview.png" alt="{t}Preview{/t}" /><br />{t}Preview{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{if !empty($smarty.session._from)}{$smarty.session._from}{else}{url name=admin_articles category=$category page=$page}{/if}" title="{t}Cancel{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Cancel{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content contentform">

        {render_messages}

        <div id="article-form" class="tabs">

            <ul>
                <li>
                    <a href="#edicion-contenido">{t}Content{/t}</a>
                </li>
                <li>
                    <a href="#edicion-extra">{t}Parameters{/t}</a>
                </li>
                {is_module_activated name="CRONICAS_MODULES"}
                <li>
                    <a id="avanced-custom-button" href="#avanced-custom">{t}Customize{/t}</a>
                </li>
                {/is_module_activated}
                <li>
                    <a href="#related-contents">{t}Related contents{/t}</a>
                </li>

            </ul>

            <div id="edicion-contenido">

                <div class="contentform-inner clearfix">
                    <div class="contentform-main">
                        <div class="form-vertical">
                            <div class="control-group">
                                <label for="title" class="control-label">{t}Title{/t}</label>
                                <div class="controls">
                                    <div class="input-append" id="title">
                                        <input type="text" name="title" id="title_input" class="input-xxlarge"
                                            value="{$article->title|clearslash|escape:"html"}" maxlength="256" required="required"/>
                                        <span class="add-on"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="title_int" class="control-label">{t}Inner title{/t}</label>
                                <div class="controls">
                                    <div class="input-append" id="title_int">
                                        <input type="text" name="title_int" id="title_int_input" maxlength="256" class="input-xxlarge"
                                                value="{$article->title_int|clearslash|escape:"html"|default:$article->title}" required="required" />
                                        <span class="add-on"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contentbox-container">
                        <div class="contentbox">
                            <h3 class="title">{t}Attributes{/t}</h3>
                            <div class="content">
                                {acl isAllowed="ARTICLE_AVAILABLE"}
                                    <input type="checkbox" name="content_status" id="content_status" {if (isset($article) && $article->content_status eq 1)}checked{/if}  value=1/>
                                    <label for="content_status">{t}Available{/t}</label>
                                    <br/>
                                {/acl}
                                {is_module_activated name="COMMENT_MANAGER"}
                                <input type="checkbox" name="with_comment" id="with_comment"  {if (isset($article) && $article->with_comment eq 1)}checked{/if} value=1/>
                                <label for="with_comment">{t}Allow coments{/t}</label>
                                <br/>
                                {/is_module_activated}
                                <hr class="divisor">
                                {acl isAllowed="ARTICLE_FRONTPAGE"}
                                    <input type="checkbox"  name="promoted_to_category_frontpage" id="promoted" {if (isset($article) && $article->promoted_to_category_frontpage == true)}checked{/if} value=1/>
                                    <label for="promoted">{t}Put in category frontpage{/t}</label>
                                    <br/>
                                {/acl}
                                {acl isAllowed="ARTICLE_HOME"}
                                    <input type="checkbox" name="frontpage" id="frontpage" {if (isset($article) && $article->frontpage eq '1')} checked {/if} value=1/>
                                    <label for="frontpage">{t}Suggested for frontpage{/t}</label>
                                {/acl}
                                <hr class="divisor">
                                <h4>{t}Category{/t}</h4>
                                <select name="category" id="category">
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
                                <br>
                                <hr class="divisor" style="margin-top:8px;">
                                <h4>{t}Author{/t}</h4>
                                {acl isAllowed="CONTENT_OTHER_UPDATE"}
                                    <select name="fk_author" id="fk_author">
                                        {html_options options=$authors selected=$article->fk_author}
                                    </select>
                                {aclelse}
                                    {if !isset($article->fk_author)}
                                        {$smarty.session.realname}
                                        <input type="hidden" name="fk_author" value="{$smarty.session.userid}">
                                    {else}
                                        {$authors[$article->fk_author]}
                                        <input type="hidden" name="fk_author" value="{$article->fk_author}">
                                    {/if}
                                {/acl}
                            </div>
                        </div>

                        <div class="contentbox">
                            <h3 class="title">{t}Tags{/t}</h3>
                            <div class="content">
                                <div class="control-group">
                                    <div class="controls">
                                        <input  type="text" id="metadata" name="metadata" required="required" value="{$article->metadata|clearslash|escape:"html"}"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="contentbox">
                            <h3 class="title"><i class="icon-time"></i> {t}Schedule{/t}</h3>
                            <div class="content">
                                <div class="form-inline-block">
                                    <div class="control-group">
                                        <label for="starttime" class="control-label">{t}Publication start date{/t}</label>
                                        <div class="controls">
                                            <input type="datetime" id="starttime" name="starttime" value="{$article->starttime}">
                                            <div class="help-block">{t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label for="endtime" class="control-label">{t}Publication end date{/t}</label>
                                        <div class="controls">
                                            <input type="datetime" id="endtime" name="endtime" value="{$article->endtime}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {is_module_activated name="PAYWALL"}
                        <div class="contentbox">
                            <h3 class="title">{t}Paywall{/t}</h3>
                            <div class="content">
                                <input type="checkbox" id="only_subscribers" name="params[only_subscribers]" {if $article->params["only_subscribers"] == "1"}checked=checked{/if} value="1">
                                <label for="only_subscribers">{t}Only available for subscribers{/t}</label>

                            </div>
                        </div>
                        {/is_module_activated}

                    </div>

                    <div class="form-inline-block contentform-main">
                        <div class="control-group">
                            <label for="agency" class="control-label">{t}Signature{/t}</label>
                            <div class="controls">
                                <input  type="text" id="agency" name="agency"
                                    {if is_object($article)}
                                        value="{$article->agency|clearslash|escape:"html"}"
                                    {else}
                                        value="{setting name=site_agency}"
                                    {/if} />
                            </div>
                        </div>

                        {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                        <div class="control-group">
                            <label for="agency_bulletin" class="control-label">{t}Signature{/t} #2</label>
                            <div class="">
                                <input  type="text" id="agency_bulletin" name="params[agencyBulletin]"
                                    {if is_object($article)}
                                        value="{$article->params['agencyBulletin']|clearslash|escape:"html"}"
                                    {else}
                                        value="{setting name=site_agency}"
                                    {/if} />
                            </div>
                        </div>
                        {/is_module_activated}
                    </div>

                    <div class="form-vertical contentform-main">
                        <div class="control-group">
                            <label for="subtitle" class="control-label clearfix">{t}Pretitle{/t}</label>
                            <div class="controls">
                                <div class="input-append" id="subtitle">
                                    <input  type="text" name="subtitle" value="{$article->subtitle|clearslash|escape:"html"}" class="input-xxlarge"/>
                                    <span class="add-on"></span>
                                </div>
                            </div>
                        </div>


                        <div class="control-group clearfix">
                            <label for="summary" class="control-label clearfix">
                                <div class="pull-left">
                                    {t}Summary{/t}
                                </div>
                                <div class="pull-right">
                                    {acl isAllowed='PHOTO_ADMIN'}
                                    <a href="#media-uploader" data-toggle="modal" data-position="summary" class="btn btn-mini">{t}Insert image{/t}</a>
                                    {/acl}
                                </div>
                            </label>
                            <div class="controls">
                                <textarea name="summary" id="summary" class="onm-editor" data-preset="simple">
                                    {$article->summary|clearslash|escape:"html"|default:"&nbsp;"}
                                </textarea>
                            </div>
                        </div>

                        <div class="form-vertical">
                            <div class="control-group">
                                <label for="metadata" class="control-label clearfix">
                                    <div class="pull-left">{t}Body{/t}</div>
                                    <div class="pull-right">
                                        {acl isAllowed='PHOTO_ADMIN'}
                                        <a href="#media-uploader" data-toggle="modal" data-position="body" class="btn btn-mini">{t}Insert image{/t}</a>
                                        {/acl}
                                    </div>
                                </label>
                                <div class="controls">
                                    <textarea name="body" id="body" class="onm-editor">{$article->body|clearslash|default:"&nbsp;"}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /contentform-main -->

                <div id="related_media" class="clearfix">
                    {include  file="article/partials/_images.tpl"}
                </div>
            </div><!-- /edicion-contenido -->

            <!-- Parameters -->
            <div id="edicion-extra">
                <div class="form-vertical">
                    <div class="control-group">
                        <label for="slug" class="control-label">{t}Slug{/t}</label>
                        <div class="controls">
                            <input type="text" id="slug" name="slug" class="input-xxlarge" value="{$article->slug|clearslash}">
                            {if $article}
                            {assign var=uri value="\" "|explode:$article->uri}
                            <span class="help-block">&nbsp;{$smarty.const.SITE_URL}{$uri.0|clearslash}</span>
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="form-vertical">
                    <div class="control-group">
                        <label for="bodyLink" class="control-label">{t}External link{/t}</label>
                        <div class="controls">
                            <input type="text" id="bodyLink" name="params[bodyLink]" class="input-xxlarge" value="{$article->params['bodyLink']}">
                        </div>
                    </div>
                </div>
            </div>
            {is_module_activated name="CRONICAS_MODULES"}
            <div id="avanced-custom">
                {include file ="article/partials/_article_avanced_customize.tpl"}
            </div>
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


            <input type="hidden" id="action" name="action" value="{$action}" />
            <input type="hidden" name="id" id="id" value="{$article->id|default:""}" />
        </div><!-- tabs -->
    </div><!-- /wrapper-content contentform -->
</form>
{/block}
