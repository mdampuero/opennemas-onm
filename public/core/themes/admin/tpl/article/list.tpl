{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ArticleListCtrl" ng-init="init()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-file-text-o page-navbar-icon"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/220778-primeros-pasos-en-opennemas-c%C3%B3mo-crear-un-art%C3%ADcu" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
              {t}Articles{/t}
            </h4>
          </li>
          <li class="quicklinks seperate hidden-xs ng-cloak" ng-if="config.locale.multilanguage">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks ng-cloak" ng-if="config.locale.multilanguage">
            <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
          </li>
          <li class="quicklinks visible-xs">
            <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/220778-primeros-pasos-en-opennemas-c%C3%B3mo-crear-un-art%C3%ADcu" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
              <i class="fa fa-question fa-lg"></i>
            </a>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="MASTER"}
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_articles_config}" class="admin_add" title="{t}Config article module{/t}">
                  <span class="fa fa-cog fa-lg"></span>
                </a>
              </li>
              <li class="quicklinks"><span class="h-seperate"></span></li>
            {/acl}
              <li class="quicklinks">
                {acl isAllowed="ARTICLE_CREATE"}
                <a class="btn btn-primary text-uppercase ng-cloak" href="{url name=admin_article_create}[% config.locale.multilanguage ? '?locale=' + config.locale.selected : '' %]" id="create-button">
                  <i class="fa fa-plus"></i>
                  {t}Create{/t}
                </a>
                {/acl}
              </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
              <i class="fa fa-arrow-left fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <h4>
              [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
            </h4>
          </li>
        </ul>
        <ul class="nav quick-section pull-right">
          <li class="quicklinks" ng-if="config.locale.multilanguage">
            {acl isAllowed="ARTICLE_UPDATE"}
            <div class="dropdown">
              <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" uib-tooltip="{t}Translate selected{/t}" tooltip-placement="bottom">
                <i class="fa fa-globe fa-lg"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-right no-padding" aria-labelledby="dropdownMenuButton">
                <li ng-repeat="(locale_key, locale_name) in data.extra.options.available" ng-show="locale_key != data.extra.locale" class="dropdown-item" ng-class="{ 'disabled': selectedItemsAreTranslatedTo(locale_key) }">
                  <a href="#" ng-click="!selectedItemsAreTranslatedTo(locale_key) && translateSelected(locale_key)" >{t 1="[% locale_name %]"}Translate into %1{/t}</a>
                </li>
              </ul>
            </div>
            {/acl}
          </li>
          <li class="quicklinks hidden-xs" ng-if="config.locale.multilanguage">
            <span class="h-seperate"></span>
          </li>
          {acl isAllowed="ARTICLE_AVAILABLE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          {/acl}
          {acl isAllowed="ARTICLE_DELETE"}
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="sendToTrashSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-trash-o fa-lg"></i>
            </button>
          </li>
          {/acl}
        </ul>
      </div>
    </div>
  </div>

  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section filter-components">
          <li class="m-r-10 quicklinks">
            <div class="input-group input-group-animated">
              <span class="input-group-addon">
                <i class="fa fa-search fa-lg"></i>
              </span>
              <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.title }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.title" placeholder="{t}Search{/t}" type="text">
              <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('title')" ng-show="criteria.title">
                <i class="fa fa-times"></i>
              </span>
            </div>
          </li>
          <li class="hidden-xs m-r-10 ng-cloak quicklinks ">
            <onm-category-selector class="block" default-value-text="{t}Any{/t}" label-text="{t}Category{/t}" locale="config.locale.selected" ng-model="criteria.category_id" placeholder="{t}Any{/t}"></onm-category-selector>
          </li>
          <li class="hidden-xs m-r-10 ng-cloak quicklinks">
            {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
          </li>
          <li class="hidden-xs hidden-sm m-r-10 ng-cloak quicklinks">
            {include file="ui/component/select/author.tpl" label="true" ngModel="criteria.fk_author"}
          </li>
        </ul>
        <ul class="nav quick-section quick-section-fixed ng-cloak" ng-if="items.length > 0">
          <li class="quicklinks">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-if="!loading && items.length == 0">
          <div class="text-center p-b-15 p-t-15">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t}Unable to find any item that matches your search.{/t}</h3>
            <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
          </div>
        </div>
        <div class="table-wrapper ng-cloak" ng-if="!loading && items.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <th class="checkbox-cell">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll()">
                  <label for="select-all"></label>
                </div>
              </th>
              <th>{t}Title{/t}</th>
              <th class="hidden-xs">{t}Author{/t}</th>
              {if $category eq 'all' || $category == 0}
                <th class="hidden-xs">{t}Section{/t}</th>
              {/if}
              <th class="text-center" width="100">{t}Published{/t}</th>
            </thead>
            <tbody>
              <tr ng-if="items.length == 0">
                <td class="empty" colspan="10">{t}No available articles.{/t}</td>
              </tr>
              <tr ng-if="items.length >= 0" ng-repeat="content in items" ng-class="{ row_selected: isSelected(content.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td>
                  [% content.title %]
                  <div class="small-text">
                    <strong>{t}Created{/t}: </strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                  </div>
                  <div class="small-text">
                    <span ng-if="content.starttime">
                      <strong>{t}Available from{/t} </strong>
                      [% content.starttime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </span>
                    <span ng-if="content.endtime">
                      <strong>{t}to{/t} </strong> [% content.endtime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </span>
                  </div>
                  <div class="listing-inline-actions">
                    {acl isAllowed="ARTICLE_UPDATE"}
                      <translator item="data.results[$index]" keys="data.extra.keys" link="[% routing.generate('admin_article_show', { id: content.id }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="config.locale.multilanguage" options="data.extra.locale" text="{t}Edit{/t}"></translator>
                      <a class="btn btn-default btn-small" href="[% routing.generate('admin_article_show', { id: content.id }) %]" ng-if="!config.locale.multilanguage">
                        <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                      </a>
                    {/acl}
                    {acl isAllowed="ARTICLE_DELETE"}
                      <button class="btn btn-danger btn-small" ng-click="sendToTrash(content)" type="button">
                        <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                      </button>
                    {/acl}
                    <span ng-if="content.params.bodyLink.length > 0" title="{t}Article has external link{/t}"><i class="fa fa-external-link-square"></i> <small>Has external link</small></span>
                  </div>
                </td>
                <td class="hidden-xs">
                  <span ng-if="content.fk_author">
                    <a href="[% routing.generate('backend_author_show', { id: content.fk_author }) %]">
                      [% (data.extra.authors | filter : { id: content.fk_author } : true)[0].name %]
                    </a>
                  </span>
                  <span ng-if="!content.fk_author && content.agency != ''">
                    [% content.agency %]
                  </span>
                </td>
                <td class="hidden-xs">
                  <span ng-if="!content.category_id">
                    {t}Unasigned{/t}
                  </span>
                  <span ng-if="content.category_id">
                    [% (categories | filter : { id: content.category_id } : true)[0].title %]
                  </span>
                </td>
                <td class="text-center">
                    {acl isAllowed="ARTICLE_AVAILABLE"}
                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.content_status == '1', 'fa-times text-error': !content.loading && content.content_status == '0' }"></i>
                    </button>
                    {/acl}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-translate-selected">
    {include file="common/modals/_translate_selected.tpl"}
  </script>
</div>
{/block}
