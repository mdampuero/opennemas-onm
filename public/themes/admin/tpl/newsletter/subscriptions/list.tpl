{extends file="base/admin.tpl"}
{block name="footer-js"}
{include file="newsletter/subscriptions/modals/_modalDelete.tpl"}
{include file="newsletter/subscriptions/modals/_modalBatchDelete.tpl"}
{include file="newsletter/subscriptions/modals/_modalBatchSubscribe.tpl"}
{include file="newsletter/subscriptions/modals/_modalAccept.tpl"}
{/block}

{block name="content"}
<div ng-app="BackendApp" ng-controller="NewsletterSubscriptorListCtrl" ng-init="init('subscriptors', {  title_like: '', subscription: 0 }, 'created', 'desc', 'backend_ws_newsletter_subscriptors', '{{$smarty.const.CURRENT_LANGUAGE}}')">

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
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks">
            <h5>{t}Subscriptions{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-white" href="{url name=admin_newsletters}" title="{t}Go back to newsletter manager{/t}">
                <span class="fa fa-reply"></span>
                <span class="hidden-xs">{t}Newsletters{/t}</span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <a href="{url name=admin_newsletter_subscriptor_create}" class="btn btn-primary" accesskey="N">
                <span class="fa fa-plus"></span>
                {t}Create{/t}
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>


<!-- <div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
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
                    <button class="btn btn-link batchDeleteButton" accesskey="d">
                        <span class="fa fa-trash-o"></span>
                        {t}Delete{/t}
                    </button>
                </li>
                <li class="quicklinks">
                    <button data-subscribe="0" class="btn btn-link batchSubscribeButton">
                        {t}Unsubscribe{/t}
                    </button>
                </li>

                <li class="quicklinks">
                    <button data-subscribe="1" class="btn btn-link batchSubscribeButton">
                        {t}Subscribe{/t}
                    </button>
                </li>
            </ul>
        </div>
    </div>
  </div> -->

  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.title_like" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by name or email{/t}" type="search"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <select name="filters[subscription]" id="filters_subscription" class="select2" ng-model="criteria.subscription">
              <option value="-1">{t}All{/t}</option>
              <option value="1">{t}Subscribed{/t}</option>
              <option value="0">{t}No subscribed{/t}</option>
            </select>
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
          <table class="table table-hover no-margin" ng-if="!loading">
            <thead>
              <thead>
                <tr>
                  <th style="width:5px">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th>{t}Name{/t}</th>
                  <th>{t}Email{/t}</th>
                  <th class="left">{t}Status{/t}</th>
                  <th class="center" style="width:10px">{t}Activated{/t}</th>
                  <th class="center" style="width:10px">{t}Subscribed{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-if="contents.length == 0">
                  <td class="empty" colspan="10">{t}No available subscriptors.{/t}</td>
                </tr>
                <tr ng-if="contents.length >= 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td class="left">
                    [% content.firstname %]&nbsp;[% content.lastname %] [% content.name %]
                    <span class="visible-xs">([% content.email %])</span>
                    <div class="listing-inline-actions">
                      <a class="link" href="[% edit(content.id, 'admin_newsletter_subscriptor_show') %]" title="{t}Edit user{/t}">
                        <i class="fa fa-pencil"></i> {t}Edit{/t}
                      </a>
                      <button type="button" class="link link-danger" title="{t}Delete user{/t}" ng-click="removePermanently(content)">
                        <i class="icon-white fa fa-trash"></i> {t}Remove{/t}
                      </button>
                  </div>
                </td>
                <td class="left hidden-xs">
                  [% content.email %]
                </td>
                <td class="left">
                  <span ng-if="content.status == 0">{t}Mail sent.Waiting for user{/t}</span>
                  <span ng-if="content.status == 1">{t}Accepted by user{/t}</span>
                  <span ng-if="content.status == 2">{t}Accepted by administrator{/t}</span>
                  <span ng-if="content.status == 3">{t}Disabled by administrator{/t}</span>
                </td>
                <td class="center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_newsletter_subscriptor_toggle_activated', 'status', content.status != 1 ? 1 : 0, 'loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && (content.status == '1' || content.status == '2'), 'fa-times text-error': !content.loading && (content.status == '0' || content.status == '3') }"></i>
                  </button>
                </td>
                <td class="center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_newsletter_subscriptor_toggle_subscription', 'subscription', content.subscription != 1 ? 1 : 0, 'loading_sub')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading_sub, 'fa-check text-success' : !content.loading_sub && content.subscription == '1', 'fa-times text-error': !content.loading_sub && content.subscription == '0' }"></i>
                  </button>
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
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
</div>
{/block}
