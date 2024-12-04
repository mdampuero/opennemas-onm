{extends file="common/extension/list.table.tpl"}

{block name="columns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-country" checklist-model="columns.selected" checklist-value="'country'" type="checkbox">
    <label for="checkbox-country">
      {t}Country{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-internal-name" checklist-model="columns.selected" checklist-value="'internal_name'" type="checkbox">
    <label for="checkbox-internal-name">
      {t}Internal name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-domains" checklist-model="columns.selected" checklist-value="'domains'" type="checkbox">
    <label for="checkbox-domains">
      {t}Domains{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-subdirectory" checklist-model="columns.selected" checklist-value="'subdirectory'" type="checkbox">
    <label for="checkbox-subdirectory">
      {t}Subdirectory{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-theme" checklist-model="columns.selected" checklist-value="'theme'" type="checkbox">
    <label for="checkbox-theme">
      {t}Theme{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-contact" checklist-model="columns.selected" checklist-value="'contact_mail'" type="checkbox">
    <label for="checkbox-contact">
      {t}Contact{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'created'" type="checkbox">
    <label for="checkbox-created">
      {t}Created{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-last-access" checklist-model="columns.selected" checklist-value="'last_login'" type="checkbox">
    <label for="checkbox-last-access">
      {t}Last activity{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-users" checklist-model="columns.selected" checklist-value="'users'" type="checkbox">
    <label for="checkbox-users">
      {t}Users{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-webpush-subscribers" checklist-model="columns.selected" checklist-value="'webpush_subscribers'" type="checkbox">
    <label for="checkbox-webpush-subscribers">
      {t}Web Push subscribers{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-page-views" checklist-model="columns.selected" checklist-value="'page_views'" type="checkbox">
    <label for="checkbox-page-views">
      {t}Page views{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-emails" checklist-model="columns.selected" checklist-value="'emails'" type="checkbox">
    <label for="checkbox-emails">
      {t}Emails{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-contents" checklist-model="columns.selected" checklist-value="'contents'" type="checkbox">
    <label for="checkbox-contents">
      {t}Contents{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-articles" checklist-model="columns.selected" checklist-value="'articles'" type="checkbox">
    <label for="checkbox-articles">
      {t}Articles{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-comments" checklist-model="columns.selected" checklist-value="'comments'" type="checkbox">
    <label for="checkbox-comments">
      {t}Comments{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-opinions" checklist-model="columns.selected" checklist-value="'opinions'" type="checkbox">
    <label for="checkbox-opinions">
      {t}Opinions{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-static-pages" checklist-model="columns.selected" checklist-value="'static_pages'" type="checkbox">
    <label for="checkbox-static-pages">
      {t}Static pages{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-albums" checklist-model="columns.selected" checklist-value="'albums'" type="checkbox">
    <label for="checkbox-albums">
      {t}Albums{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-photos" checklist-model="columns.selected" checklist-value="'photos'" type="checkbox">
    <label for="checkbox-photos">
      {t}Photos{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-videos" checklist-model="columns.selected" checklist-value="'videos'" type="checkbox">
    <label for="checkbox-videos">
      {t}Videos{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-attachments" checklist-model="columns.selected" checklist-value="'attachments'" type="checkbox">
    <label for="checkbox-attachments">
      {t}Attachments{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-kioskos" checklist-model="columns.selected" checklist-value="'kioskos'" type="checkbox">
    <label for="checkbox-kioskos">
      {t}Kioskos{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-media-size" checklist-model="columns.selected" checklist-value="'media_size'" type="checkbox">
    <label for="checkbox-media-size">
      {t}Media size{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-advertisements" checklist-model="columns.selected" checklist-value="'advertisements'" type="checkbox">
    <label for="checkbox-advertisements">
      {t}Advertisements{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-polls" checklist-model="columns.selected" checklist-value="'polls'" type="checkbox">
    <label for="checkbox-polls">
      {t}Polls{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-letters" checklist-model="columns.selected" checklist-value="'letters'" type="checkbox">
    <label for="checkbox-letters">
      {t}Letters{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-events" checklist-model="columns.selected" checklist-value="'events'" type="checkbox">
    <label for="checkbox-events">
      {t}Events{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-tags" checklist-model="columns.selected" checklist-value="'tags'" type="checkbox">
    <label for="checkbox-tags">
      {t}Tags{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-widgets" checklist-model="columns.selected" checklist-value="'widgets'" type="checkbox">
    <label for="checkbox-widgets">
      {t}Widgets{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-blocked" checklist-model="columns.selected" checklist-value="'blocked'" type="checkbox">
    <label for="checkbox-blocked">
      {t}Blocked{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-activated" checklist-model="columns.selected" checklist-value="'activated'" type="checkbox">
    <label for="checkbox-activated">
      {t}Enabled{/t}
    </label>
  </div>
{/block}

{block name="columnsHeader"}
  <th class="pointer text-center" ng-click="sort('id')" width="50">
    {t}#{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('country')" ng-show="isColumnEnabled('country')" width="50">
    <i class="fa fa-globe" uib-tooltip="{t}Country{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('country') == 'asc', 'fa fa-caret-down': isOrderedBy('country') == 'desc'}"></i>
  </th>
  <th class="pointer" ng-click="sort('name')" ng-show="isColumnEnabled('name')" width="250">
    {t}Name{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
  </th>
  <th class="pointer" ng-click="sort('internal_name')" ng-show="isColumnEnabled('internal_name')" width="200">
    {t}Internal name{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('internal_name') == 'asc', 'fa fa-caret-down': isOrderedBy('internal_name') == 'desc'}"></i>
  </th>
  <th class="pointer" ng-click="sort('domains')" ng-show="isColumnEnabled('domains')" width="300">
    {t}Domains{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('domains') == 'asc', 'fa fa-caret-down': isOrderedBy('domains') == 'desc'}"></i>
  </th>
  <th class="pointer" ng-show="isColumnEnabled('subdirectory')" width="150">
    {t}Subdirectory{/t}
  </th>
  <th class="text-center pointer" ng-click="sort('theme')" ng-show="isColumnEnabled('theme')" width="200">
    {t}Theme{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('theme') == 'asc', 'fa fa-caret-down': isOrderedBy('theme') == 'desc'}"></i>
  </th>
  <th class="pointer text-center" ng-click="sort('contact_email')" ng-show="isColumnEnabled('contact_mail')" width="200">
    {t}Contact{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('contact_mail') == 'asc', 'fa fa-caret-down': isOrderedBy('contact_mail') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('created')" ng-show="isColumnEnabled('created')" width="180">
    {t}Created{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('created') == 'asc', 'fa fa-caret-down': isOrderedBy('created') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('last_login')" ng-show="isColumnEnabled('last_login')" width="180">
    {t}Last activity{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('last_login') == 'asc', 'fa fa-caret-down': isOrderedBy('last_login') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('users')" ng-show="isColumnEnabled('users')" width="80">
    <i class="fa fa-users" uib-tooltip="{t}Users{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('users') == 'asc', 'fa fa-caret-down': isOrderedBy('users') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('webpush_subscribers')" ng-show="isColumnEnabled('webpush_subscribers')" width="80">
    <i class="fa fa-bell" uib-tooltip="{t}Web Push{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('webpush_subscribers') == 'asc', 'fa fa-caret-down': isOrderedBy('webpush_subscribers') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('page_views')" ng-show="isColumnEnabled('page_views')" width="80">
    <i class="fa fa-eye" uib-tooltip="{t}Page views{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('page_views') == 'asc', 'fa fa-caret-down': isOrderedBy('page_views') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('emails')" ng-show="isColumnEnabled('emails')" width="80">
    <i class="fa fa-mail-forward" uib-tooltip="{t}Emails{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('emails') == 'asc', 'fa fa-caret-down': isOrderedBy('emails') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('contents')" ng-show="isColumnEnabled('contents')" width="80">
    <i class="fa fa-folder-open-o" uib-tooltip="{t}Contents{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('contents') == 'asc', 'fa fa-caret-down': isOrderedBy('contents') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('articles')" ng-show="isColumnEnabled('articles')" width="80">
    <i class="fa fa-file-text" uib-tooltip="{t}Articles{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('articles') == 'asc', 'fa fa-caret-down': isOrderedBy('articles') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('comments')" ng-show="isColumnEnabled('comments')" width="80">
    <i class="fa fa-comments" uib-tooltip="{t}Comments{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('comments') == 'asc', 'fa fa-caret-down': isOrderedBy('comments') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('opinions')" ng-show="isColumnEnabled('opinions')" width="80">
    <i class="fa fa-quote-right" uib-tooltip="{t}Opinions{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('opinions') == 'asc', 'fa fa-caret-down': isOrderedBy('opinions') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('static_pages')" ng-show="isColumnEnabled('static_pages')" width="80">
    <i class="fa fa-file-o" uib-tooltip="{t}Static pages{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('static_pages') == 'asc', 'fa fa-caret-down': isOrderedBy('static_pages') == 'desc'}"></i>
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
  <th class="text-center pointer" ng-click="sort('attachments')" ng-show="isColumnEnabled('attachments')" width="80">
    <i class="fa fa-paperclip" uib-tooltip="{t}Attachments{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('attachments') == 'asc', 'fa fa-caret-down': isOrderedBy('attachments') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('kioskos')" ng-show="isColumnEnabled('kioskos')" width="80">
    <i class="fa fa-newspaper-o" uib-tooltip="{t}Kioskos{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('kioskos') == 'asc', 'fa fa-caret-down': isOrderedBy('kioskos') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('media_size')" ng-show="isColumnEnabled('media_size')" width="80">
    <i class="fa fa-database" uib-tooltip="{t}Media size{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('media_size') == 'asc', 'fa fa-caret-down': isOrderedBy('media_size') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('advertisements')" ng-show="isColumnEnabled('advertisements')" width="80">
    <i class="fa fa-bullhorn" uib-tooltip="{t}Advertisements{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('advertisements') == 'asc', 'fa fa-caret-down': isOrderedBy('advertisements') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('polls')" ng-show="isColumnEnabled('polls')" width="80">
    <i class="fa fa-pie-chart" uib-tooltip="{t}Polls{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('polls') == 'asc', 'fa fa-caret-down': isOrderedBy('polls') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('letters')" ng-show="isColumnEnabled('letters')" width="80">
    <i class="fa fa-envelope" uib-tooltip="{t}Letter{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('letters') == 'asc', 'fa fa-caret-down': isOrderedBy('letters') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('events')" ng-show="isColumnEnabled('events')" width="80">
    <i class="fa fa-calendar" uib-tooltip="{t}Events{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('events') == 'asc', 'fa fa-caret-down': isOrderedBy('events') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('tags')" ng-show="isColumnEnabled('tags')" width="80">
    <i class="fa fa-tags" uib-tooltip="{t}Tags{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('tags') == 'asc', 'fa fa-caret-down': isOrderedBy('tags') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('widgets')" ng-show="isColumnEnabled('widgets')" width="80">
    <i class="fa fa-puzzle-piece" uib-tooltip="{t}Widgets{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('widgets') == 'asc', 'fa fa-caret-down': isOrderedBy('widgets') == 'desc'}"></i>
  </th>
  <th class="text-center pointer" ng-show="isColumnEnabled('blocked')" width="60">
    <i class="fa fa-lock" uib-tooltip="{t}Blocked{/t}" tooltip-placement="bottom"></i>
  </th>
  <th class="text-center pointer" ng-click="sort('activated')" ng-show="isColumnEnabled('activated')" width="60">
    <i class="fa fa-check" uib-tooltip="{t}Activated{/t}" tooltip-placement="bottom"></i>
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('activated') == 'asc', 'fa fa-caret-down': isOrderedBy('activated') == 'desc'}"></i>
  </th>
{/block}

{block name="columnsBody"}
  <td class="text-center v-align-middle">
    [% item.id %]
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('country')">
    <i class="flag flag-[% item.country.toLowerCase() %]" uib-tooltip="[% getCountry(item.country) %]"></i>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('name')" title="[% item.name %]">
    <div class="table-text">
      [% item.name %]
    </div>
    <div class="listing-inline-actions">
      <a class="btn btn-default btn-small" ng-href="[% routing.ngGenerate('manager_instance_show', { id: item.id }) %]" ng-if="security.hasInstance(item.internal_name) && security.hasPermission('INSTANCE_UPDATE')" title="{t}Edit{/t}">
        <i class="fa fa-pencil m-r-5"></i>
        {t}Edit{/t}
      </a>
      <button class="btn btn-danger btn-small" ng-click="delete(item.id)" ng-if="security.hasInstance(item.internal_name) && security.hasPermission('INSTANCE_DELETE')" type="button">
        <i class="fa fa-trash-o m-r-5"></i>
        {t}Delete{/t}
      </button>
    </div>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('internal_name')">
    <div class="table-text">
      [% item.internal_name %]
    </div>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('domains')">
    <div class="domains">
      <small>
        <ul class="domain-list no-style" ng-if="item.domains.length > 1">
          <li ng-repeat="domain in item.domains">
            <a href="http://[% domain %]" ng-class="{ 'active': $index == item.main_domain - 1 }" target="_blank">
              [% domain %]
            </a>
          </li>
        </ul>
        <span ng-if="item.domains.length <= 1">
          <span ng-repeat="domain in item.domains">
            <a href="http://[% domain %]" target="_blank" title="[% item.name %]">
              [% domain %]
            </a>
          </span>
        </span>
      </small>
    </div>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('subdirectory')" title="{t}Subdirectory{/t}">
    <span class="label" ng-if="item.subdirectory.length > 0">
      <a href="http://[% item.domains[item.main_domain - 1] %]/[% item.subdirectory %]" target="_blank">
        [% item.subdirectory %]
      </a>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('theme')">
    <span class="label label-default text-bold">
      [% item.settings.TEMPLATE_USER %]
    </span>
  </td>
  <td class="v-align-middle table-text" ng-show="isColumnEnabled('contact_mail')">
    <a ng-href="mailto:[% item.contact_mail %]">
      [% item.contact_mail %]
    </a>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('created')">
    <div>
      <i class="fa fa-calendar"></i>
      [% item.created | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold">
      <i class="fa fa-clock-o"></i>
      [% item.created | moment : 'HH:mm:ss' %]
    </small>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('last_login')">
    <div>
      <i class="fa fa-calendar"></i>
      [% item.last_login | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold">
      <i class="fa fa-clock-o"></i>
      [% item.last_login | moment : 'HH:mm:ss' %]
    </small>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('users')" title="{t}Users{/t}">
    <span class="badge text-bold">
      [% item.users %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('webpush_subscribers')" title="{t}Web Push{/t}">
    <span class="badge text-bold">
      [% item.webpush_subscribers %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('page_views')" title="{t}Page views{/t}">
    <span class="badge text-bold">
      [% item.page_views %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('emails')" title="{t}Emails{/t}">
    <span class="badge text-bold">
      [% item.emails %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('contents')" title="{t}Contents{/t}">
    <span class="badge text-bold">
      [% item.contents %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('articles')" title="{t}Articles{/t}">
    <span class="badge text-bold">
      [% item.articles %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('comments')" title="{t}Comments{/t}">
    <span class="badge text-bold">
      [% item.comments %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('opinions')" title="{t}Opinions{/t}">
    <span class="badge text-bold">
      [% item.opinions %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('static_pages')" title="{t}Static pages{/t}">
    <span class="badge text-bold">
      [% item.static_pages %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('albums')" title="{t}Albums{/t}">
    <span class="badge text-bold">
      [% item.albums %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('photos')" title="{t}Photos{/t}">
    <span class="badge text-bold">
      [% item.photos %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('videos')" title="{t}Videos{/t}">
    <span class="badge text-bold">
      [% item.videos %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('attachments')" title="{t}Attachments{/t}">
    <span class="badge text-bold">
      [% item.attachments %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('kioskos')" title="{t}Kioskos{/t}">
    <span class="badge text-bold">
      [% item.kioskos %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('media_size')" title="{t}Media size{/t}">
    <span class="badge text-bold">
      [% item.media_size / 1024 | number : 2 %]
      MB
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('advertisements')" title="{t}Advertisements{/t}">
    <span class="badge text-bold">
      [% item.advertisements %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('polls')" title="{t}Polls{/t}">
    <span class="badge text-bold">
      [% item.polls %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('letters')" title="{t}Letters{/t}">
    <span class="badge text-bold">
      [% item.letters %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('events')" title="{t}Events{/t}">
    <span class="badge text-bold">
      [% item.events %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('tags')" title="{t}Tags{/t}">
    <span class="badge text-bold">
      [% item.tags %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('widgets')" title="{t}Widgets{/t}">
    <span class="badge text-bold">
      [% item.widgets %]
    </span>
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
{/block}
