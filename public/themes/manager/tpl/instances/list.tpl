<div class="content">
    <div class="page-title">
        <h3 class="pull-left">
            <i class="fa fa-cubes"></i> {t}Instances{/t}
        </h3>
        <ul class="breadcrumb pull-right">
            <li>
                <p>{t}YOU ARE HERE{/t}</p>
            </li>
            <li>
                <a href="#">{t}Dashboard{/t}</a>
            </li>
            <li>
                <a href="#/instances" class="active">{t}Instances{/t}</a>
            </li>
        </ul>
    </div>
    <div ng-init="route = 'manager_ws_instances_list';  language = '{{$smarty.const.CURRENT_LANGUAGE}}';">
        <div class="grid simple">
            <div class="grid-title">
                <div class="form-inline">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon primary">
                                <span class="arrow"></span>
                                <i class="fa fa-cube"></i>
                            </span>
                            <input type="text" placeholder="{t}Filter by name, domain or contact{/t}" name="name" ng-model="criteria.name[0].value"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <select class="xsmall" ng-model="epp">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <a class="btn btn-white" href="{url name=manager_ws_instances_list_export}?name=[% shvs.search.name_like %]&email=[% shvs.search.contact_mail_like %]">
                            <i class="fa fa-file-excel-o"></i> Export
                        </a>
                    </div>
                    <div class="pull-right">
                        <div class="form-group" ng-if="selected.instances.length > 0">
                            <div class="btn-group">
                                <button class="btn btn-white dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-edit"></i> {t}Actions{/t} <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                        <span class="a" ng-click="setEnabledSelected(1)">
                                            <i class="fa fa-check"></i> {t}Enable{/t}
                                        </span>
                                    </li>
                                    <li>
                                        <span class="a" ng-click="setEnabledSelected(0)">
                                            <i class="fa fa-times"></i> {t}Disable{/t}
                                        </span>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <span class="a" ng-click="deleteSelected()">
                                            <i class="fa fa-trash-o"></i> {t}Delete{/t}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_create') %]" class="btn btn-primary">
                                <i class="fa fa-plus"></i> {t}Create{/t}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-body no-padding">
                <div class="spinner-wrapper" ng-if="loading">
                    <div class="spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
                </div>
                <table class="table no-margin" ng-if="!loading">
                    <thead ng-if="instances.length >= 0">
                        <tr>
                            <th style="width:15px;">
                                <div class="checkbox checkbox-default">
                                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                    <label for="select-all"></label>
                                </div>
                            </th>
                            <th class="pointer" width="25px" ng-click="sort('id')">
                                {t}#{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.id == 'asc', 'fa fa-caret-down': orderBy.id == 'desc' }"></i>
                            </th>
                            <th class="pointer" width="" ng-click="sort('name')" ng-show="columns.name">
                                {t}Name{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.name == 'asc', 'fa fa-caret-down': orderBy.name == 'desc'}"></i>
                            </th>
                            <th class="left pointer" ng-click="sort('domains')" ng-show="columns.domains">
                                {t}Domains{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.domains == 'asc', 'fa fa-caret-down': orderBy.domains == 'desc'}"></i>
                            </th>
                            <th class="left pointer" ng-click="sort('contact_email')" ng-show="columns.contact_mail">
                                {t}Contact{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.contact_mail == 'asc', 'fa fa-caret-down': orderBy.contact_mail == 'desc'}"></i>
                            </th>
                            <th class="left pointer" ng-click="sort('last_login')" ng-show="columns.last_login">
                                {t}Last access{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.last_login == 'asc', 'fa fa-caret-down': orderBy.last_login == 'desc'}"></i>
                            </th>
                            <th class="left pointer" ng-click="sort('created')" ng-show="columns.created">
                                {t}Created{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.created == 'asc', 'fa fa-caret-down': orderBy.created == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('contents')" ng-show="columns.contents">
                                <i class="fa fa-folder-open-o" title="{t}Contents{/t}"></i>
                                <i ng-class="{ 'fa fa-caret-up': orderBy.contents == 'asc', 'fa fa-caret-down': orderBy.contents == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('articles')" ng-show="columns.articles">
                                {t}Articles{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.articles == 'asc', 'fa fa-caret-down': orderBy.articles == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('opinions')" ng-show="columns.opinions">
                                {t}Opinions{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.opinions == 'asc', 'fa fa-caret-down': orderBy.opinions == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('advertisements')" ng-show="columns.advertisements">
                                {t}Advertisements{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.advertisements == 'asc', 'fa fa-caret-down': orderBy.advertisements == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('albums')" ng-show="columns.albums">
                                {t}Albums{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.albums == 'asc', 'fa fa-caret-down': orderBy.albums == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('photos')" ng-show="columns.photos">
                                {t}Photos{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.photos == 'asc', 'fa fa-caret-down': orderBy.photos == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('videos')" ng-show="columns.videos">
                                {t}Videos{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.videos == 'asc', 'fa fa-caret-down': orderBy.videos == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('widgets')" ng-show="columns.widgets">
                                {t}Widgets{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.widgets == 'asc', 'fa fa-caret-down': orderBy.widgets == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('static_pages')" ng-show="columns.static_pages">
                                {t}Static pages{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.static_pages == 'asc', 'fa fa-caret-down': orderBy.static_pages == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('attachments')" ng-show="columns.attachments">
                                {t}Attachments{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.attachments == 'asc', 'fa fa-caret-down': orderBy.attachments == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('polls')" ng-show="columns.polls">
                                {t}Polls{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.polls == 'asc', 'fa fa-caret-down': orderBy.polls == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('letters')" ng-show="columns.letters">
                                {t}Letters{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.letters == 'asc', 'fa fa-caret-down': orderBy.letters == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('media_size')" ng-show="columns.media_size">
                                {t}Media size{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.media_size == 'asc', 'fa fa-caret-down': orderBy.media_size == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('alexa')" ng-show="columns.alexa">
                                {t}Alexa{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.alexa == 'asc', 'fa fa-caret-down': orderBy.alexa == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('page_views')" ng-show="columns.page_views">
                                {t}Page views{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.page_views == 'asc', 'fa fa-caret-down': orderBy.page_views == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('users')" ng-show="columns.users">
                                {t}Users{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.users == 'asc', 'fa fa-caret-down': orderBy.users == 'desc'}"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('emails')" ng-show="columns.emails">
                                {t}Emails{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderBy.emails == 'asc', 'fa fa-caret-down': orderBy.emails == 'desc'}"></i>
                            </th>
                            <th class="text-center" style="width: 100px">
                                <div class="dropdown">
                                    <div class="dropdown-toggle">
                                        <i class="fa fa-columns" tooltip-html-unsafe="{t}Columns{/t}"></i>
                                    </div>
                                    <div class="dropdown-menu container pull-right" role="menu">
                                        <div class="pull-left">
                                            <ul class="no-style">
                                                <li ng-click="columns.name = !columns.name;">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.name, 'fa-blank': !columns.name }"></i>
                                                        {t}Name{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.domains = !columns.domains">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.domains, 'fa-blank': !columns.domains }"></i>
                                                        {t}Domains{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.contact_mail = !columns.contact_mail">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.contact_mail, 'fa-blank': !columns.contact_mail }"></i>
                                                        {t}Contact{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.last_login = !columns.last_login">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.last_login, 'fa-blank': !columns.last_login }"></i>
                                                        {t}Last access{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.created = !columns.created">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.created, 'fa-blank': !columns.created }"></i>
                                                        {t}Created{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.contents = !columns.contents">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.contents, 'fa-blank': !columns.contents }"></i>
                                                        {t}Contents{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.articles = !columns.articles">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.articles, 'fa-blank': !columns.articles }"></i>
                                                        {t}Articles{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.opinions = !columns.opinions">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.opinions, 'fa-blank': !columns.opinions }"></i>
                                                        {t}Opinions{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.advertisements = !columns.advertisements">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.advertisements, 'fa-blank': !columns.advertisements }"></i>
                                                        {t}Advertisements{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.albums = !columns.albums">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.albums, 'fa-blank': !columns.albums }"></i>
                                                        {t}Albums{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.photos = !columns.photos">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.photos, 'fa-blank': !columns.photos }"></i>
                                                        {t}Photos{/t}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="pull-left">
                                            <ul class="no-style">
                                                <li ng-click="columns.videos = !columns.videos">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.videos, 'fa-blank': !columns.videos }"></i>
                                                        {t}Videos{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.widgets = !columns.widgets">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.widgets, 'fa-blank': !columns.widgets }"></i>
                                                        {t}Widgets{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.static_pages = !columns.static_pages">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.static_pages, 'fa-blank': !columns.static_pages }"></i>
                                                        {t}Static pages{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.attachments = !columns.attachments">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.attachments, 'fa-blank': !columns.attachments }"></i>
                                                        {t}Attachments{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.polls = !columns.polls">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.polls, 'fa-blank': !columns.polls }"></i>
                                                        {t}Polls{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.letters = !columns.letters">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.letters, 'fa-blank': !columns.letters }"></i>
                                                        {t}Letters{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.media_size = !columns.media_size">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.media_size, 'fa-blank': !columns.media_size }"></i>
                                                        {t}Media size{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.alexa = !columns.alexa">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.alexa, 'fa-blank': !columns.alexa }"></i>
                                                        {t}Alexa{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.page_views = !columns.page_views">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.page_views, 'fa-blank': !columns.page_views }"></i>
                                                        {t}Page views{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.users = !columns.users">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.users, 'fa-blank': !columns.users }"></i>
                                                        {t}Users{/t}
                                                    </span>
                                                </li>
                                                <li ng-click="columns.emails = !columns.emails;">
                                                    <span class="a">
                                                        <i class="fa" ng-class="{ 'fa-eye': columns.emails, 'fa-blank': !columns.emails }"></i>
                                                        {t}Emails{/t}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
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
                            <td ng-show="columns.name">
                                <a ng-href="[% instance.show_url %]" title="{t}Edit{/t}">
                                    [% instance.name %]
                                </a>
                            </td>
                            <td class="left" ng-show="columns.domains">
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
                            <td class="left" ng-show="columns.contact_mail">
                                <div class="creator">
                                    <a ng-href="mailto:[% instance.contact_mail %]" title="Send an email to the instance manager"> [% instance.contact_mail %]</a>
                                </div>
                            </td>
                            <td class="center" ng-show="columns.last_login">
                                [% instance.last_login %]
                            </td>
                            <td class="nowrap left" ng-show="columns.created">
                                [% instance.created %]
                            </td>
                            <td class="center" ng-show="columns.contents">
                                <span tooltip-html-unsafe="[% '{t}Articles{/t}: ' + instance.articles + '<br>{t}Ads{/t}: ' + instance.advertisements + '<br>{t}Files{/t}: ' + instance.attachments + '<br>{t}Opinions{/t}: ' + instance.opinions + '<br>{t}Albums{/t}: ' + instance.albums + '<br>{t}Images{/t}: ' + instance.photos + '<br>{t}Videos{/t}: ' + instance.videos + '<br>{t}Polls{/t}: ' + instance.polls + '<br>{t}Widgets{/t}: ' + instance.widgets + '<br>{t}Static pages{/t}: ' + instance.static_pages + '<br>{t}Letters{/t}: ' + instance.letters %]">
                                    [% instance.contents %]
                                </span>
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['contents'] > 0, 'fa fa-angle-down text-danger': instance.deltas['contents'] < 0 }" tooltip-html-unsafe="[% instance.deltas['contents'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.articles">
                                [% instance.articles %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['articles'] > 0, 'fa fa-angle-down text-danger': instance.deltas['articles'] < 0 }" tooltip-html-unsafe="[% instance.deltas['articles'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.opinions">
                                [% instance.opinions %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['opinions'] > 0, 'fa fa-angle-down text-danger': instance.deltas['opinions'] < 0 }" tooltip-html-unsafe="[% instance.deltas['opinions'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.advertisements">
                                [% instance.advertisements %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['advertisements'] > 0, 'fa fa-angle-down text-danger': instance.deltas['advertisements'] < 0 }" tooltip-html-unsafe="[% instance.deltas['advertisements'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.albums">
                                [% instance.albums %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['albums'] > 0, 'fa fa-angle-down text-danger': instance.deltas['albums'] < 0 }" tooltip-html-unsafe="[% instance.deltas['albums'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.photos">
                                [% instance.photos %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['photos'] > 0, 'fa fa-angle-down text-danger': instance.deltas['photos'] < 0 }" tooltip-html-unsafe="[% instance.deltas['photos'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.videos">
                                [% instance.videos %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['videos'] > 0, 'fa fa-angle-down text-danger': instance.deltas['videos'] < 0 }" tooltip-html-unsafe="[% instance.deltas['videos'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.widgets">
                                [% instance.widgets %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['widgets'] > 0, 'fa fa-angle-down text-danger': instance.deltas['widgets'] < 0 }" tooltip-html-unsafe="[% instance.deltas['widgets'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.static_pages">
                                [% instance.static_pages %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['static_pages'] > 0, 'fa fa-angle-down text-danger': instance.deltas['static_pages'] < 0 }" tooltip-html-unsafe="[% instance.deltas['static_pages'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.attachments">
                                [% instance.attachments %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['attachments'] > 0, 'fa fa-angle-down text-danger': instance.deltas['attachments'] < 0 }" tooltip-html-unsafe="[% instance.deltas['attachments'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.polls">
                                [% instance.polls %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['polls'] > 0, 'fa fa-angle-down text-danger': instance.deltas['polls'] < 0 }" tooltip-html-unsafe="[% instance.deltas['polls'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.letters">
                                [% instance.letters %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['letters'] > 0, 'fa fa-angle-down text-danger': instance.deltas['letters'] < 0 }" tooltip-html-unsafe="[% instance.deltas['letters'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.media_size">
                                [% instance.media_size | number : 2 %] Mb
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['media_size'] > 0, 'fa fa-angle-down text-danger': instance.deltas['media_size'] < 0 }" tooltip-html-unsafe="[% instance.deltas['media_size'] | number : 2 %] Mb"></i>
                            </td>
                            <td class="center" ng-show="columns.alexa">
                                [% instance.alexa %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['alexa'] > 0, 'fa fa-angle-down text-danger': instance.deltas['alexa'] < 0 }" tooltip-html-unsafe="[% instance.deltas['alexa'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.page_views">
                                [% instance.page_views %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['page_views'] > 0, 'fa fa-angle-down text-danger': instance.deltas['page_views'] < 0 }" tooltip-html-unsafe="[% instance.deltas['page_views'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.users">
                                [% instance.users %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['users'] > 0, 'fa fa-angle-down text-danger': instance.deltas['users'] < 0 }" tooltip-html-unsafe="[% instance.deltas['users'] %]"></i>
                            </td>
                            <td class="center" ng-show="columns.emails">
                                [% instance.emails %]
                                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['emails'] > 0, 'fa fa-angle-down text-danger': instance.deltas['emails'] < 0 }" tooltip-html-unsafe="[% instance.deltas['emails'] %]"></i>
                            </td>
                            <td class="right nowrap">
                                <div class="btn-group btn-group-xs">
                                    <button class="btn btn-primary btn-sm" type="button" ng-click="setActivated(instance, instance.activated == '1' ? '0' : '1')">
                                        <i class="fa" ng-class="{ 'fa-refresh fa-spin': instance.loading == 1, 'fa-check' : instance.activated == '1', 'fa-times': instance.activated == '0' }"></i>
                                    </button>
                                    <a class="btn btn-white" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_show', { id: instance.id }) %]" title="{t}Edit{/t}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <button class="btn btn-danger" ng-click="delete(instance)" type="button">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="[% 4 + columns.name + columns.domains + columns.contact_mail + columns.last_login + columns.created + columns.contents + columns.articles + columns.opinions + columns.advertisements + columns.albums + columns.photos + columns.videos + columns.widgets + columns.static_pages + columns.attachments + columns.polls + columns.letters + columns.media_size + columns.alexa + columns.page_views + columns.users + columns.emails %]" class="center">
                                <div class="pagination-info pull-left" ng-if="instances.length > 0">
                                    {t}Showing{/t} [% ((page - 1) * epp > 0) ? (page - 1) * epp : 1 %]-[% (page * epp) < total ? page * epp : total %] {t}of{/t} [% total|number %]
                                </div>
                                <div class="pull-right" ng-if="instances.length > 0">
                                    <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="$parent.$parent.epp" ng-model="$parent.$parent.page" total-items="$parent.$parent.total" num-pages="pages"></pagination>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
