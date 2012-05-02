{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery-ui-sliderAccess.js"}
    {script_tag src="/jquery/jquery.colorbox-min.js"}
    {script_tag src="/onm/jquery.datepicker.js"}
    {script_tag src="/utilsarticle.js"}
    {script_tag src="/jquery-onm/jquery.article.js"}
    {script_tag src="/editables.js"}
    {script_tag src="/utilsGallery.js"}
    {script_tag src="/swfobject.js"}
    {script_tag src="/onm/jquery.content-provider.js"}
{/block}

{block name="header-css" append}
    {css_tag href="/jquery/colorbox.css" media="screen"}
    <style type="text/css">
    label {
        display:block;
        color:#666;
        text-transform:uppercase;
    }
    .utilities-conf label {
        text-transform:none;
    }
    </style>
{/block}

{block name="footer-js" append}
    <script>
    document.observe('dom:loaded', function() {
        if($('title')){
            new OpenNeMas.Maxlength($('title'), {});
            $('title').focus(); // Set focus first element
        }
    });
    jQuery(document).ready(function($){
        $('#article-form').tabs();
        // $("#form-validate-button #form-send-button ul li a#related-contents").on("click", function(event) {
            $( "#content-provider" ).tabs();
            $( "#content-provider" ).tabs('paging', { cycle: false, follow: false, tabsPerPage: 4 } );
        //}
    });

    jQuery(document).ready(function($){

        $("#form-validate-button, #form-send-button").on("click", function(event) {

            saveRelatedContent();
            return true;
        });
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
<form action="#" method="POST" name="formulario" id="formulario">
    {include file="article/partials/_menu.tpl"}
    <div class="wrapper-content">
        {render_messages}
        <div id="article-form" class="tabs">

            <ul>
                <li>
                    <a href="#edicion-contenido">{t}Article content{/t}</a>
                </li>
                <li>
                    <a href="#edicion-extra">{t}Article parameters{/t}</a>
                </li>
                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                <li>
                    <a id="avanced-custom-button" href="#avanced-custom">{t}Article customize{/t}</a>
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
                                <input type="text" id="title" name="title"
                                       title="{t}Title for this new in frontpage{/t}" style="width:100%"
                                       value="{$article->title|clearslash|escape:"html"|default:""}"
                                       class="required" maxlength="256"
                                       onChange="countWords(this,document.getElementById('counter_title'));"
                                       onkeyup="countWords(this,document.getElementById('counter_title'))"
                                       {if is_object($article)}
                                       onChange="search_related('{$article->pk_article}',$('metadata').value);"
                                       {/if}
                                       tabindex="1"/>
                            </td>
                            <td style="width:20%; text-align:right;"  rowspan="5">
                                <div class="utilities-conf" style="width:99%;">
                                    <div style="text-align:right">
                                        <h3>{t}Options{/t}</h3>
                                        {if $smarty.session.desde != 'list_hemeroteca'}
                                            {is_module_activated name="COMMENT_MANAGER"}
                                            {t}Allow coments{/t}
                                            <input type="checkbox" {if (isset($article) && $article->with_comment eq 1)}checked{/if} name="with_comment" id="with_comment" value=1/>
                                            <br/>
                                            {/is_module_activated}

                                            {acl isAllowed="ARTICLE_AVAILABLE"}
                                                {t}Available:{/t}
                                                <input type="checkbox" {if (isset($article) && $article->content_status eq 1)}checked{/if}  name="content_status" id="content_status" value=1/>
                                                <br/>
                                            {/acl}
                                            {*acl isAllowed="ARTICLE_FRONTPAGE"}
                                                {t}Put in section frontpage:{/t}
                                                <input type="checkbox"  name="frontpage" {if (isset($article) && $article->frontpage eq 1)}checked{/if} id="frontpage" value=1/>
                                                <br/>
                                            {/acl}
                                            {acl isAllowed="ARTICLE_HOME"}
                                            {if ($article->in_home neq 1)}
                                                {t}Suggest for frontpage:{/t}
                                                <input type="checkbox"
                                                       name="in_home"
                                                       {if (isset($article) && $article->in_home eq 2)}
                                                           checked
                                                       {/if}
                                                       id="in_home"
                                                       value=2/>
                                                <br/>
                                            {/if}
                                           {/acl*}
                                           <input type="hidden" id="in_home" name="in_home"  value="{$article->in_home|default:"0"}" />
                                           <input type="hidden" id="frontpage" name="frontpage"  value="{$article->frontpage|default:"0"}" />

                                        {else} {* else if not list_hemeroteca *}
                                            {t}Archived:{/t}
                                            <input type="checkbox" name="content_status" {if (isset($article) && $article->content_status == 0)}checked{/if} value="0" id="content_status"/>
                                            <br/>
                                            <input type="hidden" id="columns" name="columns"  value="{$article->columns}" />
                                            <input type="hidden" id="home_columns" name="home_columns"  value="{$article->home_columns}" />
                                            <input type="hidden" id="with_comment" name="with_comment"  value="{$article->with_comment}" />
                                            <input type="hidden" id="available" name="available"  value="{$article->available}" />
                                            <input type="hidden" id="in_home" name="in_home"  value="{$article->in_home}" />
                                        {/if}
                                    </div>

                                    <div style="text-align:right">
                                        <h3>{t}Statistics{/t}</h3>
                                        <div>
                                            {t}Frontpage title:{/t}
                                            <input type="text" id="counter_title" name="counter_title" disabled=disabled
                                                   value="0" onkeyup="countWords(document.getElementById('title'),this)"/> {t}words{/t}
                                        </div><!-- / -->
                                        <div>
                                            {t}Inner title{/t}
                                            <input type="text" id="counter_title_int" name="counter_title_int" disabled=disabled
                                                    value="0" onkeyup="countWords(document.getElementById('title_int'),this)"/> {t}words{/t}
                                        </div><!-- / -->
                                        <div>
                                            {t}Pretitle:{/t}
                                            <input type="text" id="counter_subtitle" name="counter_subtitle" disabled=disabled
                                                    value="0" onkeyup="countWords(document.getElementById('subtitle'),this)"/> {t}words{/t}
                                        </div><!-- / -->
                                        <div>
                                            {t}Summary:{/t}
                                            <input type="text" id="counter_summary" name="counter_summary" disabled=disabled
                                                    value="0"
                                                    onChange="countWords(document.getElementById('summary'),this)"
                                                    onkeyup="countWords(document.getElementById('summary'),this)"/> {t}words{/t}
                                        </div><!-- / -->
                                        <div>
                                            {t}Body:{/t}
                                            <input type="text" id="counter_body" name="counter_body" disabled=disabled
                                                    value="0" size="3" onChange="counttiny(document.getElementById('counter_body'));" onkeyup="counttiny(document.getElementById('counter_body'));"/> {t}words{/t}
                                        </div><!-- / -->
                                    </div><!-- / -->
                                    <script>
                                    document.observe("dom:loaded", function() {
                                        countWords(document.getElementById('title'), document.getElementById('counter_title'));
                                        countWords(document.getElementById('subtitle'), document.getElementById('counter_subtitle'));
                                        countWords(document.getElementById('summary'), document.getElementById('counter_summary'));
                                        countWords(document.getElementById('body'), document.getElementById('counter_body'));
                                    });
                                    </script>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top; padding:4px 0;">
                                <label for="title_int">{t}Inner title:{/t}</label>
                                <input 	type="text" id="title_int" name="title_int" title="{t}Inner title:{/t}"
                                        value="{$article->title_int|clearslash|escape:"html"}" class="required" style="width:100%"
                                        maxlength="256"
                                        onChange="countWords(this,document.getElementById('counter_title_int'));get_tags(this.value);"
                                        onkeyup="countWords(this,document.getElementById('counter_title_int'))"
                                        tabindex="2"/>

                                <script>
                                $('title').observe('blur', function(evt) {
                                    var tituloInt = $('title_int').value.strip();
                                    if( tituloInt.length == 0 ) {
                                            $('title_int').value = $F('title');
                                            get_tags($('title_int').value);
                                    }
                                });
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top; padding:4px 0;" >
                                <div style="display:inline-block; width:30%; vertical-align:top;">
                                    <label for="category">{t}Section:{/t}</label>
                                    <select style="width:100%" name="category" id="category" class="validate-section" onChange="get_tags($('title').value);"  tabindex="3">
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
                                <div style="display:inline-block; width:69%; vertical-align:top;">
                                <label for="agency">{t}Agency{/t}</label>
                                    <input  type="text" id="agency" name="agency" title="{t}Agency{/t}"
                                            class="required" style="width:100%" tabindex="4"
                                            {if is_object($article)}
                                                value="{$article->agency|clearslash|escape:"html"}"
                                                onblur="setTimeout(function(){ tinyMCE.get('summary').focus(); }, 200);"
                                            {else}
                                                value="{setting name=site_agency}"
                                            {/if}
                                    />
                                </div><!-- / -->
                                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                                <div style="display:inline-block; width:69%; vertical-align:top;">
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
                                <input type="text" id="subtitle" name="subtitle" title="antetítulo" style="width:100%"
                                        value="{$article->subtitle|upper|clearslash|escape:"html"}" onChange="countWords(this,document.getElementById('counter_subtitle'))" onkeyup="countWords(this,document.getElementById('counter_subtitle'))" tabindex="5"/>
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
                                <textarea tabindex="6" name="summary" id="summary" title="Resumen de la noticia" style="width:100%; min-height:70px;"
                                          onChange="countWords(this,document.getElementById('counter_summary'))"
                                          onkeyup="countWords(this,document.getElementById('counter_summary'))">{$article->summary|clearslash|escape:"html"}
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
            <input type="hidden" id="relatedFrontpage" name="ordenArti" value="" />
            <input type="hidden" id="relatedInner" name="ordenArtiInt" value="" />

            <input type="hidden" id="params[withGallery]" name="params[withGallery]" value="" />
            <input type="hidden" id="params[withGalleryInt]" name="params[withGalleryInt]" value="" />

            <input type="hidden" id="params[relatedHome]" name="params[relatedHome]" value="" />
            <input type="hidden" id="params[withGalleryHome]" name="params[withGalleryHome]" value="" />
        </div>

        <div id="reloadPreview" style="display: none; background-color: #FFE9AF; color: #666; border: 1px solid #996699; padding: 10px; font-size: 1.1em; font-weight: bold; width: 550px; position: absolute; right: 0; top: 0;">
            <img src="{$params.IMAGE_DIR}loading.gif"  />
            <span id="reloadPreviewText"></span>
        </div>
        <div id="savePreview" style="display: none; background-color: #FFE9AF; color: #666; border: 1px solid #996699; padding: 10px; font-size: 1.1em; font-weight: bold; width: 550px; position: absolute; right: 0; top: 0;">
            <img src="{$params.IMAGE_DIR}btn_filesave.png"  />
            <span id="savePreviewText"></span>
        </div>
            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" name="id" id="id" value="{$article->id|default:""}" />
        </div>
    </div>
</form>
{/block}
