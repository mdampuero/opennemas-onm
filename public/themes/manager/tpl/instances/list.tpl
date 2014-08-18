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

        {render_messages}

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
                        <div class="form-group">
                            <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_create') %]" class="btn btn-primary">
                                <i class="fa fa-plus"></i> {t}Create{/t}
                            </a>
                            <div class="form-group dropdown">
                                <div class="btn btn-white dropdown-toggle">
                                    <i class="fa fa-angle-down fa-lg"></i>
                                </div>
                                <div class="dropdown-menu pull-right container" role="menu">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div ng-click="name = !name;">
                                                <i class="pull-right" ng-class="{ 'fa-ok': name }"></i>
                                                {t}Name{/t}
                                            </div>
                                            <div ng-click="domains = !domains">
                                                <i class="pull-right" ng-class="{ 'fa-ok': domains }"></i>
                                                {t}Domains{/t}
                                            </div>
                                            <div ng-click="contact_mail = !contact_mail">
                                                <i class="pull-right" ng-class="{ 'fa-ok': contact_mail }"></i>
                                                {t}Contact{/t}
                                            </div>
                                            <div ng-click="last_login = !last_login">
                                                <i class="pull-right" ng-class="{ 'fa-ok': last_login }"></i>
                                                {t}Last access{/t}
                                            </div>
                                            <div ng-click="created = !created">
                                                <i class="pull-right" ng-class="{ 'fa-ok': created }"></i>
                                                {t}Created{/t}
                                            </div>
                                            <div ng-click="contents = !contents">
                                                <i class="pull-right" ng-class="{ 'fa-ok': contents }"></i>
                                                {t}Contents{/t}
                                            </div>
                                            <div ng-click="articles = !articles">
                                                <i class="pull-right" ng-class="{ 'fa-ok': articles }"></i>
                                                {t}Articles{/t}
                                            </div>
                                            <div ng-click="opinions = !opinions">
                                                <i class="pull-right" ng-class="{ 'fa-ok': opinions }"></i>
                                                {t}Opinions{/t}
                                            </div>
                                            <div ng-click="advertisements = !advertisements">
                                                <i class="pull-right" ng-class="{ 'fa-ok': advertisements }"></i>
                                                {t}Advertisements{/t}
                                            </div>
                                            <div ng-click="albums = !albums">
                                                <i class="pull-right" ng-class="{ 'fa-ok': albums }"></i>
                                                {t}Albums{/t}
                                            </div>
                                            <div ng-click="photos = !photos">
                                                <i class="pull-right" ng-class="{ 'fa-ok': photos }"></i>
                                                {t}Photos{/t}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div ng-click="videos = !videos">
                                                <i class="pull-right" ng-class="{ 'fa-ok': videos }"></i>
                                                {t}Videos{/t}
                                            </div>
                                            <div ng-click="widgets = !widgets">
                                                <i class="pull-right" ng-class="{ 'fa-ok': widgets }"></i>
                                                {t}Widgets{/t}
                                            </div>
                                            <div ng-click="static_pages = !static_pages">
                                                <i class="pull-right" ng-class="{ 'fa-ok': static_pages }"></i>
                                                {t}Static pages{/t}
                                            </div>
                                            <div ng-click="attachments = !attachments">
                                                <i class="pull-right" ng-class="{ 'fa-ok': attachments }"></i>
                                                {t}Attachments{/t}
                                            </div>
                                            <div ng-click="polls = !polls">
                                                <i class="pull-right" ng-class="{ 'fa-ok': polls }"></i>
                                                {t}Polls{/t}
                                            </div>
                                            <div ng-click="letters = !letters">
                                                <i class="pull-right" ng-class="{ 'fa-ok': letters }"></i>
                                                {t}Letters{/t}
                                            </div>
                                            <div ng-click="media_size = !media_size">
                                                <i class="pull-right" ng-class="{ 'fa-ok': media_size }"></i>
                                                {t}Media size{/t}
                                            </div>
                                            <div ng-click="alexa = !alexa">
                                                <i class="pull-right" ng-class="{ 'fa-ok': alexa }"></i>
                                                {t}Alexa{/t}
                                            </div>
                                            <div ng-click="page_views = !page_views">
                                                <i class="pull-right" ng-class="{ 'fa-ok': page_views }"></i>
                                                {t}Page views{/t}
                                            </div>
                                            <div ng-click="users = !users">
                                                <i class="pull-right" ng-class="{ 'fa-ok': users }"></i>
                                                {t}Users{/t}
                                            </div>
                                            <div ng-click="$('th').length();">
                                                <i class="pull-right" ng-class="{ 'fa-ok': emails }"></i>
                                                {t}Emails{/t}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-body no-padding">
                <div class="spinner-wrapper" ng-if="loading">
                    <div class="spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
                </div>
                <table class="table no-more-tables no-margin no-padding" ng-if="!loading">
                    <thead ng-if="instances.length >= 0">
                        <tr>
                            <th style="width:15px;">
                                <checkbox select-all="true"></checkbox>
                            </th>
                            <th class="pointer" width="25px" ng-click="sort('id')">
                                {t}#{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'id', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'id' }"></i>
                            </th>
                            <th class="pointer" width="" ng-click="sort('name')" ng-show="columns.name">
                                {t}Name{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'name', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'name' }"></i>
                            </th>
                            <th class="left pointer" ng-click="sort('domains')" ng-show="columns.domains">
                                {t}Domains{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'domains', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'domains' }"></i>
                            </th>
                            <th class="left pointer" ng-click="sort('contact_email')" ng-show="columns.contact_mail">
                                {t}Contact{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'contact_mail', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'contact_mail' }"></i>
                            </th>
                            <th class="left pointer" ng-click="sort('last_login')" ng-show="columns.last_login">
                                {t}Last access{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'last_login', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'last_login' }"></i>
                            </th>
                            <th class="left pointer" ng-click="sort('created')" ng-show="columns.created">
                                {t}Created{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'created', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'created' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('contents')" ng-show="columns.contents">
                                <i class="fa fa-folder-open-o" title="{t}Contents{/t}"></i>
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'contents', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'contents' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('articles')" ng-show="columns.articles">
                                {t}Articles{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'articles', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'articles' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('opinions')" ng-show="columns.opinions">
                                {t}Opinions{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'opinions', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'opinions' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('advertisements')" ng-show="columns.advertisements">
                                {t}Advertisements{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'advertisements', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'advertisements' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('albums')" ng-show="columns.albums">
                                {t}Albums{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'albums', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'albums' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('photos')" ng-show="columns.photos">
                                {t}Photos{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'photos', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'photos' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('videos')" ng-show="columns.videos">
                                {t}Videos{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'videos', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'videos' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('widgets')" ng-show="columns.widgets">
                                {t}Widgets{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'widgets', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'widgets' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('static_pages')" ng-show="columns.static_pages">
                                {t}Static pages{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'static_pages', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'static_pages' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('attachments')" ng-show="columns.attachments">
                                {t}Attachments{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'attachments', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'attachments' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('polls')" ng-show="columns.polls">
                                {t}Polls{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'polls', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'polls' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('letters')" ng-show="columns.letters">
                                {t}Letters{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'letters', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'letters' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('media_size')" ng-show="columns.media_size">
                                {t}Media size{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'media_size', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'media_size' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('alexa')" ng-show="columns.alexa">
                                {t}Alexa{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'alexa', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'alexa' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('page_views')" ng-show="columns.page_views">
                                {t}Page views{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'page_views', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'page_views' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('users')" ng-show="columns.users">
                                {t}Users{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'users', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'users' }"></i>
                            </th>
                            <th class="center pointer" ng-click="sort('emails')" ng-show="columns.emails">
                                {t}Emails{/t}
                                <i ng-class="{ 'fa fa-caret-up': orderDirection == 'asc' && orderBy == 'emails', 'fa fa-caret-down': orderDirection == 'desc' && orderBy == 'emails' }"></i>
                            </th>
                            <th class="center" style="width: 130px;">
                                {t}Actions{/t}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-if="instances.length == 0">
                            <td class="empty" colspan="10">{t}There is no available instances yet{/t}</td>
                        </tr>
                        <tr ng-if="instances.length >= 0" ng-repeat="instance in instances" ng-class="{ row_selected: isSelected(instance.id) }">
                            <td>
                                <checkbox index="[% instance.id %]">
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
                                    <button class="btn btn-primary btn-sm" type="button">
                                        <i class="fa" ng-class="{ 'fa-refresh fa-spin': instance.loading == 1, 'fa-check' : instance.activated == '1', 'fa-times': instance.activated == '0' }"></i>
                                    </button>
                                    <a class="btn btn-white" ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instance_show', { id: instance.id }) %]" title="{t}Edit{/t}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <button class="btn btn-danger"
                                        ng-click="open('modal-delete', 'manager_ws_instance_delete', $index)" type="button">
                                        <i class="fa fa-trash-o fa-white"></i>
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
                                    <pagination class="no-margin" max-size="5" direction-links="true" ng-model="page" total-instances="total" num-pages="pages"></pagination>
                                </div>
                                <span ng-if="instances.length == 0">&nbsp;</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/ng-template" id="modal-delete">
    {include file="instances/modals/_modalDelete.tpl"}
</script>
<script type="text/ng-template" id="modal-delete-selected">
    {include file="instances/modals/_modalBatchDelete.tpl"}
</script>
