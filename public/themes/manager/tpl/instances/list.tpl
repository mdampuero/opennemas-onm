<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-cubes fa-lg"></i>
                        {t}Instances{/t}
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" ng-href="{url name=manager_ws_instances_list_export}?ids=[% selected.instances.join(); %]">
                            <i class="fa fa-download fa-lg"></i>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_create') %]" class="btn btn-primary">
                            <i class="fa fa-plus fa-lg"></i>
                            {t}Create{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="page-navbar selected-navbar" ng-class="{ 'collapsed': selected.instances.length == 0 }">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section pull-left">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-check"></i>
                        [% selected.instances.length %] {t}items selected{/t}
                    </h4>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="selected.instances = []; selected.all = 0" tooltip="{t}Clear selection{/t}" tooltip-placement="bottom" type="button">
                      {t}Deselect{/t}
                    </button>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <a class="btn btn-link" ng-href="{url name=manager_ws_instances_list_export}?ids=[% selected.instances.join(); %]" tooltip="{t}Download CSV of selected{/t}" tooltip-placement="bottom">
                        <i class="fa fa-download fa-lg"></i>
                    </a>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="setEnabledSelected(0)" tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                        <i class="fa fa-times fa-lg"></i>
                    </button>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="setEnabledSelected(1)" tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                        <i class="fa fa-check fa-lg"></i>
                    </button>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="deleteSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
                <li class="m-r-10 input-prepend inside search-form no-boarder">
                    <span class="add-on">
                        <span class="fa fa-search fa-lg"></span>
                    </span>
                    <input class="no-boarder" ng-keyup="searchByKeypress($event)" placeholder="{t}Filter by name, domain or contact{/t}" ng-model="criteria.name_like[0].value" type="text" style="width:250px;"/>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks hidden-xs">
                    <select class="xmedium" ng-model="epp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                    </select>
                </li>
                <li class="quicklinks hidden-xs">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="criteria = {  name_like: [ { value: '', operator: 'like' } ]}; orderBy = [ { name: 'last_login', value: 'desc' } ]; page = 1; epp = 25; refresh()">
                        <i class="fa fa-trash-o fa-lg"></i>
                    </button>
                </li>
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="refresh()">
                        <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
                    </button>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks toggle-columns">
                    <div class="btn btn-link" ng-class="{ 'active': !columns.collapsed }" ng-click="toggleColumns()" tooltip-html-unsafe="{t}Columns{/t}" tooltip-placement="left">
                        <i class="fa fa-columns"></i>
                    </div>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks form-inline pagination-links">
                    <div class="btn-group">
                        <button class="btn btn-white" ng-click="page = page - 1" ng-disabled="page - 1 < 1" type="button">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <!-- <input class="form-control hidden" type="text" value="[% page + '/' + pages %]" readonly> -->
                        <button class="btn btn-white" ng-click="page = page + 1" ng-disabled="page == pages" type="button">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="content">
    <div class="row column-filters" ng-class="{ 'collapsed': columns.collapsed }">
        <div class="row">
            <div class="col-xs-12 title">
                <h5>{t}Columns{/t}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-3 column">
                <div class="checkbox check-default">
                    <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
                    <label for="checkbox-name">
                        {t}Name{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-domains" checklist-model="columns.selected" checklist-value="'domains'" type="checkbox">
                    <label for="checkbox-domains">
                        {t}Domains{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-domain-expire" checklist-model="columns.selected" checklist-value="'domain_expire'" type="checkbox">
                    <label for="checkbox-domain-expire">
                        {t}Domain expire{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-contact" checklist-model="columns.selected" checklist-value="'contact_mail'" type="checkbox">
                    <label for="checkbox-contact">
                        {t}Contact{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-last-access" checklist-model="columns.selected" checklist-value="'last_login'" type="checkbox">
                    <label for="checkbox-last-access">
                        {t}Last access{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'created'" type="checkbox">
                    <label for="checkbox-created">
                        {t}Created{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-database" checklist-model="columns.selected" checklist-value="'database'" type="checkbox">
                    <label for="checkbox-database">
                        {t}Database{/t}
                    </label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 column">
                <div class="checkbox check-default">
                    <input id="checkbox-contents" checklist-model="columns.selected" checklist-value="'contents'" type="checkbox">
                    <label for="checkbox-contents">
                        {t}Contents{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-articles" checklist-model="columns.selected" checklist-value="'articles'" type="checkbox">
                    <label for="checkbox-articles">
                        {t}Articles{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-opinions" checklist-model="columns.selected" checklist-value="'opinions'" type="checkbox">
                    <label for="checkbox-opinions">
                        {t}Opinions{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-advertisements" checklist-model="columns.selected" checklist-value="'advertisements'" type="checkbox">
                    <label for="checkbox-advertisements">
                        {t}Advertisements{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-albumns" checklist-model="columns.selected" checklist-value="'albumns'" type="checkbox">
                    <label for="checkbox-albumns">
                        {t}Albums{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-photos" checklist-model="columns.selected" checklist-value="'photos'" type="checkbox">
                    <label for="checkbox-photos">
                        {t}Photo{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-support" checklist-model="columns.selected" checklist-value="'support'" type="checkbox">
                    <label for="checkbox-support">
                        {t}Support plan{/t}
                    </label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 column">
                <div class="checkbox check-default">
                    <input id="checkbox-videos" checklist-model="columns.selected" checklist-value="'videos'" type="checkbox">
                    <label for="checkbox-videos">
                        {t}Videos{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-widgets" checklist-model="columns.selected" checklist-value="'widgets'" type="checkbox">
                    <label for="checkbox-widgets">
                        {t}Widgets{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-static-pages" checklist-model="columns.selected" checklist-value="'static_pages'" type="checkbox">
                    <label for="checkbox-static-pages">
                        {t}Static pages{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-attachments" checklist-model="columns.selected" checklist-value="'attachments'" type="checkbox">
                    <label for="checkbox-attachments">
                        {t}Attachments{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-polls" checklist-model="columns.selected" checklist-value="'polls'" type="checkbox">
                    <label for="checkbox-polls">
                        {t}Polls{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-letters" checklist-model="columns.selected" checklist-value="'letters'" type="checkbox">
                    <label for="checkbox-letters">
                        {t}Letters{/t}
                    </label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 column">
                <div class="checkbox check-default">
                    <input id="checkbox-media-size" checklist-model="columns.selected" checklist-value="'media_size'" type="checkbox">
                    <label for="checkbox-media-size">
                        {t}Media size{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-alexa" checklist-model="columns.selected" checklist-value="'alexa'" type="checkbox">
                    <label for="checkbox-alexa">
                        {t}Alexa{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-page-views" checklist-model="columns.selected" checklist-value="'page_views'" type="checkbox">
                    <label for="checkbox-page-views">
                        {t}Page views{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-users" checklist-model="columns.selected" checklist-value="'users'" type="checkbox">
                    <label for="checkbox-users">
                        {t}Users{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-emails" checklist-model="columns.selected" checklist-value="'emails'" type="checkbox">
                    <label for="checkbox-emails">
                        {t}Emails{/t}
                    </label>
                </div>
                <div class="checkbox check-default">
                    <input id="checkbox-activated" checklist-model="columns.selected" checklist-value="'activated'" type="checkbox">
                    <label for="checkbox-activated">
                        {t}Enabled{/t}
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="grid simple">
        <div class="grid-body no-padding">
            <div class="grid-overlay" ng-if="loading"></div>
            <div class="table-wrapper">
                <table class="table table-hover no-margin">
                    <thead ng-if="instances.length >= 0">
                        <tr>
                            <th style="width:15px;">
                                <div class="checkbox checkbox-default">
                                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                    <label for="select-all"></label>
                                </div>
                            </th>
                            <th class="pointer" style="width: 50px;" ng-click="sort('id')">
                                {t}#{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
                            </th>
                            <th class="pointer" ng-click="sort('name')" ng-show="isEnabled('name')">
                                {t}Name{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
                            </th>
                            <th class="pointer" ng-click="sort('domains')" ng-show="isEnabled('domains')">
                                {t}Domains{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('domains') == 'asc', 'fa fa-caret-down': isOrderedBy('domains') == 'desc'}"></i>
                            </th>
                            <th class="pointer" ng-click="sort('domain_expire')" ng-show="isEnabled('domain_expire')">
                                {t}Domain expire{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('domain_expire') == 'asc', 'fa fa-caret-down': isOrderedBy('domain_expire') == 'desc'}"></i>
                            </th>
                            <th class="pointer" ng-click="sort('contact_email')" ng-show="isEnabled('contact_mail')">
                                {t}Contact{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('contact_mail') == 'asc', 'fa fa-caret-down': isOrderedBy('contact_mail') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('last_login')" ng-show="isEnabled('last_login')">
                                {t}Last access{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('last_login') == 'asc', 'fa fa-caret-down': isOrderedBy('last_login') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('created')" ng-show="isEnabled('created')">
                                {t}Created{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('created') == 'asc', 'fa fa-caret-down': isOrderedBy('created') == 'desc'}"></i>
                            </th>
                            <th class="text-center" ng-show="isEnabled('database')">
                                {t}Database{/t}
                            </th>
                            <th class="text-center pointer" ng-click="sort('contents')" ng-show="isEnabled('contents')">
                                <i class="fa fa-folder-open-o" title="{t}Contents{/t}"></i>
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('contents') == 'asc', 'fa fa-caret-down': isOrderedBy('contents') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('articles')" ng-show="isEnabled('articles')">
                                {t}Articles{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('articles') == 'asc', 'fa fa-caret-down': isOrderedBy('articles') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('opinions')" ng-show="isEnabled('opinions')">
                                {t}Opinions{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('opinions') == 'asc', 'fa fa-caret-down': isOrderedBy('opinions') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('advertisements')" ng-show="isEnabled('advertisements')">
                                {t}Advertisements{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('advertisements') == 'asc', 'fa fa-caret-down': isOrderedBy('advertisements') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('albums')" ng-show="isEnabled('albums')">
                                {t}Albums{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('albums') == 'asc', 'fa fa-caret-down': isOrderedBy('albums') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('photos')" ng-show="isEnabled('photos')">
                                {t}Photos{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('photos') == 'asc', 'fa fa-caret-down': isOrderedBy('photos') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('support')" ng-show="isEnabled('support')">
                                {t}Support plan{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('support') == 'asc', 'fa fa-caret-down': isOrderedBy('support') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('videos')" ng-show="isEnabled('videos')">
                                {t}Videos{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('videos') == 'asc', 'fa fa-caret-down': isOrderedBy('videos') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('widgets')" ng-show="isEnabled('widgets')">
                                {t}Widgets{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('widgets') == 'asc', 'fa fa-caret-down': isOrderedBy('widgets') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('static_pages')" ng-show="isEnabled('static_pages')">
                                {t}Static pages{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('static_pages') == 'asc', 'fa fa-caret-down': isOrderedBy('static_pages') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('attachments')" ng-show="isEnabled('attachments')">
                                {t}Attachments{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('attachments') == 'asc', 'fa fa-caret-down': isOrderedBy('attachments') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('polls')" ng-show="isEnabled('polls')">
                                {t}Polls{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('polls') == 'asc', 'fa fa-caret-down': isOrderedBy('polls') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('letters')" ng-show="isEnabled('letters')">
                                {t}Letters{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('letters') == 'asc', 'fa fa-caret-down': isOrderedBy('letters') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('media_size')" ng-show="isEnabled('media_size')">
                                {t}Media size{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('media_size') == 'asc', 'fa fa-caret-down': isOrderedBy('media_size') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('alexa')" ng-show="isEnabled('alexa')">
                                {t}Alexa{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('alexa') == 'asc', 'fa fa-caret-down': isOrderedBy('alexa') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('page_views')" ng-show="isEnabled('page_views')">
                                {t}Page views{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('page_views') == 'asc', 'fa fa-caret-down': isOrderedBy('page_views') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('users')" ng-show="isEnabled('users')">
                                {t}Users{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('users') == 'asc', 'fa fa-caret-down': isOrderedBy('users') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('emails')" ng-show="isEnabled('emails')">
                                {t}Emails{/t}
                                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('emails') == 'asc', 'fa fa-caret-down': isOrderedBy('emails') == 'desc'}"></i>
                            </th>
                            <th class="text-center pointer" ng-click="sort('activated')" ng-show="isEnabled('activated')" style="width: 60px">
                                <span>
                                    <i class="fa fa-check"></i>
                                    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('activated') == 'asc', 'fa fa-caret-down': isOrderedBy('activated') == 'desc'}"></i>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-if="instances.length == 0">
                            <td class="empty" colspan="10">{t}There is no available instances yet{/t}</td>
                        </tr>
                        <tr ng-if="instances.length >= 0" ng-repeat="instance in instances" ng-class="{ row_selected: isSelected(instance.id) }">
                            <td>
                                <div class="checkbox check-default">
                                    <input id="checkbox[%$index%]" checklist-model="selected.instances" checklist-value="instance.id" type="checkbox">
                                    <label for="checkbox[%$index%]"></label>
                                </div>
                            </td>
                            <td>
                                [% instance.id %]
                            </td>
                            <td ng-show="isEnabled('name')">
                                <a ng-href="[% instance.show_url %]" title="{t}Edit{/t}">
                                    [% instance.name %]
                                </a>
                                <div class="listing-inline-actions">
                                    <a class="link" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_show', { id: instance.id }) %]" title="{t}Edit{/t}">
                                        <i class="fa fa-pencil"></i>{t}Edit{/t}
                                    </a>
                                    <button class="link link-danger" ng-click="delete(instance)" type="button">
                                        <i class="fa fa-trash-o"></i>{t}Delete{/t}
                                    </button>
                                </div>
                            </td>
                            <td ng-show="isEnabled('domains')">
                                <div class="domains">
                                    <small>
                                        <ul class="domain-list no-style" ng-if="instance.domains.length > 1">
                                            <li ng-repeat="domain in instance.domains">
                                                <a href="http://[% domain %]" ng-class="{ 'active': $index == instance.main_domain - 1 }" target="_blank">[% domain %]</a>
                                            </li>
                                        </ul>
                                        <span ng-if="instance.domains.length <= 1">
                                            <span ng-repeat="domain in instance.domains">
                                                <a href="http://[% domain %]" target="_blank" title="[% instance.name %]">[% domain %]</a>
                                            </span>
                                        </span>
                                    </small>
                                </div>
                            </td>
                            <td ng-show="isEnabled('domain_expire')">
                                [% instance.domain_expire %]
                            </td>
                            <td ng-show="isEnabled('contact_mail')">
                                <div class="creator">
                                    <a ng-href="mailto:[% instance.contact_mail %]" title="Send an email to the instance manager"> [% instance.contact_mail %]</a>
                                </div>
                            </td>
                            <td class="text-center" ng-show="isEnabled('last_login')">
                                [% instance.last_login %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('created')">
                                [% instance.created %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('database')">
                                [% instance.settings.BD_DATABASE %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('contents')">
                                <span tooltip-html-unsafe="[% '{t}Articles{/t}: ' + instance.articles + '<br>{t}Ads{/t}: ' + instance.advertisements + '<br>{t}Files{/t}: ' + instance.attachments + '<br>{t}Opinions{/t}: ' + instance.opinions + '<br>{t}Albums{/t}: ' + instance.albums + '<br>{t}Images{/t}: ' + instance.photos + '<br>{t}Videos{/t}: ' + instance.videos + '<br>{t}Polls{/t}: ' + instance.polls + '<br>{t}Widgets{/t}: ' + instance.widgets + '<br>{t}Static pages{/t}: ' + instance.static_pages + '<br>{t}Letters{/t}: ' + instance.letters %]">
                                    [% instance.contents %]
                                </span>
                            </td>
                            <td class="text-center" ng-show="isEnabled('articles')">
                                [% instance.articles %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('opinions')">
                                [% instance.opinions %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('advertisements')">
                                [% instance.advertisements %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('albums')">
                                [% instance.albums %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('photos')">
                                [% instance.photos %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('support')">
                                [% instance.support_plan %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('videos')">
                                [% instance.videos %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('widgets')">
                                [% instance.widgets %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('static_pages')">
                                [% instance.static_pages %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('attachments')">
                                [% instance.attachments %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('polls')">
                                [% instance.polls %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('letters')">
                                [% instance.letters %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('media_size')">
                                [% instance.media_size | number : 2 %] Mb
                            </td>
                            <td class="text-center" ng-show="isEnabled('alexa')">
                                [% instance.alexa == 100000000 ? '{t}No rank{/t}' : instance.alexa %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('page_views')">
                                [% instance.page_views %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('users')">
                                [% instance.users %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('emails')">
                                [% instance.emails %]
                            </td>
                            <td class="text-center" ng-show="isEnabled('activated')">
                                <button class="btn btn-white" type="button" ng-click="setEnabled(instance, instance.activated == '1' ? '0' : '1')">
                                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': instance.loading, 'fa-check text-success' : !instance.loading &&instance.activated == '1', 'fa-times text-error': !instance.loading && instance.activated == '0' }"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="grid-footer clearfix">
            <div class="pull-left pagination-info" ng-if="instances.length > 0">
                {t}Showing{/t} [% ((page - 1) * epp > 0) ? (page - 1) * epp : 1 %]-[% (page * epp) < total ? page * epp : total %] {t}of{/t} [% total|number %]
            </div>
            <div class="pull-right" ng-if="instances.length > 0">
                <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="$parent.$parent.epp" ng-model="$parent.$parent.page" total-items="$parent.$parent.total" num-pages="$parent.$parent.pages"></pagination>
            </div>
        </div>
    </div>
</div>
<script type="text/ng-template" id="modal-confirm">
    {include file="common/modal_confirm.tpl"}
</script>
