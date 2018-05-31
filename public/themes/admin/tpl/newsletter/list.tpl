{extends file="base/admin.tpl"}
{block name="content"}
<div ng-controller="NewsletterListCtrl" ng-init="criteria = { epp: 10, orderBy: { created: 'desc' }, page: 1 }; init(null, 'backend_ws_newsletter_list')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
                <i class="fa fa-envelope"></i>
                {t}Newsletters{/t}
              </a>
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li>
              <a class="btn btn-link" href="{url name=backend_newsletters_config}" class="admin_add" title="{t}Config newsletter module{/t}">
                <span class="fa fa-cog fa-lg"></span>
              </a>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            {* <li class="hidden-xs">
              <a class="btn btn-danger" href="{url name=admin_newsletter_subscriptors}" class="admin_add" id="submit_mult" title="{t}Subscribers{/t}">
                <span class="fa fa-users"></span>
                {t}Subscribers{/t}
              </a>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li> *}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=backend_newsletters_create}" accesskey="N" tabindex="1" id="create-button">
                <i class="fa fa-plus"></i>
                {t}Create{/t}
              </a>
            </li>
          </ul>
        </div>
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
            <input class="no-boarder" name="title" ng-model="criteria.title" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by subject{/t}" type="text"/>
          </li>
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <span class="info">{$message}</span>
          </li>
        </ul>
        <ul class="nav quick-section pull-right ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="spinner-wrapper" ng-if="loading">
      <div class="loading-spinner"></div>
      <div class="spinner-text">{t}Loading{/t}...</div>
    </div>
    <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-warning text-warning"></i>
        <h3>{t}Unable to find any item that matches your search.{/t}</h3>
        <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
      </div>
    </div>
    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th>{t}Title{/t}</th>
                <th class="hidden-xs hidden-sm" style="width:250px;">{t}Updated{/t}</th>
                <th class="right">{t}Sendings{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="content in contents">
                <td>
                  <div ng-if="content.title != ''">[% content.title %]</div>
                  <div ng-if="content.title == ''">{t}Newsletter{/t}  -  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]</div>
                  <div class="small-text">
                    <strong>{t}Created:{/t}</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                  </div>
                  <div class="listing-inline-actions">
                    <a class="btn btn-default btn-small" href="[% edit(content.id, 'backend_newsletters_show_contents') %]" title="{t}Edit{/t}" >
                      <i class="fa fa-pencil"></i> {t}Edit{/t}
                    </a>
                    <a href="[% edit(content.id, 'backend_newsletters_preview') %]" title="{t}Preview{/t}" class="btn btn-primary btn-small">
                      <i class="fa fa-eye"></i>
                      {t}Preview{/t}
                    </a>
                    <button class="btn btn-danger btn-small" ng-if="content.sent < 1" class="link link-danger" ng-click="removePermanently(content)" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Delete{/t}
                    </button>
                  </div>
                </td>
                <td class="hidden-xs hidden-sm">
                  [% content.updated | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
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
        <div class="pull-right">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
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
      <p>{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to remove permanently %1 item(s)?{/t}</p>
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
