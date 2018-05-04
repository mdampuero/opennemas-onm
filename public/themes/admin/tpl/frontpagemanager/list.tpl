{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts src="@AdminTheme/js/onm/frontpagemanager.js" output="frontpagemanager"}
  <script type="text/javascript">
    var frontpage_messages = {
      remember_save_positions: "{t}Please, remember save positions after finish.{/t}",
      error_tab_content_provider: "{t}Couldn't load this tab. We'll try to fix this as soon as possible.{/t}",
      frontpage_too_long: "{t}You have reached the maximum number of elements (%number%). To add new contents remove the older.{/t}"
    };

    var tooltip_strings = {
      state: "{t}Status{/t}: ",
      views: "{t}Views{/t}: ",
      category: "{t}Category{/t}: ",
      schedule: "{t}Schedule{/t}: ",
      starttime: "{t}Available from{/t}: ",
      last_author: "{t}Last editor{/t}: ",
    }

    var frontpage_urls = {
      list:                  '{url name=admin_frontpage_list}',
      save_positions:        '{url name=admin_frontpage_savepositions}',
      toggle_suggested:      '{url name=admin_content_toggle_suggested}',
      quick_info:            '{url name=admin_content_quick_info}',
      set_arquived:          '{url name=admin_content_set_archived}',
      send_to_trash:         '{url name=admin_content_send_to_trash}',
      customize_content:     '{url name=admin_content_update_property}',
      check_version:         '{url name=admin_frontpage_last_version category=$category_id}'
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
              <i class="fa fa-newspaper-o page-navbar-icon"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/221736-opennemas-c%C3%B3mo-insertar-mover-gestionar-art%C3%ADculo" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
              {t}Frontpages{/t}
            </h4>
          </li>
          <li class="quicklinks visible-xs">
            <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/221736-opennemas-c%C3%B3mo-insertar-mover-gestionar-art%C3%ADculo" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
              <i class="fa fa-question fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks hidden-xs hidden-sm">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs hidden-sm">
            <h5>
              {$categories[$category_id]['name']}
              {if $available_layouts > 1}
              <small class="hidden-xs hidden-sm hidden-md">({$layout_theme['name']})</small>
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
              <button class="btn btn-white" id="button_previewfrontpage" ng-click="preview('{$categories[$category_id]['value']}')" title="{t}Preview frontpage with actual content positions{/t}" type="button" id="preview-button">
                <span class="fa fa-desktop" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></span>
                {t}Preview{/t}
              </button>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <a id="button_savepositions" href="#" class="btn btn-primary" data-text="{t}Saving{/t}..." data-title="{t}Save changes{/t}" id="save-button">
                <span class="fa fa-save"></span> <span class="hidden-xs text">{t}Save changes{/t}</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed hidden-xs" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button">
              <i class="fa fa-arrow-left fa-lg"></i>
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
            <button class="btn btn-link" ng-click="removeSelectedContents()" type="button">
              <i class="fa fa-times"></i> {t}Remove from this frontpage{/t}
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="archiveSelectedContents()" type="button">
              <i class="fa fa-inbox"></i> {t}Arquive{/t}
            </button>
          </li>
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
          <li class="quicklinks hidden-xs ng-cloak"  ng-init="category='{$categories[$category_id]['value']}'; categories = {json_encode(array_values($categories))|clear_json}">
            <ui-select name="author" theme="select2" ng-model="category" ng-change=changeCategory($select.selected.id)>
              <ui-select-match>
                <strong>{t}Category{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices group-by="'group'" repeat="item.value as item in categories | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
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
    <div id="warnings-validation"></div>

    <div class="grid simple visible-xs not-available-in-phone">
      <div class="grid-body center">
        <h5>{t escape=off}The frontpage manager is currently <strong>unavaible for your screen size</strong>{/t}</h5>
      </div>
    </div>

    <div id="frontpagemanager" data-category="{$category_id}" class="{$category_id} clearfix span-24 hidden-xs">
      {$layout}
    </div><!-- /frontpagemanager -->

    <div id="content-provider" class="clearfix hidden-xs ng-cloak" title="{t}Available contents{/t}">
      <div class="content-provider-block-wrapper clearfix">
        <ul>
          {is_module_activated name="ARTICLE_MANAGER"}
          {if $category_id eq 0}
          <li>
            <a href="{url name=admin_articles_content_provider_suggested category=$category_id}">{t}Suggested{/t}</a>
          </li>
          {else}
          <li>
            <a href="{url name=admin_articles_content_provider_category category=$category_id}">{t}Others in category{/t}</a>
          </li>
          {/if}
          {/is_module_activated}
          <li> {* filter_by_category param is to avoid category filter on latest articles provider *}
            <a href="{url name=admin_articles_content_provider_category category=$category_id filter_by_category=0}">{t}Latest articles{/t}</a>
          </li>

          {is_module_activated name="WIDGET_MANAGER"}
          <li>
            <a href="{url name=admin_widgets_content_provider category=$category_id}">{t}Widgets{/t}</a>
          </li>
          {/is_module_activated}
          {is_module_activated name="OPINION_MANAGER"}
          <li>
            <a href="{url name=admin_opinions_content_provider category=$category_id}">{t}Opinions{/t}</a>
          </li>
          {/is_module_activated}
          {is_module_activated name="VIDEO_MANAGER"}
          <li>
            <a href="{url name=admin_videos_content_provider category=$category_id}">{t}Videos{/t}</a>
          </li>
          {/is_module_activated}
          {is_module_activated name="ALBUM_MANAGER"}
          <li>
            <a href="{url name=admin_albums_content_provider category=$category_id}">{t}Albums{/t}</a>
          </li>
          {/is_module_activated}
          {is_module_activated name="LETTER_MANAGER"}
          <li>
            <a href="{url name=admin_letters_content_provider category=$category_id}">{t}Letter{/t}</a>
          </li>
          {/is_module_activated}
          {is_module_activated name="POLL_MANAGER"}
          <li>
            <a href="{url name=admin_polls_content_provider category=$category_id}">{t}Polls{/t}</a>
          </li>
          {/is_module_activated}
          {is_module_activated name="ADS_MANAGER"}
          <li>
            <a href="{url name=admin_ads_content_provider category=$category_id}">{t}Advertisement{/t}</a>
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
  <input type="hidden"  id="category" name="category" value="{$category_id}">
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
    href="{url name=admin_frontpage_pick_layout category=$category_id layout=$key}">
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
      {$categories[$category_id]['name']}
    </h4>
  </div>
  <div class="modal-body clearfix no-padding">
    <iframe ng-src="[% template.src %]" frameborder="0"></iframe>
  </div>
</script>
<script type="text/ng-template" id="modal-drop-selected">
  {include file="common/modals/_modalDropSelected.tpl"}
</script>
<script type="text/ng-template" id="modal-archive-selected">
  {include file="common/modals/_modalArchiveSelected.tpl"}
</script>
{/block}


{block name="modals"}
  {include file="frontpagemanager/modals/_modal_send_to_trash.tpl"}
  {include file="frontpagemanager/modals/_modal_archive.tpl"}
  {include file="frontpagemanager/modals/_modal_new_version.tpl"}

  {is_module_activated name="ADVANCED_FRONTPAGE_MANAGER"}
  {include file="frontpagemanager/modals/_modal_customize_content.tpl"}
  {/is_module_activated}
{/block}
