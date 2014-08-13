{extends file="base/base.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<div class="clearfix"></div>

<form action="{url name=manager_instances}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('instance', { contact_mail_like: '', name_like: '' }, 'last_login', 'asc', 'manager_ws_instances_list', '{{$smarty.const.CURRENT_LANGUAGE}}', 25); name = 1; domains = 1; contact_mail = 1; last_login = 1; created = 1; contents = 1; alexa = 1; page_views = 1">
<div class="content">
    <div class="page-title"> <i class="fa-custom-left"></i>
        <h3>Instances</h3>
    </div>
    <ul class="top-buttons old-button pull-right">
        <li ng-if="shvs.selected.length > 0">
            <a href="#">
                <img src="{$params.COMMON_ASSET_DIR}images/select.png" title="{t}Batch actions{/t}" alt="{t}Batch actions{/t}"/>
                <br/>{t}Batch actions{/t}
            </a>
            <ul class="dropdown-menu" style="margin-top: 0;">
                <li>
                    <a href="#" id="batch-activate" ng-click="updateSelectedItems('manager_ws_instances_set_activated', 'activated', 1, 'loading')">
                        {t}Batch activate{/t}
                    </a>
                </li>
                <li>
                    <a href="#" id="batch-desactivate" ng-click="updateSelectedItems('manager_ws_instances_set_activated', 'activated', 0, 'loading')">
                        {t}Batch desactivate{/t}
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'manager_ws_instances_delete')">
                        <i class="fa-trash"></i>
                        {t}Delete{/t}
                    </a>
                </li>
            </ul>
        </li>
        <li class="separator" ng-if="shvs.selected.length > 0"></li>
        <li>
            <a href="{url name=manager_instance_create category=$category}">
                <img border="0" src="{$params.COMMON_ASSET_DIR}images/list-add.png" alt="{t}New Instance{/t}">
                <br />{t}Create{/t}
            </a>
        </li>
    </ul>

    {render_messages}

    <div class="table-info clearfix">
        <div class="pull-left">
            <div class="form-inline">
                <strong>{t}FILTER:{/t}</strong>
                &nbsp;&nbsp;
                <input type="text" placeholder="{t}Filter by instance name{/t}" name="name" ng-model="shvs.search.name_like"/>
                &nbsp;&nbsp;
                <input type="text" autofocus placeholder="{t}Filter by e-mail{/t}" name="contact_mail" ng-model="shvs.search.contact_mail_like"/>
                &nbsp;&nbsp;
                <select ng-model="shvs.elements_per_page" class="input-small">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                </select>
            </div>
        </div>
        <div class="pull-left">
            <a href="{url name=manager_ws_instances_list_export}?name=[% shvs.search.name_like %]&email=[% shvs.search.contact_mail_like %]">{image_tag src="{$params.COMMON_ASSET_DIR}images/csv.png" base_url=""} Export list</a>
        </div>
        <div class="pull-right">
            <div class="dropdown">
                <div class="btn dropdown-toggle">
                    <span class="caret"></span>
                </div>
                <div class="dropdown-menu pull-right container" role="menu">
                    <div class="row">
                        <div class="span2">
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
                        <div class="span2">
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

    <div ng-include="'instances'"></div>

</div>

<script type="text/ng-template" id="instances">
<div class="spinner-wrapper" ng-if="loading">
    <div class="spinner"></div>
    <div class="spinner-text">{t}Loading{/t}...</div>
</div>

