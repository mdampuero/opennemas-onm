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
  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section filter-components">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="info">
              {if $maxAllowed gt 0}
                {t 1=$lastInvoice 2=$totalSendings 3=$maxAllowed}%2 newsletter sents from %1 (%3 allowed){/t}
              {else}
                {t 1=$lastInvoice 2=$totalSendings}%2 newsletter sents from %1 {/t}
              {/if}
            </span>
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
        <div class="table-wrapper ng-cloak">
          <div ng-if="!loading && contents.length == 0">
              {t}There is no newsletters yet.{/t}
          </div>
          <table class="table table-hover no-margin" ng-if="!loading">
            <thead>
              <tr ng-if="contents.length > 0">
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
              <tr ng-if="contents.length >= 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
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
                    <small><strong>{t}Created on:{/t}</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]</small>
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
        <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
          <div class="pagination-info pull-left" ng-if="contents.length > 0">
            {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
          </div>
          <div class="pull-right pagination-wrapper" ng-if="contents.length > 0">
            <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
          </div>
        </div>
      </div>
    </div>

  </div>
  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/_modalDelete.tpl"}
  </script>
</div>
{/block}
