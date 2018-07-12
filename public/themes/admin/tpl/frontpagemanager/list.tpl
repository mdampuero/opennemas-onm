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
      toggle_suggested:      '{url name=admin_content_toggle_suggested}',
      quick_info:            '{url name=admin_content_quick_info}',
      set_arquived:          '{url name=admin_content_set_archived}',
      send_to_trash:         '{url name=admin_content_send_to_trash}',
      customize_content:     '{url name=admin_content_update_property}'
    };
    var content_states = {
      {foreach from=$frontpage_articles item=content}
        {if $content->id}
          {$content->id}: {$content->getQuickInfo()|json_encode},
        {/if}
      {/foreach}
    }
  </script>
  {/javascripts}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" ng-controller="FrontpageCtrl" ng-init="init({json_encode($frontpages)|clear_json}, {json_encode($versions)|clear_json}, {json_encode($category_id)|clear_json}, {json_encode($version_id)|clear_json}, {json_encode($time)|clear_json}, {json_encode($frontpage_last_saved)|clear_json}, {json_encode($available_layouts)|clear_json}, {json_encode($layout_theme)|clear_json})">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-newspaper-o m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              {t}Frontpages{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5 ng-cloak">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks frontpages ng-cloak">
            <ui-select name="frontpages" theme="select2" ng-model="categoryId" ng-change=changeCategory($select.selected.id)>
              <ui-select-match>
                [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices group-by="'manual'" repeat="item.id as item in frontpages | filter: { name: $select.search }">
                <div>
                  <span ng-if="item.manual" class="fa fa-newspaper-o"></span>
                  <span ng-bind-html="item.name | highlight: $select.search"></span>
                </div>
              </ui-select-choices>
            </ui-select>
          </li>
        {is_module_activated name="es.openhost.theme.scheduleFrontpage"}
          <li class="quicklinks hidden-xs m-l-5 m-r-5 ng-cloak">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks version ng-cloak">
            <ui-select name="versions" theme="select2" ng-model="versionId" ng-change=changeVersion($select.selected.id) search-enabled="false">
              <ui-select-match>
                [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices  repeat="item.id as item in versions">
                <div class="versionNotScheduled" ng-if="scheduledFFuture.indexOf(item.id) === -1 && item.id !== publishVersionId">
                  <span class="notScheduled"></span>
                  <span ng-bind-html="item.name | highlight: $select.search"></span>
                  <span class="btn btn-link" ng-click="deleteVersion($event, item.id)"><span class="fa fa-trash-o text-danger"></span></span>
                </div>
                <div class="versionScheduled" ng-if="scheduledFFuture.indexOf(item.id) !== -1 && item.id !== publishVersionId">
                  <span class="fa fa-calendar-check-o p-5"></span>
                  <span ng-bind-html="item.name | highlight: $select.search"></span>
                  <span class="versionDate">[% utcToTimezone(item.publish_date) %]</span>
                </div>
                <div class="versionLive"  ng-if="item.id === publishVersionId">
                  <span class="fa fa-globe"></span>
                  <span ng-bind-html="item.name | highlight: $select.search"></span>
                  <span class="badge badge-pill badge-primary"><span class="fa fa-globe"></span>{t}LIVE{/t}</span>
                </div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks ng-cloak" ng-if="publishVersionId === versionId">
            <div class="badge badge-pill badge-primary"><span class="fa fa-globe"></span>{t}LIVE{/t}</div>
          </li>
        {/is_module_activated}
        </ul>
        <div class="all-actions pull-right hidden-xs ng-cloak">
          <ul class="nav quick-section">
            {is_module_activated name="FRONTPAGES_LAYOUT"}
            <li class="quicklinks hidden-sm">
              <div class="btn btn-default" id="frontpage-settings" uib-tooltip="{t}Settings{/t}" tooltip-placement="left" ng-click="openLayoutModal()">
                <i class="fa fa-cog"></i>
              </div>
            </li>
            <li class="quicklinks hidden-sm">
              <span class="h-seperate"></span>
            </li>
            {/is_module_activated}
            <li class="quicklinks">
              <div class="btn-group">
                <button class="btn btn-primary" type="button" ng-click="save()">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  {t}Save{/t}
                </button>
                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" type="button">
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu no-padding pull-right">
                  <li>
                    <a href="#" ng-click="preview()">
                      <i class="fa fa-eye"></i>
                      {t}Preview{/t}
                    </a>
                  </li>
                {is_module_activated name="es.openhost.theme.scheduleFrontpage"}
                  <li class="divider"></li>
                  <li>
                    <a href="#" ng-click="saveVersion()">
                      <i class="fa fa-files-o"></i>
                      {t}Save this version{/t}
                    </a>
                  </li>
                  <li class="divider visible-md visible-sm"></li>
                  <li class="visible-md visible-sm">
                    <a href="#">
                      <i class="fa fa-toggle-off"></i>
                      {t}Live now{/t}
                    </a>
                  </li>
                  <li class="divider visible-sm"></li>
                  <li class="visible-sm">
                    <a href="#" ng-click="deleteVersion($event)">
                      <i class="fa fa-trash-o fa-lg"></i>
                      {t}Delete{/t}
                    </a>
                  </li>
                {/is_module_activated}
                {is_module_activated name="FRONTPAGES_LAYOUT"}
                  <li class="divider visible-sm"></li>
                  <li class="visible-sm">
                    <a href="#" ng-click="openLayoutModal()">
                      <i class="fa fa-cog"></i>
                      {t}Settings{/t}
                    </a>
                  </li>
                {/is_module_activated}
                </ul>
              </div>
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
{is_module_activated name="es.openhost.theme.scheduleFrontpage"}
  <div class="page-navbar filters-navbar hidden-xs ng-cloak" ng-if="publishVersionId !== versionId">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <span class="info input-label">{t}Title{/t}:</span>
            <input id="name" name="name" ng-model="version.name" type="text"  required="required">
          </li>
        </ul>
        <ul class="nav quick-section pull-right">
          <li class="quicklinks">
            <span class="info input-label">{t}Go live on{/t} ([% time.timezone %]):</span>
            <input name="publish_date" datetime-picker datetime-picker-timezone="{$timezone}" keepOpen="true" ng-model="frontpageInfo.publish_date" type="datetime">
            <span class="input-addon">
              <span class="fa fa-calendar"></span>
            </span>
          </li>
          <li class="quicklinks hidden-md hidden-sm hidden-xs">
            <a class="btn btn-white" href="#" ng-click="saveLiveNow()">
              <span class="fa fa-toggle-off"></span> <span>{t}Live now{/t}</span>
            </a>
          </li>
          <li class="quicklinks hidden-sm">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-sm">
            <button class="btn btn-danger" ng-click="deleteVersion($event)" uib-tooltip="{t}Delete{/t}" tooltip-placement="left" ng-if="versionId !== publishVersionId">
              <i class="fa fa-trash-o fa-lg"></i>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
{/is_module_activated}
  <div class="content">
    <a class="btn btn-add btn-success hidden-xs ng-cloak btn-down" href="#" id="button_addnewcontents" title="{t}Add contents{/t}">
      <span class="fa fa-plus"></span>
    </a>
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
  <script type="text/ng-template" id="modal-layout">
    <div class="modal-header">
    [% template.categoryId %]
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()" type="button">&times;</button>
      <h4 class="modal-title">
        {t}Change the layout of this frontpage{/t}
      </h4>
      </div>
      <div class="modal-body clearfix">
        <a class="layout-type btn" ng-repeat="(layoutKey, layout) in template.layouts" ng-click="template.changeLayout(layoutKey)"> [% layout.name %]</a>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">{t}Close{/t}</button>
      </div>
    </div>
  </script>
  <input type="hidden"  id="category" name="category" value="{$category_id}">
  <input type="hidden" name="id" id="id" value="{$id|default}" />
</form>
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
<script type="text/ng-template" id="modal-new-version">
  {include file="frontpagemanager/modals/_modal_new_version.tpl"}
</script>
<script type="text/ng-template" id="modal-publish-check">
  {include file="frontpagemanager/modals/_publish_check.tpl"}
</script>
<script type="text/ng-template" id="modal-publish-now">
  {include file="frontpagemanager/modals/_publish_now.tpl"}
</script>
{/block}


{block name="modals"}
  {include file="frontpagemanager/modals/_modal_send_to_trash.tpl"}
  {include file="frontpagemanager/modals/_modal_archive.tpl"}

  {is_module_activated name="ADVANCED_FRONTPAGE_MANAGER"}
  {include file="frontpagemanager/modals/_modal_customize_content.tpl"}
  {/is_module_activated}
{/block}
