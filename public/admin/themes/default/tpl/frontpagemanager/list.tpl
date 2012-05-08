{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/bp/screen.css"}
    <!--[if IE]>{css_tag href="/bp/ie.css"}<![endif]-->
    {css_tag href="/frontpagemanager.css"}
    {css_tag href="/jquery/colorbox.css" media="screen"}
{/block}

{block name="footer-js" append}
    {script_tag src="/jquery/jquery.colorbox-min.js"}
    <script>
        var frontpage_messages = {
            remember_save_positions: "{t}Please, remember save positions after finish.{/t}",
            error_tab_content_provider: "{t}Couldn't load this tab. We'll try to fix this as soon as possible.{/t}"
        }
    </script>
    {script_tag src="/jquery-onm/jquery.frontpagemanager.js"}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Frontpage Manager{/t} :: {if $category eq 0}{t}HOME{/t}{else}{$datos_cat[0]->title}{/if}</h2></div>
            <ul class="old-button">

                {if $category!='home'}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 2);" name="submit_mult" value="Frontpage" title="Sugerir Home">
                        <img border="0" src="{$params.IMAGE_DIR}gosuggest50.png" title="{t}Suggest to home{/t}" alt="{t}Suggest to home{/t}" ><br />{t}Suggest to home{/t}
                    </a>
                </li>
                {/if}
                <!-- <li>
                    <a title="More actions" id="button_moreactions">
                        <img border="0" src="{$params.IMAGE_DIR}home_no50.png" title="{t}No home{/t}" alt="More actions" ><br />{t}More actions{/t}
                    </a>
                </li>
                <li class="separator"></li> -->
                <li>
                     <a href="#" data-category="{$category}" id="button_clearcache">
                         <img border="0" src="{$params.IMAGE_DIR}clearcache.png" title="{t}Clean cache{/t}" alt="" /><br />{t}Clean cache{/t}
                     </a>
                </li>
                <li style="display:none">
                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" status="0" src="{$params.IMAGE_DIR}select_button.png" title="{t}Select all{/t}" alt="{t}Select all{/t}"  status="0">
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="#" id="button_previewfrontpage" title="{t}Preview frontpage with actual content positions{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}preview.png" title="{t}Preview{/t}" alt="{t}Preview{/t}" ><br />{t}Preview{/t}
                    </a>
                </li>
                <li>
                    <a id="button_savepositions" href="#" class="admin_add" title="Guardar Positions" alt="Guardar Cambios">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save changes{/t}" alt="{t}Save changes{/t}" ><br />{t}Save changes{/t}
                    </a>
                </li>
                <li>
                     <a href="#" id="button_addnewcontents">
                         <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="{t}Add contents{/t}" alt="" /><br />{t}Add contents{/t}
                     </a>
                </li>
            </ul><!-- /old-button -->
        </div><!-- /wrapper-content -->
    </div><!-- /top-action-bar -->


    <div class="wrapper-content">

        {include file="frontpagemanager/_render_menu_categories.tpl"}

        <div id="warnings-validation"></div><!-- /warnings-validation -->

        <div id="frontpagemanager" data-category="{$category_id}" class="{$category} clearfix">
            {$layout}
        </div><!-- /frontpagemanager -->

        <div id="content-provider" class="clearfix" title="{t}Available contents{/t}">
            <div class="spinner"></div>
            <div class="content-provider-block-wrapper wrapper-content clearfix">
                <ul>
                    {is_module_activated name="ARTICLE_MANAGER"}
                    {if $category neq 'home'}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/article.php?action=content-provider-category&amp;category={$category}">{t}Other articles in this category{/t}</a>
                    </li>
                    {else}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/article.php?action=content-provider-suggested">{t}Suggested articles{/t}</a>
                    </li>
                    {/if}
                    {/is_module_activated}
                    {is_module_activated name="ADVANCED_SEARCH"}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/search_advanced/search_advanced.php?action=content-provider&amp;">{t}Search{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="WIDGET_MANAGER"}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/widget/widget.php?action=content-provider&amp;category={$category}">{t}Widgets{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="OPINION_MANAGER"}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/opinion/opinion.php?action=content-provider&amp;category={$category}">{t}Opinions{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="VIDEO_MANAGER"}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/video/video.php?action=content-provider&amp;category={$category}">{t}Videos{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="ALBUM_MANAGER"}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/album/album.php?action=content-provider&amp;category={$category}">{t}Albums{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="ADS_MANAGER"}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/advertisement/advertisement.php?action=content-provider&amp;category={$category}">{t}Advertisement{/t}</a>
                    </li>
                    {/is_module_activated}
                </ul>
            </div>

        </div><!-- /content-provider -->
    </div>

    <input type="hidden"  id="category" name="category" value="{$category}">
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default}" />
</form>
{include file="frontpagemanager/modals/_modal_send_to_trash.tpl"}
{include file="frontpagemanager/modals/_modal_archive.tpl"}
{include file="frontpagemanager/modals/_modal_suggest_to_frontpage.tpl"}
{/block}
