<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <i class="fa fa-bell fa-lg"></i>
            {t}Notifications{/t}
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a ng-href="[% routing.ngGenerate('manager_notification_create') %]" class="btn btn-primary">
              <i class="fa fa-plus fa-lg"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
{include file='common/selected_navbar.tpl' list="instance"}
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="m-r-10 input-prepend inside search-form no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" ng-keyup="searchByKeypress($event)" placeholder="{t}Filter by title{/t}" ng-model="criteria.title_like[0].value" type="text" style="width:250px;"/>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="pagination.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
          </li>
        <li class="quicklinks hidden-xs">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-white" ng-click="criteria = {  title_like: [ { value: '', operator: 'like' } ]}; orderBy = [ { name: 'title', value: 'desc' } ]; pagination = { page: 1, epp: 25 }; refresh()">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-white" ng-click="refresh()">
            <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks toggle-columns">
          <div class="btn btn-link" ng-class="{ 'active': !columns.collapsed }" ng-click="toggleColumns()" tooltip-html="'{t}Columns{/t}'" tooltip-placement="left">
            <i class="fa fa-columns"></i>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks form-inline pagination-links">
          <div class="btn-group">
            <button class="btn btn-white" ng-click="pagination.page = pagination.page - 1" ng-disabled="pagination.page - 1 < 1" type="button">
              <i class="fa fa-chevron-left"></i>
            </button>
            <button class="btn btn-white" ng-click="pagination.page = pagination.page + 1" ng-disabled="pagination.page == pagination.pages" type="button">
              <i class="fa fa-chevron-right"></i>
            </button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content">
  <div class="row column-filters collapsed" ng-class="{ 'collapsed': columns.collapsed }">
    <div class="row">
      <div class="col-xs-12 title">
        <h5>{t}Columns{/t}</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-md-3 column">
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
            <label for="checkbox-name">
              {t}Name{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-domains" checklist-model="columns.selected" checklist-value="'domains'" type="checkbox">
            <label for="checkbox-domains">
              {t}Domains{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-domain-expire" checklist-model="columns.selected" checklist-value="'domain_expire'" type="checkbox">
            <label for="checkbox-domain-expire">
              {t}Domain expire{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-contact" checklist-model="columns.selected" checklist-value="'contact_mail'" type="checkbox">
            <label for="checkbox-contact">
              {t}Contact{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-last-access" checklist-model="columns.selected" checklist-value="'last_login'" type="checkbox">
            <label for="checkbox-last-access">
              {t}Last access{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'created'" type="checkbox">
            <label for="checkbox-created">
              {t}Created{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-database" checklist-model="columns.selected" checklist-value="'database'" type="checkbox">
            <label for="checkbox-database">
              {t}Database{/t}
            </label>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 column">
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-contents" checklist-model="columns.selected" checklist-value="'contents'" type="checkbox">
            <label for="checkbox-contents">
              {t}Contents{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-articles" checklist-model="columns.selected" checklist-value="'articles'" type="checkbox">
            <label for="checkbox-articles">
              {t}Articles{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-opinions" checklist-model="columns.selected" checklist-value="'opinions'" type="checkbox">
            <label for="checkbox-opinions">
              {t}Opinions{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-advertisements" checklist-model="columns.selected" checklist-value="'advertisements'" type="checkbox">
            <label for="checkbox-advertisements">
              {t}Advertisements{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-albumns" checklist-model="columns.selected" checklist-value="'albumns'" type="checkbox">
            <label for="checkbox-albumns">
              {t}Albums{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-photos" checklist-model="columns.selected" checklist-value="'photos'" type="checkbox">
            <label for="checkbox-photos">
              {t}Photo{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-support" checklist-model="columns.selected" checklist-value="'support'" type="checkbox">
            <label for="checkbox-support">
              {t}Support plan{/t}
            </label>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 column">
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-videos" checklist-model="columns.selected" checklist-value="'videos'" type="checkbox">
            <label for="checkbox-videos">
              {t}Videos{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-widgets" checklist-model="columns.selected" checklist-value="'widgets'" type="checkbox">
            <label for="checkbox-widgets">
              {t}Widgets{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-static-pages" checklist-model="columns.selected" checklist-value="'static_pages'" type="checkbox">
            <label for="checkbox-static-pages">
              {t}Static pages{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-attachments" checklist-model="columns.selected" checklist-value="'attachments'" type="checkbox">
            <label for="checkbox-attachments">
              {t}Attachments{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-polls" checklist-model="columns.selected" checklist-value="'polls'" type="checkbox">
            <label for="checkbox-polls">
              {t}Polls{/t}
            </label>
          </div>
        </div>
        <div class="checkbox check-default">
          <input id="checkbox-letters" checklist-model="columns.selected" checklist-value="'letters'" type="checkbox">
          <label for="checkbox-letters">
            {t}Letters{/t}
          </label>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 column">
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-media-size" checklist-model="columns.selected" checklist-value="'media_size'" type="checkbox">
            <label for="checkbox-media-size">
              {t}Media size{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-alexa" checklist-model="columns.selected" checklist-value="'alexa'" type="checkbox">
            <label for="checkbox-alexa">
              {t}Alexa{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-page-views" checklist-model="columns.selected" checklist-value="'page_views'" type="checkbox">
            <label for="checkbox-page-views">
              {t}Page views{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-users" checklist-model="columns.selected" checklist-value="'users'" type="checkbox">
            <label for="checkbox-users">
              {t}Users{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-emails" checklist-model="columns.selected" checklist-value="'emails'" type="checkbox">
            <label for="checkbox-emails">
              {t}Emails{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-activated" checklist-model="columns.selected" checklist-value="'activated'" type="checkbox">
            <label for="checkbox-activated">
              {t}Enabled{/t}
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead ng-if="items.length >= 0">
            <tr>
              <th style="width:15px;">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="pointer" ng-click="sort('id')" width="50">
                {t}#{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
              </th>
              <th class="pointer" ng-click="sort('title')" ng-show="isEnabled('name')">
                {t}Title{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('title') == 'asc', 'fa fa-caret-down': isOrderedBy('title') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('instance')" width="10">
                {t}Instance{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('instance') == 'asc', 'fa fa-caret-down': isOrderedBy('instance') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('type')" width="100" width=10>
                {t}Type{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('type') == 'asc', 'fa fa-caret-down': isOrderedBy('type') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('start')" width="250">
                {t}Start{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('start') == 'asc', 'fa fa-caret-down': isOrderedBy('start') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('end')" width="250">
                {t}End{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('end') == 'asc', 'fa fa-caret-down': isOrderedBy('end') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('end')" width="10">
                {t}Fixed{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('end') == 'asc', 'fa fa-caret-down': isOrderedBy('end') == 'desc'}"></i>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-if="items.length == 0">
              <td class="empty" colspan="10">{t}There is no available instances yet{/t}</td>
            </tr>
            <tr ng-if="items.length >= 0" ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
              <td>
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td>
                [% item.id %]
              </td>
              <td ng-show="isEnabled('name')">
                <a ng-href="[% item.show_url %]" title="{t}Edit{/t}">
                  [% item.title['en'] %]
                </a>
                <div class="listing-inline-actions">
                  <a class="link" ng-href="[% routing.ngGenerate('manager_notification_show', { id: item.id }) %]" title="{t}Edit{/t}">
                    <i class="fa fa-pencil"></i>{t}Edit{/t}
                  </a>
                  <button class="link link-danger" ng-click="delete(item)" type="button">
                    <i class="fa fa-trash-o"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td>
                <div ng-repeat="id in item.instances">
                  [% extra.instances[id].name %]
                </div>
              </td>
              <td class="text-center">
                <i class="fa text-[% item.style %] p-b-10 p-l-10 p-r-10 p-t-10" ng-class="{ 'fa-comment': item.type === 'comment', 'fa-database': item.type === 'media', 'fa-envelope': item.type === 'email', 'fa-support': item.type === 'help', 'fa-info': item.type !== 'comment' && item.type !== 'media' && item.type !== 'email' && item.type !== 'help' && item.type !== 'user', 'fa-users': item.type === 'user' }"></i>
              </td>
              <td class="text-center">
                [% item.start %]
              </td>
              <td class="text-center">
                [% item.end %]
              </td>
              <td>
                <button class="btn btn-white" type="button" ng-click="setEnabled(item, item.fixed == '1' ? '0' : '1')">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.loading, 'fa-check text-success' : !item.loading &&item.fixed == '1', 'fa-times text-error': !item.loading && item.fixed == '0' }"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-left pagination-info" ng-if="items.length > 0">
        {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total|number %]
      </div>
      <div class="pull-right" ng-if="items.length > 0">
        <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="pagination.epp" ng-model="pagination.page" total-items="pagination.total" num-pages="pagination.pages"></pagination>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