<table id="manager" class="table table-hover table-condensed no-more-tables" ng-if="!loading">

    <thead ng-if="shvs.contents.length >= 0">
        <tr>
            <th style="width:15px;">
                <checkbox select-all="true"></checkbox>
            </th>
            <th class="pointer" width="25px" ng-click="sort('id')">
                {t}#{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'id', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'id' }"></i>
            </th>
            <th class="pointer" width="" ng-click="sort('name')" ng-show="name">
                {t}Name{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'name', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'name' }"></i>
            </th>
            <th class="left pointer" ng-click="sort('domains')" ng-show="domains">
                {t}Domains{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'domains', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'domains' }"></i>
            </th>
            <th class="left pointer" ng-click="sort('contact_email')" ng-show="contact_mail">
                {t}Contact{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'contact_mail', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'contact_mail' }"></i>
            </th>
            <th class="left pointer" ng-click="sort('last_login')" ng-show="last_login">
                {t}Last access{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'last_login', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'last_login' }"></i>
            </th>
            <th class="left pointer" ng-click="sort('created')" ng-show="created">
                {t}Created{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'created', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'created' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('contents')" ng-show="contents">
                {t}Contents{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'contents', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'contents' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('articles')" ng-show="articles">
                {t}Articles{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'articles', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'articles' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('opinions')" ng-show="opinions">
                {t}Opinions{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'opinions', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'opinions' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('advertisements')" ng-show="advertisements">
                {t}Advertisements{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'advertisements', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'advertisements' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('albums')" ng-show="albums">
                {t}Albums{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'albums', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'albums' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('photos')" ng-show="photos">
                {t}Photos{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'photos', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'photos' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('videos')" ng-show="videos">
                {t}Videos{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'videos', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'videos' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('widgets')" ng-show="widgets">
                {t}Widgets{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'widgets', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'widgets' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('static_pages')" ng-show="static_pages">
                {t}Static pages{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'static_pages', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'static_pages' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('attachments')" ng-show="attachments">
                {t}Attachments{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'attachments', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'attachments' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('polls')" ng-show="polls">
                {t}Polls{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'polls', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'polls' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('letters')" ng-show="letters">
                {t}Letters{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'letters', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'letters' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('media_size')" ng-show="media_size">
                {t}Media size{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'media_size', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'media_size' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('alexa')" ng-show="alexa">
                {t}Alexa{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'alexa', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'alexa' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('page_views')" ng-show="page_views">
                {t}Page views{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'page_views', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'page_views' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('users')" ng-show="users">
                {t}Users{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'users', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'users' }"></i>
            </th>
            <th class="center pointer" ng-click="sort('emails')" ng-show="emails">
                {t}Emails{/t}
                <i ng-class="{ 'fa-caret-up': shvs.sort_order == 'asc' && shvs.sort_by == 'emails', 'fa-caret-down': shvs.sort_order == 'desc' && shvs.sort_by == 'emails' }"></i>
            </th>
            <th class="center" width="70px">{t}Activated{/t}
            </th>
            <th class="center" width="10px">{t}Actions{/t}
            </th>
        </tr>
    </thead>

    <tbody>
        <tr ng-if="shvs.contents.length == 0">
            <td class="empty" colspan="10">{t}There is no available instances yet{/t}</td>
        </tr>
        <tr ng-if="shvs.contents.length >= 0" ng-repeat="instance in shvs.contents" ng-class="{ row_selected: isSelected(instance.id) }">
            <td>
                <checkbox index="[% instance.id %]">
            </td>
            <td>
                [% instance.id %]
            </td>
            <td ng-show="name">
                <a ng-href="[% instance.show_url %]" title="{t}Edit{/t}">
                    [% instance.name %]
                </a>
            </td>
            <td class="left" ng-show="domains">
                <div class="domains">
                    <small>
                        <ul ng-if="instance.domains.length > 1">
                            <li ng-repeat="domain in instance.domains">
                                <a href="http://[% domain %]" target="_blank" title="[% instance.name %]">[% domain %]</a>
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
            <td class="left" ng-show="contact_mail"
                <div class="creator">
                    <a ng-href="mailto:[% instance.contact_mail %]" title="Send an email to the instance manager"> [% instance.contact_mail %]</a>
                </div>
            </td>
            <td class="center" ng-show="last_login">
                [% instance.last_login  | moment : 'YYYY-MM-DD HH:mm' : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
            </td>
            <td class="nowrap left" ng-show="created">[% instance.created | moment : 'YYYY-MM-DD' : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
            </td>
            <td class="center" ng-show="contents">
                <span tooltip-html-unsafe="[% '{t}Articles{/t}: ' + instance.articles + '<br>{t}Ads{/t}: ' + instance.advertisements + '<br>{t}Files{/t}: ' + instance.attachments + '<br>{t}Opinions{/t}: ' + instance.opinions + '<br>{t}Albums{/t}: ' + instance.albums + '<br>{t}Images{/t}: ' + instance.photos + '<br>{t}Videos{/t}: ' + instance.videos + '<br>{t}Polls{/t}: ' + instance.polls + '<br>{t}Widgets{/t}: ' + instance.widgets + '<br>{t}Static pages{/t}: ' + instance.static_pages + '<br>{t}Letters{/t}: ' + instance.letters %]">
                    [% instance.contents %]
                </span>
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['contents'] > 0, 'fa fa-angle-down text-danger': instance.deltas['contents'] < 0 }" tooltip-html-unsafe="[% instance.deltas['contents'] %]"></i>
            </td>
            <td class="center" ng-show="articles">
                [% instance.articles %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['articles'] > 0, 'fa fa-angle-down text-danger': instance.deltas['articles'] < 0 }" tooltip-html-unsafe="[% instance.deltas['articles'] %]"></i>
            </td>
            <td class="center" ng-show="opinions">
                [% instance.opinions %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['opinions'] > 0, 'fa fa-angle-down text-danger': instance.deltas['opinions'] < 0 }" tooltip-html-unsafe="[% instance.deltas['opinions'] %]"></i>
            </td>
            <td class="center" ng-show="advertisements">
                [% instance.advertisements %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['advertisements'] > 0, 'fa fa-angle-down text-danger': instance.deltas['advertisements'] < 0 }" tooltip-html-unsafe="[% instance.deltas['advertisements'] %]"></i>
            </td>
            <td class="center" ng-show="albums">
                [% instance.albums %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['albums'] > 0, 'fa fa-angle-down text-danger': instance.deltas['albums'] < 0 }" tooltip-html-unsafe="[% instance.deltas['albums'] %]"></i>
            </td>
            <td class="center" ng-show="photos">
                [% instance.photos %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['photos'] > 0, 'fa fa-angle-down text-danger': instance.deltas['photos'] < 0 }" tooltip-html-unsafe="[% instance.deltas['photos'] %]"></i>
            </td>
            <td class="center" ng-show="videos">
                [% instance.videos %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['videos'] > 0, 'fa fa-angle-down text-danger': instance.deltas['videos'] < 0 }" tooltip-html-unsafe="[% instance.deltas['videos'] %]"></i>
            </td>
            <td class="center" ng-show="widgets">
                [% instance.widgets %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['widgets'] > 0, 'fa fa-angle-down text-danger': instance.deltas['widgets'] < 0 }" tooltip-html-unsafe="[% instance.deltas['widgets'] %]"></i>
            </td>
            <td class="center" ng-show="static_pages">
                [% instance.static_pages %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['static_pages'] > 0, 'fa fa-angle-down text-danger': instance.deltas['static_pages'] < 0 }" tooltip-html-unsafe="[% instance.deltas['static_pages'] %]"></i>
            </td>
            <td class="center" ng-show="attachments">
                [% instance.attachments %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['attachments'] > 0, 'fa fa-angle-down text-danger': instance.deltas['attachments'] < 0 }" tooltip-html-unsafe="[% instance.deltas['attachments'] %]"></i>
            </td>
            <td class="center" ng-show="polls">
                [% instance.polls %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['polls'] > 0, 'fa fa-angle-down text-danger': instance.deltas['polls'] < 0 }" tooltip-html-unsafe="[% instance.deltas['polls'] %]"></i>
            </td>
            <td class="center" ng-show="letters">
                [% instance.letters %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['letters'] > 0, 'fa fa-angle-down text-danger': instance.deltas['letters'] < 0 }" tooltip-html-unsafe="[% instance.deltas['letters'] %]"></i>
            </td>
            <td class="center" ng-show="media_size">
                [% instance.media_size | number : 2 %] Mb
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['media_size'] > 0, 'fa fa-angle-down text-danger': instance.deltas['media_size'] < 0 }" tooltip-html-unsafe="[% instance.deltas['media_size'] | number : 2 %] Mb"></i>
            </td>
            <td class="center" ng-show="alexa">
                [% instance.alexa %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['alexa'] > 0, 'fa fa-angle-down text-danger': instance.deltas['alexa'] < 0 }" tooltip-html-unsafe="[% instance.deltas['alexa'] %]"></i>
            </td>
            <td class="center" ng-show="page_views">
                [% instance.page_views %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['page_views'] > 0, 'fa fa-angle-down text-danger': instance.deltas['page_views'] < 0 }" tooltip-html-unsafe="[% instance.deltas['page_views'] %]"></i>
            </td>
            <td class="center" ng-show="users">
                [% instance.users %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['users'] > 0, 'fa fa-angle-down text-danger': instance.deltas['users'] < 0 }" tooltip-html-unsafe="[% instance.deltas['users'] %]"></i>
            </td>
            <td class="center" ng-show="emails">
                [% instance.emails %]
                <i ng-class="{ 'fa fa-angle-up text-success': instance.deltas['emails'] > 0, 'fa fa-angle-down text-danger': instance.deltas['emails'] < 0 }" tooltip-html-unsafe="[% instance.deltas['emails'] %]"></i>
            </td>
            <td class="center">
                <button class="btn-link" ng-class="{ loading: instance.loading == 1, published: instance.activated == '1', unpublished: instance.activated == '0' }" ng-click="updateItem($index, instance.id, 'manager_ws_instance_set_activated', 'activated', instance.activated != 1 ? 1 : 0, 'loading')" type="button"></button>
            </td>
            <td class="right nowrap">
                <div class="btn-group">
                    <a class="btn" href="[% instance.show_url%]" title="{t}Edit{/t}">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <button class="del btn btn-danger"
                        ng-click="open('modal-delete', 'manager_ws_instance_delete', $index)" type="button">
                        <i class="fa fa-trash-o fa-white"></i>
                    </button>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="[% 4 + name + domains + contact_mail + last_login + created + contents + articles + opinions + advertisements + albums + photos + videos + widgets + static_pages + attachments + polls + letters + media_size + alexa + page_views + users + emails %]" class="center">
                <div class="pull-left" ng-if="shvs.contents.length > 0">
                    {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total|number %]
                </div>
                <div class="pull-right" ng-if="shvs.contents.length > 0">
                    <pagination max-size="0" direction-links="true"  on-select-page="selectPage(page, 'manager_ws_instances_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                </div>
                <span ng-if="shvs.contents.length == 0">&nbsp;</span>
            </td>
        </tr>
    </tfoot>
</table>
</script>
<script type="text/ng-template" id="modal-delete">
    {include file="instances/modals/_modalDelete.tpl"}
</script>
<script type="text/ng-template" id="modal-delete-selected">
    {include file="instances/modals/_modalBatchDelete.tpl"}
</script>
{/block}
