{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/swfobject.js"}
{/block}

{block name="header-css" append}
{css_tag href="/jquery/colorbox.css" media="screen"}
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
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery.colorbox-min.js"}
    {script_tag src="/onm/jquery.datepicker.js"}
    {script_tag src="/onm/article.js"}
    {script_tag src="/onm/content-provider.js"}
    {script_tag src="/jquery-onm/jquery.inputlength.js"}
    <script>
        var article_urls = {
            preview : '{url name=admin_article_preview}'
        };

        jQuery(document).ready(function($){
            $('#formulario').onmValidate({
                'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
            });

            $('#article-form').tabs();
            $('#title, #title_int, #subtitle').inputLengthControl();
            $('#title_input, #category').on('change', function() {
                var title = $('#title_input');
                var category = $('#category option:selected');
                var metatags = $('#metadata');
                var title_int_element = $('#title_int_input');
                if (title_int_element.val().length == 0) {
                    title_int_element.val(title.val());
                };
                fill_tags(title.val() + " " + category.data('name') + " " + metatags.val(), '#metadata', '{url name=admin_utils_calculate_tags}');
            });
            $('#formulario').on('submit', function(){
                save_related_contents();
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
                {acl isAllowed="ARTICLE_UPDATE"}
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{if isset($article->id)}{t}Update{/t}{else}{t}Save{/t}{/if}
                    </button>
                </li>
                {/acl}

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

    <div class="wrapper-content">

        {render_messages}

        <div id="article-form" class="tabs">

            <ul>
                <li>
                    <a href="#edicion-contenido">{t}Content{/t}</a>
                </li>
                <li>
                    <a href="#edicion-extra">{t}Parameters{/t}</a>
                </li>
                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                <li>
                    <a id="avanced-custom-button" href="#avanced-custom">{t}Customize{/t}</a>
                </li>
                {/is_module_activated}
                <li>
                    <a href="#related-contents">{t}Related contents{/t}</a>
                </li>

            </ul>

            {* Pestaña de edición-contenido*}
            <div id="edicion-contenido">

                <div class="form-vertical" style="position:relative">
                    <div class="utilities-conf" style="width:200px; position:absolute; top:0px; right:0px">
                        {is_module_activated name="COMMENT_MANAGER"}
                        <input type="checkbox" name="with_comment" id="with_comment"  {if (isset($article) && $article->with_comment eq 1)}checked{/if} value=1/>
                        <label for="with_comment">{t}Allow coments{/t}</label>
                        <br/>
                        {/is_module_activated}

                        {acl isAllowed="ARTICLE_AVAILABLE"}
                            <input type="checkbox" name="content_status" id="content_status" {if (isset($article) && $article->content_status eq 1)}checked{/if}  value=1/>
                            <label for="content_status">{t}Available{/t}</label>
                            <br/>
                        {/acl}
                        {acl isAllowed="ARTICLE_FRONTPAGE"}
                            <input type="checkbox"  name="promoted_to_category_frontpage" id="promoted" {if (isset($article) && $article->promoted_to_category_frontpage == true)}checked{/if} value=1/>
                            <label for="promoted">{t}Put in category frontpage{/t}</label>
                            <br/>
                        {/acl}
                        {acl isAllowed="ARTICLE_HOME"}
                            <input type="checkbox" name="frontpage" id="frontpage" {if (isset($article) && $article->frontpage eq '1')} checked {/if} value=1/>
                            <label for="frontpage">{t}Suggested for frontpage{/t}</label>
                        {/acl}
                    </div>

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

                <div class="form-inline-block">
                    <div class="control-group">
                        <label for="category" class="control-label">{t}Section:{/t}</label>
                        <div class="controls">
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
                        </div>
                    </div>

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

                    {is_module_activated name="CRONICAS_MODULES"}
                    <div class="control-group">
                        <label for="agency_bulletin" class="control-label">{t}Newsletter signature{/t}</label>
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

                <div class="form-vertical">
                    <div class="control-group">
                        <label for="subtitle" class="control-label">{t}Pretitle{/t}</label>
                        <div class="controls">
                            <div class="input-append" id="subtitle">
                                <input  type="text" name="subtitle" value="{$article->subtitle|clearslash|escape:"html"}" class="input-xxlarge"/>
                                <span class="add-on"></span>
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="metadata" class="control-label">{t}Keywords{/t}</label>
                        <div class="controls">
                            <input  type="text" id="metadata" name="metadata" required="required" value="{$article->metadata|clearslash|escape:"html"}" class="input-xxlarge"/>
                            <div class="help-block">{t}List of words separated by commas{/t}</div>
                        </div>
                    </div>


                    <div class="control-group">
                        <label for="summary" class="control-label">
                            {t}Summary{/t}
                        </label>
                        <div class="controls">
                            <textarea name="summary" id="summary" class="onm-editor" data-preset="simple">{$article->summary|clearslash|escape:"html"}</textarea>
                        </div>
                    </div>


                    <div class="control-group">
                        <label for="metadata" class="control-label">
                            {t}Body{/t}
                        </label>
                        <div class="controls">
                            <textarea name="body" id="body" class="onm-editor">{$article->body|clearslash}</textarea>
                        </div>
                    </div>
                </div>
                <div id="article_images">
                    {include  file="article/partials/_images.tpl"}
                </div>
            </div>

            {* Pestaña de parámetros de noticia *}
            <div id="edicion-extra">
                <div class="form-vertical">
                    <div class="control-group">
                        <label for="slug" class="control-label">{t}Slug{/t}</label>
                        <div class="controls">
                            <input type="text" id="slug" name="slug" class="input-xxlarge" value="{$article->slug|clearslash}">
                        </div>
                    </div>
                </div>

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
        {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
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
        </div>
    </div>
</form>

{/block}
