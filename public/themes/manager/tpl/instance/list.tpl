<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_instances_list') %]">
              <i class="fa fa-cubes"></i>
              {t}Instances{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_REPORT')">
            <a class="btn btn-link" ng-href="{url name=manager_ws_instances_csv}?ids=[% selected.items.join(); %]&token=[% security.token %]">
              <i class="fa fa-download fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks" ng-if="security.hasPermission('MASTER') || (security.hasPermission('INSTANCE_CREATE') && security.hasPermission('INSTANCE_REPORT') && security.instances.length < security.user.max_instances)">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks" ng-if="security.hasPermission('MASTER') || (security.hasPermission('INSTANCE_CREATE') && security.instances.length < security.user.max_instances)">
            <a class="btn btn-success text-uppercase" ng-href="[% routing.ngGenerate('manager_instance_create') %]">
              <i class="fa fa-plus m-r-5"></i>
              {t}Create{/t}
            </a>
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
          <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button">
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
        <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_REPORT')">
          <a class="btn btn-link" ng-href="{url name=manager_ws_instances_csv}?ids=[% selected.instances.join(); %]&token=[% security.token %]" uib-tooltip="{t}Download CSV of selected{/t}" tooltip-placement="bottom">
            <i class="fa fa-download fa-lg text-white"></i>
          </a>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_REPORT') && (security.hasPermission('INSTANCE_UPDATE') || security.hasPermission('INSTANCE_DELETE'))">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_UPDATE')">
          <button class="btn btn-link" ng-click="patchSelected('activated', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-times fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_UPDATE')">
          <button class="btn btn-link" ng-click="patchSelected('activated', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-check fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_UPDATE') && security.hasPermission('INSTANCE_DELETE')">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_DELETE')">
          <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon">
              <i class="fa fa-search fa-lg"></i>
            </span>
            <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.name }" ng-model="criteria.name" placeholder="{t}Search{/t}" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="clear('name')" ng-show="criteria.name">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <ui-select name="view" theme="select2" ng-model="criteria.country">
            <ui-select-match>
              <strong>{t}Country{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="country.id as country in extra.countries | filter: $select.search">
              <div ng-bind-html="country.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks">
          <ui-select name="view" theme="select2" ng-model="criteria.owner_id">
            <ui-select-match>
              <strong>{t}Owner{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="user.id as user in extra.users | filter: $select.search">
              <div ng-bind-html="user.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="criteria.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="resetFilters()" uib-tooltip="{t}Reset filters{/t}" tooltip-placement="bottom">
            <i class="fa fa-fire fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-lg fa-refresh" ng-class="{ 'fa-spin': loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="items">
  <div class="p-b-100 p-t-100 text-center" ng-if="items.length == 0">
    <i class="fa fa-7x fa-user-secret"></i>
    <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
  </div>
  <div class="grid simple" ng-if="items.length > 0">
    <div class="column-filters-toggle hidden-sm" ng-click="toggleColumns()"></div>
    <div class="column-filters collapsed hidden-sm" ng-class="{ 'collapsed': columns.collapsed }">
      <h5>{t}Columns{/t}</h5>
      <div class="row">
        <div class="col-sm-6 col-md-3 column">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
            <label for="checkbox-name">
              {t}Name{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-domains" checklist-model="columns.selected" checklist-value="'domains'" type="checkbox">
            <label for="checkbox-domains">
              {t}Domains{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-domain-expire" checklist-model="columns.selected" checklist-value="'domain_expire'" type="checkbox">
            <label for="checkbox-domain-expire">
              {t}Domain expire{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-country" checklist-model="columns.selected" checklist-value="'country'" type="checkbox">
            <label for="checkbox-country">
              {t}Country{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-contact" checklist-model="columns.selected" checklist-value="'contact_mail'" type="checkbox">
            <label for="checkbox-contact">
              {t}Contact{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-last-access" checklist-model="columns.selected" checklist-value="'last_login'" type="checkbox">
            <label for="checkbox-last-access">
              {t}Last activity{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'created'" type="checkbox">
            <label for="checkbox-created">
              {t}Created{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-database" checklist-model="columns.selected" checklist-value="'database'" type="checkbox">
            <label for="checkbox-database">
              {t}Database{/t}
            </label>
          </div>
        </div>
        <div class="col-sm-6 col-md-3 column">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-theme" checklist-model="columns.selected" checklist-value="'theme'" type="checkbox">
            <label for="checkbox-theme">
              {t}Theme{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-support" checklist-model="columns.selected" checklist-value="'support'" type="checkbox">
            <label for="checkbox-support">
              {t}Support plan{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-contents" checklist-model="columns.selected" checklist-value="'contents'" type="checkbox">
            <label for="checkbox-contents">
              {t}Contents{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-articles" checklist-model="columns.selected" checklist-value="'articles'" type="checkbox">
            <label for="checkbox-articles">
              {t}Articles{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-opinions" checklist-model="columns.selected" checklist-value="'opinions'" type="checkbox">
            <label for="checkbox-opinions">
              {t}Opinions{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-advertisements" checklist-model="columns.selected" checklist-value="'advertisements'" type="checkbox">
            <label for="checkbox-advertisements">
              {t}Advertisements{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-albums" checklist-model="columns.selected" checklist-value="'albums'" type="checkbox">
            <label for="checkbox-albums">
              {t}Albums{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-photos" checklist-model="columns.selected" checklist-value="'photos'" type="checkbox">
            <label for="checkbox-photos">
              {t}Photos{/t}
            </label>
          </div>
        </div>
        <div class="col-sm-6 col-md-3 column">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-videos" checklist-model="columns.selected" checklist-value="'videos'" type="checkbox">
            <label for="checkbox-videos">
              {t}Videos{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-widgets" checklist-model="columns.selected" checklist-value="'widgets'" type="checkbox">
            <label for="checkbox-widgets">
              {t}Widgets{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-static-pages" checklist-model="columns.selected" checklist-value="'static_pages'" type="checkbox">
            <label for="checkbox-static-pages">
              {t}Static pages{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-attachments" checklist-model="columns.selected" checklist-value="'attachments'" type="checkbox">
            <label for="checkbox-attachments">
              {t}Attachments{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-polls" checklist-model="columns.selected" checklist-value="'polls'" type="checkbox">
            <label for="checkbox-polls">
              {t}Polls{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-letters" checklist-model="columns.selected" checklist-value="'letters'" type="checkbox">
            <label for="checkbox-letters">
              {t}Letters{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-users" checklist-model="columns.selected" checklist-value="'users'" type="checkbox">
            <label for="checkbox-users">
              {t}Users{/t}
            </label>
          </div>
        </div>
        <div class="col-sm-6 col-md-3 column">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-emails" checklist-model="columns.selected" checklist-value="'emails'" type="checkbox">
            <label for="checkbox-emails">
              {t}Emails{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-media-size" checklist-model="columns.selected" checklist-value="'media_size'" type="checkbox">
            <label for="checkbox-media-size">
              {t}Media size{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-alexa" checklist-model="columns.selected" checklist-value="'alexa'" type="checkbox">
            <label for="checkbox-alexa">
              {t}Alexa{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-page-views" checklist-model="columns.selected" checklist-value="'page_views'" type="checkbox">
            <label for="checkbox-page-views">
              {t}Page views{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-blocked" checklist-model="columns.selected" checklist-value="'blocked'" type="checkbox">
            <label for="checkbox-blocked">
              {t}Blocked{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-activated" checklist-model="columns.selected" checklist-value="'activated'" type="checkbox">
            <label for="checkbox-activated">
              {t}Enabled{/t}
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead>
            <tr>
              <th width="15">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll()">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="pointer" ng-click="sort('id')" width="50">
                {t}#{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
              </th>
              <th class="pointer" ng-click="sort('name')" ng-show="isColumnEnabled('name')">
                {t}Name{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('domains')" ng-show="isColumnEnabled('domains')">
                {t}Domains{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('domains') == 'asc', 'fa fa-caret-down': isOrderedBy('domains') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('domain_expire')" ng-show="isColumnEnabled('domain_expire')">
                {t}Domain expire{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('domain_expire') == 'asc', 'fa fa-caret-down': isOrderedBy('domain_expire') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('country')" ng-show="isColumnEnabled('country')">
                {t}Country{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('country') == 'asc', 'fa fa-caret-down': isOrderedBy('country') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('contact_email')" ng-show="isColumnEnabled('contact_mail')">
                {t}Contact{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('contact_mail') == 'asc', 'fa fa-caret-down': isOrderedBy('contact_mail') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('last_login')" ng-show="isColumnEnabled('last_login')">
                {t}Last activity{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('last_login') == 'asc', 'fa fa-caret-down': isOrderedBy('last_login') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('created')" ng-show="isColumnEnabled('created')">
                {t}Created{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('created') == 'asc', 'fa fa-caret-down': isOrderedBy('created') == 'desc'}"></i>
              </th>
              <th class="text-center" ng-show="isColumnEnabled('database')">
                {t}Database{/t}
              </th>
              <th class="text-center" ng-show="isColumnEnabled('theme')">
                {t}Theme{/t}
              </th>
              <th class="text-center" ng-show="isColumnEnabled('support')">
                {t}Support plan{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('support') == 'asc', 'fa fa-caret-down': isOrderedBy('support') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('contents')" ng-show="isColumnEnabled('contents')" width="80">
                <i class="fa fa-folder-open-o" uib-tooltip="{t}Contents{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('contents') == 'asc', 'fa fa-caret-down': isOrderedBy('contents') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('articles')" ng-show="isColumnEnabled('articles')" width="80">
                <i class="fa fa-file-text" uib-tooltip="{t}Articles{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('articles') == 'asc', 'fa fa-caret-down': isOrderedBy('articles') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('opinions')" ng-show="isColumnEnabled('opinions')" width="80">
                <i class="fa fa-quote-right" uib-tooltip="{t}Opinions{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('opinions') == 'asc', 'fa fa-caret-down': isOrderedBy('opinions') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('advertisements')" ng-show="isColumnEnabled('advertisements')" width="80">
                <i class="fa fa-bullhorn" uib-tooltip="{t}Advertisements{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('advertisements') == 'asc', 'fa fa-caret-down': isOrderedBy('advertisements') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('albums')" ng-show="isColumnEnabled('albums')" width="80">
                <i class="fa fa-stack-overflow" uib-tooltip="{t}Albums{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('albums') == 'asc', 'fa fa-caret-down': isOrderedBy('albums') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('photos')" ng-show="isColumnEnabled('photos')" width="80">
                <i class="fa fa-picture-o" uib-tooltip="{t}Photos{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('photos') == 'asc', 'fa fa-caret-down': isOrderedBy('photos') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('videos')" ng-show="isColumnEnabled('videos')" width="80">
                <i class="fa fa-film" uib-tooltip="{t}Videos{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('videos') == 'asc', 'fa fa-caret-down': isOrderedBy('videos') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('widgets')" ng-show="isColumnEnabled('widgets')" width="80">
                <i class="fa fa-puzzle-piece" uib-tooltip="{t}Widgets{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('widgets') == 'asc', 'fa fa-caret-down': isOrderedBy('widgets') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('static_pages')" ng-show="isColumnEnabled('static_pages')" width="80">
                <i class="fa fa-file-o" uib-tooltip="{t}Static pages{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('static_pages') == 'asc', 'fa fa-caret-down': isOrderedBy('static_pages') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('attachments')" ng-show="isColumnEnabled('attachments')" width="80">
                <i class="fa fa-paperclip" uib-tooltip="{t}Attachments{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('attachments') == 'asc', 'fa fa-caret-down': isOrderedBy('attachments') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('polls')" ng-show="isColumnEnabled('polls')" width="80">
                <i class="fa fa-pie-chart" uib-tooltip="{t}Polls{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('polls') == 'asc', 'fa fa-caret-down': isOrderedBy('polls') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('letters')" ng-show="isColumnEnabled('letters')" width="80">
                <i class="fa fa-envelope" uib-tooltip="{t}Letter{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('letters') == 'asc', 'fa fa-caret-down': isOrderedBy('letters') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('users')" ng-show="isColumnEnabled('users')">
                <i class="fa fa-users" uib-tooltip="{t}Users{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('users') == 'asc', 'fa fa-caret-down': isOrderedBy('users') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('emails')" ng-show="isColumnEnabled('emails')">
                <i class="fa fa-mail-forward" uib-tooltip="{t}Emails{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('emails') == 'asc', 'fa fa-caret-down': isOrderedBy('emails') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('media_size')" ng-show="isColumnEnabled('media_size')" width="120">
                <i class="fa fa-database" uib-tooltip="{t}Media size{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('media_size') == 'asc', 'fa fa-caret-down': isOrderedBy('media_size') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('alexa')" ng-show="isColumnEnabled('alexa')" width="120">
                <i class="fa fa-line-chart" uib-tooltip="{t}Alexa{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('alexa') == 'asc', 'fa fa-caret-down': isOrderedBy('alexa') == 'desc'}"></i>
              </th>
              <th class="text-center pointer" ng-click="sort('page_views')" ng-show="isColumnEnabled('page_views')" width="120">
                <i class="fa fa-eye" uib-tooltip="{t}Page views{/t}" tooltip-placement="bottom"></i>
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('page_views') == 'asc', 'fa fa-caret-down': isOrderedBy('page_views') == 'desc'}"></i>
              </th>
              <th class="text-center" ng-show="isColumnEnabled('blocked')" width="60">
                <span>
                  <i class="fa fa-lock"></i>
                </span>
              </th>
              <th class="text-center pointer" ng-click="sort('activated')" ng-show="isColumnEnabled('activated')" width="60">
                <span>
                  <i class="fa fa-check"></i>
                  <i ng-class="{ 'fa fa-caret-up': isOrderedBy('activated') == 'asc', 'fa fa-caret-down': isOrderedBy('activated') == 'desc'}"></i>
                </span>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
              <td>
                <div class="checkbox check-default" ng-if="security.hasInstance(item.internal_name)">
                  <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td>
                [% item.id %]
              </td>
              <td ng-show="isColumnEnabled('name')">
                <i class="flag flag-[% item.country.toLowerCase() %] m-r-5" ng-if="!isColumnEnabled('country')" uib-tooltip="[% getCountry(item.country) %]"></i>
                [% item.name %]
                <div class="listing-inline-actions">
                  <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_instance_show', { id: item.id }) %]" ng-if="security.hasInstance(item.internal_name) && security.hasPermission('INSTANCE_UPDATE')" title="{t}Edit{/t}">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <button class="btn btn-link text-danger" ng-click="delete(item.id)" ng-if="security.hasInstance(item.internal_name) && security.hasPermission('INSTANCE_DELETE')" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td ng-show="isColumnEnabled('domains')">
                <div class="domains">
                  <small>
                    <ul class="domain-list no-style" ng-if="item.domains.length > 1">
                      <li ng-repeat="domain in item.domains">
                        <a href="http://[% domain %]" ng-class="{ 'active': $index == item.main_domain - 1 }" target="_blank">[% domain %]</a>
                      </li>
                    </ul>
                    <span ng-if="item.domains.length <= 1">
                      <span ng-repeat="domain in item.domains">
                        <a href="http://[% domain %]" target="_blank" title="[% item.name %]">[% domain %]</a>
                      </span>
                    </span>
                  </small>
                </div>
              </td>
              <td ng-show="isColumnEnabled('domain_expire')">
                [% item.domain_expire %]
              </td>
              <td ng-show="isColumnEnabled('country')">
                <i class="flag flag-[% item.country.toLowerCase() %] m-r-5" ng-if="item.country"></i>
                <span ng-if="item.country">[% getCountry(item.country) %]</span>
                <i ng-if="!item.country">{t}None{/t}</i>
              </td>
              <td ng-show="isColumnEnabled('contact_mail')">
                <div class="creator">
                  <a ng-href="mailto:[% item.contact_mail %]" title="Send an email to the instance manager"> [% item.contact_mail %]</a>
                </div>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('last_login')">
                [% item.last_login %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('created')">
                [% item.created %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('database')">
                [% item.settings.BD_DATABASE %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('theme')">
                [% item.settings.TEMPLATE_USER %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('support')">
                <span ng-repeat="uuid in item.activated_modules | filter: 'SUPPORT_'">[% uuid %]</span>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('contents')" title="{t}Contents{/t}">
                [% item.contents %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('articles')" title="{t}Articles{/t}">
                [% item.articles %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('opinions')" title="{t}Opinions{/t}">
                [% item.opinions %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('advertisements')" title="{t}Advertisements{/t}">
                [% item.advertisements %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('albums')" title="{t}Albums{/t}">
                [% item.albums %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('photos')" title="{t}Photos{/t}">
                [% item.photos %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('videos')" title="{t}Videos{/t}">
                [% item.videos %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('widgets')" title="{t}Widgets{/t}">
                [% item.widgets %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('static_pages')" title="{t}Static pages{/t}">
                [% item.static_pages %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('attachments')" title="{t}Attachments{/t}">
                [% item.attachments %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('polls')" title="{t}Polls{/t}">
                [% item.polls %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('letters')" title="{t}Letters{/t}">
                [% item.letters %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('users')" title="{t}Users{/t}">
                [% item.users %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('emails')" title="{t}Emails{/t}">
                [% item.emails %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('media_size')" title="{t}Media size{/t}">
                [% item.media_size | number : 2 %] Mb
              </td>
              <td class="text-center" ng-show="isColumnEnabled('alexa')" title="{t}Alexa{/t}">
                [% item.alexa == 100000000 ? '{t}No rank{/t}' : item.alexa %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('page_views')" title="{t}Page views{/t}">
                [% item.page_views %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('blocked')">
                <button class="btn btn-loading btn-white" type="button" ng-click="patch(item, 'blocked', item.blocked == '1' ? '0' : '1')" ng-if="security.hasInstance(item.internal_name) && security.hasPermission('INSTANCE_UPDATE')">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.blockedLoading, 'fa-lock text-error' : !item.blockedLoading && item.blocked == '1', 'fa-unlock-alt text-success': !item.blockedLoading && (!item.blocked || item.blocked == '0') }"></i>
                </button>
                <span ng-if="!security.hasInstance(item.internal_name) || !security.hasPermission('INSTANCE_UPDATE')">
                  <i class="fa m-t-5" ng-class="{ 'fa-lock text-error' : item.blocked == '1', 'fa-unlock-alt text-success': !item.blocked || item.blocked == '0' }"></i>
                </span>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('activated')">
                <button class="btn btn-loading btn-white" type="button" ng-click="patch(item, 'activated', item.activated == '1' ? '0' : '1')" ng-if="security.hasInstance(item.internal_name) && security.hasPermission('INSTANCE_UPDATE')">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.activatedLoading, 'fa-check text-success' : !item.activatedLoading && item.activated == '1', 'fa-times text-error': !item.activatedLoading && item.activated == '0' }"></i>
                </button>
                <span ng-if="!security.hasInstance(item.internal_name) || !security.hasPermission('INSTANCE_UPDATE')">
                  <i class="fa m-t-5" ng-class="{ 'fa-check text-success' : item.activated == '1', 'fa-times text-error': item.activated == '0' }"></i>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-right">
        <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
