{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery-ui-sliderAccess.js"}
    {script_tag src="/jquery/jquery.colorbox-min.js"}
    {script_tag src="/onm/jquery.datepicker.js"}
    {script_tag src="/utilsarticle.js"}
    {script_tag src="/jquery-onm/jquery.article.js"}
    {script_tag src="/utilsGallery.js"}
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
    {script_tag src="/onm/jquery.content-provider.js"}
    {script_tag src="/jquery-onm/jquery.articlerelated.js"}
    {script_tag src="/jquery-onm/jquery.inputlength.js"}
    <script>
    jQuery(document).ready(function($){
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
            fill_tags(title.val() + " " + category.data('name') + " " + metatags.val(), '#metadata');
        })
    });
    </script>

    {script_tag src="/tiny_mce/opennemas-config.js"}
    <script>
        tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );

        {if isset($article) && $article->isClone()}
            OpenNeMas.tinyMceConfig.simple.readonly   = 1;
            OpenNeMas.tinyMceConfig.advanced.readonly = 1;
        {/if}

        OpenNeMas.tinyMceConfig.simple.elements = "summary";
        tinyMCE.init( OpenNeMas.tinyMceConfig.simple );

        OpenNeMas.tinyMceConfig.advanced.elements = "body";
        tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
    </script>
{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Article manager{/t} :: {if isset($article->id)}{t}Creating new article{/t}{else}{t}Editing article{/t}{/if}</h2></div>
            <ul class="old-button">
                {if ($article->content_status eq 0) && ($article->available eq 1)}
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'restore', '{$article->id|default:""}', 'formulario');" onmouseover="return escape('Recuperar');" name="submit_mult" value="noFrontpage">
                        <img src="{$params.IMAGE_DIR}archive_no.png" alt="{t}Restore{/t}"><br />{t}Restore{/t}
                    </a>
                </li>
                {/if}

                {acl isAllowed="ARTICLE_UPDATE"}
                <li>
                    <a href="#" class="admin_add" id="save-button" onClick="save_related_contents();sendFormValidate(this, '_self', 'validate', '{$article->id|default:""}', 'formulario');" title="{t}Save and continue{/t}">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                 {/acl}
                {acl isAllowed="ARTICLE_UPDATE"}
                <li>
                    <a href="#" class="admin_add" id="validate-button" onClick="save_related_contents();sendFormValidate(this, '_self', 'update', '{$article->id|default:""}', 'formulario');" id="button_save">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save and exit{/t}" ><br />{t}Save and exit{/t}
                    </a>
                </li>
                {/acl}

                <li>
                    <a href="#" accesskey="P" id="button_preview">
                        <img src="{$params.IMAGE_DIR}preview.png" alt="{t}Preview{/t}" /><br />{t}Preview{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_articles category=$category page=$page}" title="{t}Cancel{/t}">
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
                {is_module_activated name="COMMENT_MANAGER"}
                {if isset($article) && is_object($article)}
                <li>
                    <a href="#comments">{t}Comments{/t}</a>
                </li>
                {/if}
                {/is_module_activated}
                <li>
                    <a href="#related-contents">{t}Related contents{/t}</a>
                </li>

            </ul>

            {* Pestaña de edición-contenido*}
            <div id="edicion-contenido">
                <table style="margin-bottom:0; width:100%;">
                    <tbody>
                        <tr>
                            <td style="width:75%; vertical-align:top; padding:4px 0;" >
                                <label for="title">{t}Frontpage title:{/t}</label>
                                <div class="input-append"  id="title">
                                    <input type="text" name="title" style="width:90%" id="title_input"
                                       value="{$article->title|clearslash|escape:"html"|default:""}"
                                       class="required span-10" maxlength="256"
                                       tabindex="1"/>
                                    <span class="add-on"></span>
                                </div>
                            </td>
                            <td style="width:20%; text-align:right;"  rowspan="5">
                                <div class="utilities-conf" style="width:99%;">
                                    <div style="text-align:right">
                                        <h3>{t}Options{/t}</h3>
                                        {if $smarty.session.desde != 'list_hemeroteca'}
                                            {is_module_activated name="COMMENT_MANAGER"}
                                            {t}Allow coments{/t}
                                            <input type="checkbox" name="with_comment" id="with_comment"  {if (isset($article) && $article->with_comment eq 1)}checked{/if} value=1/>
                                            <br/>
                                            {/is_module_activated}

                                            {acl isAllowed="ARTICLE_AVAILABLE"}
                                                {t}Available:{/t}
                                                <input type="checkbox" name="content_status" id="content_status" {if (isset($article) && $article->content_status eq 1)}checked{/if}  value=1/>
                                                <br/>
                                            {/acl}
                                            {acl isAllowed="ARTICLE_FRONTPAGE"}
                                                {t}Put in category frontpage:{/t}
                                                <input type="checkbox"  name="promoted_to_category_frontpage" {if (isset($article) && $article->promoted_to_category_frontpage == true)}checked{/if} value=1/>
                                                <br/>
                                            {/acl}
                                            {acl isAllowed="ARTICLE_HOME"}
                                                {t}Suggested for frontpage:{/t}
                                                <input type="checkbox" name="frontpage" id="frontpage" {if (isset($article) && $article->frontpage eq '1')} checked {/if} />
                                            {/acl}
                                        {else} {* else if not list_hemeroteca *}
                                            {t}Archived:{/t}
                                            <input type="checkbox" name="content_status" {if (isset($article) && $article->content_status == 0)}checked{/if} value="0" id="content_status"/>
                                            <br/>
                                            <input type="hidden" id="columns" name="columns"  value="{$article->columns}" />
                                            <input type="hidden" id="home_columns" name="home_columns"  value="{$article->home_columns}" />
                                            <input type="hidden" id="with_comment" name="with_comment"  value="{$article->with_comment}" />
                                            <input type="hidden" id="available" name="available"  value="{$article->available}" />
                                            <input type="hidden" id="promoted_to_category_frontpage" name="promoted_to_category_frontpage"  value="{$article->promoted_to_category_frontpage}" />
                                        {/if}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top; padding:4px 0;">
                                <label for="title_int">{t}Inner title:{/t}</label>
                                <div class="input-append"  id="title_int">
                                    <input type="text" name="title_int" id="title_int_input" style="width:90%"
                                        value="{$article->title_int|clearslash|escape:"html"|default:$article->title}" class="required"
                                        maxlength="256"
                                        tabindex="2"/>
                                    <span class="add-on"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top; padding:4px 0;" >
                                <div style="display:inline-block; width:30%; vertical-align:top;">
                                    <label for="category">{t}Section:{/t}</label>
                                    <select style="width:100%" name="category" id="category" class="validate-section" tabindex="3">
                                        <option value="20" data-name="{t}Unknown{/t}" {if !isset($category)}selected{/if}>{t}Unknown{/t}</option>
                                        {section name=as loop=$allcategorys}
                                            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                            <option value="{$allcategorys[as]->pk_content_category}" data-name="{$allcategorys[as]->title}"
                                            {if (($category == $allcategorys[as]->pk_content_category) && $action == "new") || $article->category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                                            {section name=su loop=$subcat[as]}
                                                {if $subcat[as][su]->internal_category eq 1}
                                                    <option value="{$subcat[as][su]->pk_content_category}" data-name="{$subcat[as][su]->title}"
                                                    {if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} >&nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                                {/if}
                                            {/section}
                                            {/acl}
                                        {/section}
                                    </select>
                                </div><!-- / -->
                                <div style="display:inline-block; width:30%; vertical-align:top;">
                                <label for="agency">{t}Agency{/t}</label>
                                    <input  type="text" id="agency" name="agency" title="{t}Agency{/t}"
                                            style="width:100%" tabindex="4"
                                            {if is_object($article)}
                                                value="{$article->agency|clearslash|escape:"html"}"
                                                onblur="setTimeout(function(){ tinyMCE.get('summary').focus(); }, 200);"
                                            {else}
                                                value="{setting name=site_agency}"
                                            {/if}
                                    />
                                </div><!-- / -->
                                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                                <div style="display:inline-block; width:30%; vertical-align:top;">
                                   <label>{t}Agency in Bulletin{/t}</label>
                                    <input 	type="text" id="agencyWeb" name="params[agencyBulletin]" title="{t}Agency{/t}"
                                    style="width:98%" tabindex="5"
                                    {if is_object($article)}
                                        value="{$article->params['agencyBulletin']|clearslash|escape:"html"}"
                                    {else}
                                        value=""
                                    {/if}
                                 </div>
                                {/is_module_activated}
                            </td>
                        </tr>
                        <tr>
                            <td >
                                <label for="subtitle">{t}Pretitle{/t}</label>
                                <div class="input-append"  id="subtitle">
                                    <input type="text" name="subtitle" style="width:90%"
                                        value="{$article->subtitle|upper|clearslash|escape:"html"}"
                                        tabindex="5"/>
                                    <span class="add-on"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top; padding:4px 0;" >
                                <label for="metadata">{t}Keywords{/t} <small>{t}(Separated by commas){/t}</small></label>
                                <input type="text" id="metadata" name="metadata"
                                   {if isset($article) && is_object($article)}
                                   value="{$article->metadata}"
                                   onChange="search_related('{$article->pk_article|default:""}',$('metadata').value);"
                                   {/if} title="Metadatos" tabindex="6" style="width:100%"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table style="width:100%;">
                    <tbody>
                        <tr>
                            <td>
                                <label for="summary">
                                    {t}Summary{/t}
                                    {if is_object($article)}
                                        <a href="#" onclick="OpenNeMas.tinyMceFunctions.toggle('summary');return false;" title="Habilitar/Deshabilitar editor">
                                            <img src="{$params.IMAGE_DIR}/users_edit.png" alt=""  />
                                        </a>
                                    {/if}
                                </label>
                                <textarea tabindex="6" name="summary" id="summary" title="Resumen de la noticia" style="width:100%; min-height:70px;">{$article->summary|clearslash|escape:"html"}
                                </textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding:0 4px;">
                                <label for="body">{t}Body{/t}
                                {if is_object($article)}
                                    <a href="#" onclick="OpenNeMas.tinyMceFunctions.toggle('body');return false;" title="{t}Enable/disable enhanced editor{/t}">
                                        <img src="{$params.IMAGE_DIR}/users_edit.png" alt=""  />
                                    </a>
                                {/if}
                                </label>
                                <textarea tabindex="7" name="body" id="body" title="Cuerpo de la noticia"
                                        style="width:100%;  height:20em;"
                                        onChange="counttiny(document.getElementById('counter_body'));" >{$article->body|clearslash}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <tbody>
                        <tr style="padding:4px;">
                            <td colspan="2" >
                                <div id="article_images">
                                    {include  file="article/partials/_images.tpl"}
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {* Pestaña de parámetros de noticia *}
            <div id="edicion-extra">
                <table style="width:98%">
                    <tbody>
                        <tr>
                            <td style="padding:4px;">
                                <label for="slug">{t}Slug{/t}</label>
                                <input type="text" id="slug" name="slug" title="{t}slug{/t}"
                                        style="width:98%" maxlength="256" tabindex="5"
                                    {if is_object($article)}
                                            value="{$article->slug|clearslash|escape:"html"}"
                                    {else}
                                            value=""
                                    {/if}/>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:4px;">
                                <div style="width:370px;">
                                    <label for="starttime">{t}Publication start date:{/t}</label>
                                    <input type="text" id="starttime" name="starttime" size="18"
                                           title="Fecha inicio publicaci&oacute;n"
                                           value="{$article->starttime}" tabindex="-1" />
                                </div>
                            </td>
                            <td style="padding:4px;">
                                <div style="width:370px;">
                                    <label for="endtime">{t}Publication end date:{/t}</label>
                                    <input type="text" id="endtime" name="endtime" size="18"
                                           title="Fecha fin publicaci&oacute;n"
                                           value="{$article->endtime}" tabindex="-1" />
                                </div>
                                <sub>{t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</sub>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:4px;">
                                <label for="description">{t}Description{/t}</label>
                                <textarea name="description" id="description"
                                        title="Descripción interna de la noticia" style="width:98%; height:8em;" tabindex="-1">{$article->description|clearslash}</textarea>
                            </td>
                        </tr>
                    </tbody>
            </table>
        </div>
        {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
        <div id="avanced-custom">
            {include file ="article/partials/_article_avanced_customize.tpl"}
        </div>
        {/is_module_activated}

        {is_module_activated name="COMMENT_MANAGER"}
            {if isset($article) && is_object($article)}
                {include file="article/partials/_comments.tpl"}
            {/if}
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
