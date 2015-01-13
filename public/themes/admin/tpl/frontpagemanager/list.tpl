{extends file="base/admin.tpl"}

{block name="header-css" append}
    {stylesheets src="@AdminTheme/css/bp/screen.css,
        @AdminTheme/css/frontpagemanager.css,
        @AdminTheme/css/jquery/colorbox.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}">
    {/stylesheets}
    <!--[if IE]>{css_tag href="/bp/ie.css"}<![endif]-->
{/block}

{block name="footer-js" append}
    <script>
        var frontpage_messages = {
            remember_save_positions: "{t}Please, remember save positions after finish.{/t}",
            error_tab_content_provider: "{t}Couldn't load this tab. We'll try to fix this as soon as possible.{/t}"
        }
        var frontpage_urls = {
            save_positions: '{url name=admin_frontpage_savepositions}',
            preview_frontpage: '{url name=admin_frontpage_preview category=$category}',
            toggle_suggested: '{url name=admin_content_toggle_suggested}',
            quick_info: '{url name=admin_content_quick_info}',
            set_arquived: '{url name=admin_content_set_archived}',
            send_to_trash: '{url name=admin_content_send_to_trash}',
            customize_content: '{url name=admin_content_update_property}',
            check_version: '{url name=admin_frontpage_last_version category=$category}',
            get_preview_frontpage: '{url name=admin_frontpage_get_preview category=$category}',
        };
        var content_states = {
            {foreach from=$frontpage_articles item=content}
            {if $content->id}
            {$content->id}: {$content->getQuickInfo()|json_encode},
            {/if}
            {/foreach}
        }
        var frontpage_info = {
            last_saved : '{$frontpage_last_saved}',
            changed: false
        }
    </script>
    {javascripts src="@AdminTheme/js/jquery/jquery.colorbox-min.js,
        @AdminTheme/js/onm/frontpagemanager.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}

{/block}

{block name="content"}
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-newspaper-o fa-lg"></i>
                        {t}Frontpages{/t} :: {if $category eq 0}{t}Home{/t}{else}{$datos_cat[0]->title}{/if} {if $available_layouts > 1} <small>({$layout_theme['name']})</small> {/if}
                    </h4>
                </li>
            </ul>
        </div>
    </div>
</div>

<form action="#" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <ul class="old-button">
                <li class="batch-actions">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" id="button_multiple_delete">
                                <i class="icon-remove"></i> {t}Remove from this frontpage{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="button_multiple_arquive">
                                <i class="icon-inbox"></i> {t}Arquive{/t}
                            </a>
                        </li>
                        {if $category_id != 0}
                        <li>
                            <a href="#" id="button_multiple_suggest">
                                <i class="icon-star"></i>{t}Toggle suggest{/t}
                            </a>
                        </li>
                        {/if}
                    </ul>

                </li>

                <li class="separator batch-actions"></li>
                <li>
                    <a href="{url name=admin_article_create  category=$category}" class="admin_add" title="{t}New article{/t}">
                        <img src="{$params.IMAGE_DIR}/article_add.png" title="" alt="" />
                        <br />{t}New article{/t}
                    </a>
                </li>

                <li class="separator"></li>

                <li>
                    <a href="#" id="button_previewfrontpage"  data-category-name="{if $category eq 0}home{else}{$datos_cat[0]->name}{/if}" title="{t}Preview frontpage with actual content positions{/t}">
                        <img src="{$params.IMAGE_DIR}preview.png" alt="{t}Preview{/t}" ><br />{t}Preview{/t}
                    </a>
                </li>
                <li>
                    <a id="button_savepositions" href="#" class="admin_add"  title="{t}Save changes{/t}">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save changes{/t}" ><br />{t}Save changes{/t}
                    </a>
                </li>
                <li>
                     <a href="#" id="button_addnewcontents" title="{t}Add contents{/t}">
                         <img src="{$params.IMAGE_DIR}list-add.png" alt="" /><br />{t}Add contents{/t}
                     </a>
                </li>
            </ul><!-- /old-button -->
        </div><!-- /wrapper-content -->
    </div><!-- /top-action-bar -->

    <div class="settings settings-panel">
        <div class="wrapper-content">
            <a href="#" class="close">×</a>
            {if $available_layouts > 1}
                <h4>{t}Default layout for this frontpage{/t}</h4>
                {foreach from=$available_layouts key=key item=avlayout}
                    <a class="thumbnail {if $avlayout['name'] eq $layout_theme['name']}active{/if}"
                       href="{url name=admin_frontpage_pick_layout category=$category layout=$key}">
                        {$avlayout['name']}
                    </a>
                {/foreach}
            {/if}
        </div>
    </div>

    <div class="wrapper-content">
        {include file="frontpagemanager/_render_menu_categories.tpl"}

        <div id="warnings-validation"></div><!-- /warnings-validation -->
        {render_messages}

        <div id="frontpagemanager" data-category="{$category_id}" class="{$category} clearfix">
            {$layout}
        </div><!-- /frontpagemanager -->

        <div id="content-provider" class="clearfix" title="{t}Available contents{/t}">
            <div class="spinner"></div>
            <div class="content-provider-block-wrapper clearfix">
                <ul>
                    {is_module_activated name="ARTICLE_MANAGER"}
                    {if empty($category) || $category eq 'home' || $category eq 0}
                    <li>
                        <a href="{url name=admin_articles_content_provider_suggested category=$category}">{t}Suggested{/t}</a>
                    </li>
                    {else}
                    <li>
                         <a href="{url name=admin_articles_content_provider_category category=$category}">{t}Others in category{/t}</a>
                    </li>
                    {/if}
                    {/is_module_activated}
                    <li>
                        <a href="{url name=admin_articles_content_provider_category}">{t}Latest articles{/t}</a>
                    </li>

                    {is_module_activated name="WIDGET_MANAGER"}
                    <li>
                        <a href="{url name=admin_widgets_content_provider category=$category}">{t}Widgets{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="OPINION_MANAGER"}
                    <li>
                        <a href="{url name=admin_opinions_content_provider category=$category}">{t}Opinions{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="VIDEO_MANAGER"}
                    <li>
                        <a href="{url name=admin_videos_content_provider category=$category}">{t}Videos{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="ALBUM_MANAGER"}
                    <li>

                        <a href="{url name=admin_albums_content_provider category=$category}">{t}Albums{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="LETTER_MANAGER"}
                    <li>
                        <a href="{url name=admin_letters_content_provider category=$category}">{t}Letter{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="POLL_MANAGER"}
                    <li>
                        <a href="{url name=admin_polls_content_provider category=$category}">{t}Polls{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="ADS_MANAGER"}
                    <li>
                        <a href="{url name=admin_ads_content_provider category=$category}">{t}Advertisement{/t}</a>
                    </li>
                    {/is_module_activated}
                    {is_module_activated name="ADVANCED_SEARCH"}
                    <li>
                        <a href="{url name=admin_search_content_provider related=0}"><i class="icon-search"></i></a>
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
{include file="frontpagemanager/modals/_modal_drop_selected.tpl"}
{include file="frontpagemanager/modals/_modal_arquive_selected.tpl"}
{include file="frontpagemanager/modals/_modal_new_version.tpl"}
{is_module_activated name="AVANCED_FRONTPAGE_MANAGER"}
{include file="frontpagemanager/modals/_modal_customize_content.tpl"}
{/is_module_activated}

{/block}
