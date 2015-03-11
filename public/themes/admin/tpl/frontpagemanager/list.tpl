{extends file="base/admin.tpl"}

{block name="header-css" append}
    {stylesheets src="@AdminTheme/css/bp/screen.css,
        @AdminTheme/less/_frontpage.less,
        @AdminTheme/css/frontpagemanager.css,
        @AdminTheme/css/jquery/colorbox.css" filters="cssrewrite,less"}
        <link rel="stylesheet" href="{$asset_url}">
    {/stylesheets}
    <!--[if IE]>{css_tag href="/bp/ie.css"}<![endif]-->
    <style>
      @media (max-width: 767px) {
        .page-content .filters-navbar ~ .content {
          margin-top: 60px;
        }
        #frontpagemanager {
          zoom:0.7 !important;
        }
      }
      @media (max-width:900px) {
        #frontpagemanager {
          zoom:0.7 !important;
        }
      }

    </style>
{/block}

{block name="footer-js" append}
    <script type="text/javascript">
        var frontpage_messages = {
            remember_save_positions: "{t}Please, remember save positions after finish.{/t}",
            error_tab_content_provider: "{t}Couldn't load this tab. We'll try to fix this as soon as possible.{/t}"
        }
        var frontpage_urls = {
            list:                  '{url name=admin_frontpage_list}',
            save_positions:        '{url name=admin_frontpage_savepositions}',
            toggle_suggested:      '{url name=admin_content_toggle_suggested}',
            quick_info:            '{url name=admin_content_quick_info}',
            set_arquived:          '{url name=admin_content_set_archived}',
            send_to_trash:         '{url name=admin_content_send_to_trash}',
            customize_content:     '{url name=admin_content_update_property}',
            check_version:         '{url name=admin_frontpage_last_version category=$category}'
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

        jQuery(document).ready(function($) {
            $('#categoryItem').on('change', function(){
                var category_value = $('#categoryItem option:checked').val();
                window.location = frontpage_urls.list+'/'+category_value;
            });
        })
    </script>

    {javascripts src="@AdminTheme/js/jquery/jquery.colorbox-min.js,
        @AdminTheme/js/onm/frontpagemanager.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}

{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" ng-controller="FrontpageCtrl">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-newspaper-o"></i>
                            {t}Frontpages{/t}
                        </h4>
                    </li>
                    <li class="quicklinks hidden-xs hidden-sm">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks hidden-xs hidden-sm">
                        <h5>
                            {if $category eq 0}
                                {t}Home{/t}
                            {else}
                                {$datos_cat[0]->title}
                            {/if}
                            {if $available_layouts > 1}
                                <small>({$layout_theme['name']})</small>
                            {/if}
                        </h5>
                    </li>
                </ul>
                <div class="all-actions pull-right hidden-xs">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                             <a class="btn btn-white" href="#" id="button_addnewcontents" title="{t}Add contents{/t}">
                                 <span class="fa fa-plus"></span> <span class="hidden-xs">{t}Add contents{/t}</span>
                             </a>
                        </li>
                        <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
                        <li class="quicklinks">
                            <button class="btn btn-white" id="button_previewfrontpage" ng-click="preview('{if $category eq 0}home{else}{$datos_cat[0]->name}{/if}')" title="{t}Preview frontpage with actual content positions{/t}" type="button">
                              <span class="fa fa-desktop" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></span>
                              {t}Preview{/t}
                            </button>
                        </li>
                        <li class="quicklinks"><span class="h-seperate"></span></li>
                        <li class="quicklinks">
                            <a id="button_savepositions" href="#" class="btn btn-primary"  title="{t}Save changes{/t}">
                                <span class="fa fa-save"></span> <span class="hidden-xs">{t}Save changes{/t}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }" style="display: none;">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                      <button class="btn btn-link" ng-click="deselectAll()" tooltip="Clear selection" tooltip-placement="right"type="button">
                        <i class="fa fa-check fa-lg"></i>
                      </button>
                    </li>
                     <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h4>
                            [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
                        </h4>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    <li class="quicklinks">
                        <a href="#" id="button_multiple_delete">
                            <i class="fa fa-times"></i> {t}Remove from this frontpage{/t}
                        </a>
                    </li>
                    <li class="quicklinks">
                        <a href="#" id="button_multiple_arquive">
                            <i class="fa fa-inbox"></i> {t}Arquive{/t}
                        </a>
                    </li>
                    {if $category_id != 0}
                        <li class="quicklinks">
                            <a href="#" id="button_multiple_suggest">
                                <i class="fa fa-star"></i>{t}Toggle suggest{/t}
                            </a>
                        </li>
                    {/if}
                </ul>
            </div>
        </div>
    </div>

    <div class="page-navbar filters-navbar hidden-xs">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                <li class="quicklinks">
                  <span class="info">{t}Managing frontpage:{/t}</span>
                </li>
                    {*<!-- {acl hasCategoryAccess=0}
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_frontpage_list category=home}" class="{if $category == 'home' || $category == 0}active{/if}">{t}Home{/t}</a>
                        </li>
                    {/acl}
                    {foreach from=$menuItems item=menuItem}
                        {if $menuItem->type == 'category'}
                            {acl hasCategoryAccess=$menuItem->categoryID}
                                <li class="quicklinks {if count($menuItem->submenu) > 0}dropdown{/if}{if $category eq $menuItem->categoryID} active{/if}">
                                    <a class="btn btn-link{if $category eq $menuItem->categoryID || ($datos_cat[0]->fk_content_category neq '0' && $menuItem->categoryID eq $datos_cat[0]->fk_content_category)} active{/if}" {if count($menuItem->submenu) > 0}data-toggle="dropdown"{/if} href="{url name=admin_frontpage_list category=$menuItem->categoryID}" title="Sección: {$menuItem->title}">
                                        {$menuItem->title}
                                        {if count($menuItem->submenu) > 0}
                                            <span class="caret"></span>
                                        {/if}
                                    </a>
                                    {if count($menuItem->submenu) > 0}
                                        {assign value=$menuItem->submenu var=submenu}
                                        <ul class="dropdown-menu">
                                            {section  name=s loop=$submenu}
                                                {acl hasCategoryAccess=$submenu[s]->categoryID}
                                                    <li class="{if $category eq $submenu[s]->categoryID}active{/if}">
                                                        <a href="{url name=admin_frontpage_list category=$submenu[s]->categoryID}" title="{$submenu[s]->title|mb_lower}" class="cat {$menuItem->link}{if $category eq $menuItem->categoryID} active{/if}">
                                                            {$submenu[s]->title}
                                                        </a>
                                                    </li>
                                                {/acl}
                                            {/section}
                                        </ul>
                                    {/if}
                                </li>
                            {/acl}
                        {/if}
                    {/foreach}
                    -->*}

                    <li class="quicklinks">
                      <select name="category" id="categoryItem" class="select2">
                          {acl hasCategoryAccess=0}
                          <option value="0" {if $category eq 0}selected{/if}>{t}Home{/t}</option>
                          {/acl}
                          {section name=as loop=$allcategorys}
                              {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                              <option value="{$allcategorys[as]->pk_content_category}"
                                  {if $allcategorys[as]->inmenu eq 0} class="unavailable" {/if}
                                  {if $category eq $allcategorys[as]->pk_content_category} selected ="selected" {/if} >
                                      {t 1=$allcategorys[as]->title}%1{/t}
                              </option>
                              {/acl}
                              {section name=su loop=$subcat[as]}
                                  {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                  <option value="{$subcat[as][su]->pk_content_category}"
                                      {if $subcat[as][su]->inmenu eq 0} class="unavailable" {/if}
                                      {if $category eq $subcat[as][su]->pk_content_category} selected ="selected" {/if} >
                                      &nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}
                                  </option>
                                  {/acl}
                              {/section}
                          {/section}
                        </select>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    {is_module_activated name="FRONTPAGES_LAYOUT"}
                      <li class="quicklinks">
                          <span class="h-seperate"></span>
                      </li>
                       <li class="quicklinks">
                          <div class="btn btn-default" id="frontpage-settings" ng-click="open('modal-layout')">
                            <i class="fa fa-cog"></i>
                          </div>
                      </li>
                    {/is_module_activated}
                </ul>
            </div>
        </div>
    </div>

    <div class="content">
        <div id="warnings-validation"></div><!-- /warnings-validation -->

        {render_messages}

        <div class="grid simple visible-xs">
          <div class="grid-body center">
            <h5>{t escape=off}The frontpage manager is currently <strong>unavaible for your screen size</strong>{/t}</h5>
          </div>
        </div>

        <div id="frontpagemanager" data-category="{$category_id}" class="{$category} clearfix hidden-xs">
            {$layout}
        </div><!-- /frontpagemanager -->

        <div id="content-provider" class="clearfix hidden-xs ng-cloak" title="{t}Available contents{/t}">
            <div class="loading-spinner"></div>
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
                        <a href="{url name=admin_search_content_provider related=0}"><i class="fa fa-search"></i></a>
                    </li>
                    {/is_module_activated}
                </ul>
            </div>
        </div><!-- /content-provider -->
    </div>
    <input type="hidden"  id="category" name="category" value="{$category}">
    <input type="hidden" name="id" id="id" value="{$id|default}" />
</form>

<script type="text/ng-template" id="modal-layout">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()" type="button">&times;</button>
    <h4 class="modal-title">
      {t}Change the layout of this frontpage{/t}
    </h4>
  </div>
  <div class="modal-body clearfix">
    {if $available_layouts > 1}
      {foreach from=$available_layouts key=key item=avlayout}
          <a class="layout-type {if $avlayout['name'] eq $layout_theme['name']}active{/if}"
          href="{url name=admin_frontpage_pick_layout category=$category layout=$key}">
          {$avlayout['name']}
        </a>
      {/foreach}
    {/if}
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">Close</button>
  </div>
</div>
</script>

<script type="text/ng-template" id="modal-preview">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()" type="button">&times;</button>
    <h4 class="modal-title">
      {t}Preview{/t}
      {if $category eq 0}
        ({t}Home{/t})
    {else}
        ({$datos_cat[0]->title})
    {/if}
    </h4>
  </div>
  <div class="modal-body clearfix no-padding">
    <iframe ng-src="[% template.src %]" frameborder="0"></iframe>
  </div>
</script>


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
