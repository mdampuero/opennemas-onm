{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="NewsletterListCtrl" ng-init="init('newsletter', { title_like: '' }, 'created', 'desc', 'backend_ws_newsletter_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-home fa-lg"></i>
              {t}Newsletters{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li>
              <a class="btn btn-link" href="{url name=admin_newsletter_config}" class="admin_add" title="{t}Config newsletter module{/t}">
                <span class="fa fa-cog fa-lg"></span>
              </a>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="hidden-xs">
              <a class="btn btn-white" href="{url name=admin_newsletter_subscriptors}" class="admin_add" id="submit_mult" title="{t}Subscriptors{/t}">
                <span class="fa fa-users"></span>
                {t}Subscriptors{/t}
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_newsletter_create}" accesskey="N" tabindex="1">
                <i class="fa fa-plus"></i>
                {t}Create{/t}
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" tooltip="Clear selection" tooltip-placement="right"type="button">
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
            <button class="btn btn-link" ng-click="removePermanentlySelected()" tooltip="{t}Remove{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-trash-o fa-lg"></i> <span class="hidden-xs">{t}Remove{/t}</span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section filter-components">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.title_like" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by subject{/t}" type="text"/>
          </li>
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <span class="info">{$message}</span>
          </li>
        </ul>
        <ul class="nav quick-section pull-right simple-pagination ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <span class="info">
              [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            </span>
          </li>
          <li class="quicklinks form-inline pagination-links">
            <div class="btn-group">
              <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                <i class="fa fa-chevron-left"></i>
              </button>
              <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                <i class="fa fa-chevron-right"></i>
              </button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="content">

    {render_messages}

    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
            <h4>{t}Unable to find any newsletter that matches your search.{/t}</h4>
            <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
          </div>
        </div>
        <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th>{t}Title{/t}</th>
                <th class="left hidden-xs"  style="width:150px;">{t}Created{/t}</th>
                <th class="left hidden-xs  hidden-sm"  style="width:150px;">{t}Updated{/t}</th>
                <th class="right">{t}Sendings{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td class="left">
                  <p ng-if="content.title != ''">[% content.title %]</p>
                  <p ng-if="content.title == ''">{t}Newsletter{/t}  -  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]</p>
                  <p class="visible-xs">
                    <small><strong>{t}Created:{/t}</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]</small>
                  </p>

                  <div class="listing-inline-actions">
                    <a class="link" href="[% edit(content.id, 'admin_newsletter_show_contents') %]" title="{t}Edit{/t}" >
                      <i class="fa fa-pencil"></i> {t}Edit{/t}
                    </a>

                    <a href="[% edit(content.id, 'admin_newsletter_preview') %]" title="{t}Preview{/t}" class="link">
                      <i class="fa fa-eye"></i>
                      {t}Show contents{/t}
                    </a>
                    <button ng-if="content.sent < 1" class="link link-danger" ng-click="removePermanently(content)" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Delete{/t}
                    </button>
                  </div>
                </td>
                <td class="left hidden-xs">
                  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </td>
                <td class="left hidden-xs hidden-sm">
                  [% content.updated | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </td>
                <td class="right">
                  [% content.sent != 0 ? content.sent : '{t}No{/t}' %]
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
        <div class="pagination-info pull-left">
          {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
        </div>
        <div class="pull-right pagination-wrapper">
          <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
        </div>
      </div>
    </div>

  </div>
  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/_modalDelete.tpl"}
  </script>

  <script type="text/ng-template" id="modal-batch-remove-permanently">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
      <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        {t}Remove permanently selected items{/t}
      </h4>
    </div>
    <div class="modal-body">
      <p>{t escape=off}Are you sure you want to remove permanently [% template.selected.contents.length %] item(s)?{/t}</p>
      <p class="alert alert-error">{t} You will not be able to restore them back.{/t}</p>
    </div>
    <div class="modal-footer">
      <span class="loading" ng-if="deleting == 1"></span>
      <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, remove them all{/t}</button>
      <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
    </div>
  </script>
</div>
{/block}
