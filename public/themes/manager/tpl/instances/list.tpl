{extends file="base/base.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=manager_instances}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('instance', { contact_mail_like: '', name_like: '' }, 'id', 'asc', 'manager_ws_instances_list', '{{$smarty.const.CURRENT_LANGUAGE}}'); name = 1; domains = 1; contact_mail = 1; last_access = 1; created = 1; articles = 1">
    <div class="top-action-bar clearfix" >
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Instances{/t}</h2>
            </div>
            <ul class="old-button">
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
                                <i class="icon-trash"></i>
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
        </div>
    </div>
<div class="wrapper-content">
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
                <ul class="dropdown-menu pull-right form-horizontal" role="menu">
                    <li ng-click="name = !name;">
                        <a href="#">
                            <i class="pull-right" ng-class="{ 'icon-ok': name }"></i>
                            {t}Name{/t}
                        </a>
                    </li>
                    <li ng-click="domains = !domains">
                        <a href="#">
                            <i class="pull-right" ng-class="{ 'icon-ok': domains }"></i>
                            {t}Domains{/t}
                        </a>
                    </li>
                    <li ng-click="last_access = !last_access">
                        <a href="#">
                            <i class="pull-right" ng-class="{ 'icon-ok': last_access }"></i>
                            {t}Last access{/t}
                        </a>
                    </li>
                    <li ng-click="created = !created">
                        <a href="#">
                            <i class="pull-right" ng-class="{ 'icon-ok': created }"></i>
                            {t}Created{/t}
                        </a>
                    </li>
                    <li ng-click="articles = !articles">
                        <a href="#">
                            <i class="pull-right" ng-class="{ 'icon-ok': articles }"></i>
                            {t}Artcicles{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div ng-include="'instances'"></div>
    <script type="text/ng-template" id="instances">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>

        <table id="manager" class="table table-hover table-condensed" ng-if="!loading">

            <thead ng-if="shvs.contents.length >= 0">
                <tr>
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th class="pointer" width="25px" ng-click="sort('id')">{t}#{/t}</th>
                    <th class="pointer" width="" ng-click="sort('name')" ng-show="name">{t}Name{/t}</th>
                    <th class="left pointer" ng-click="sort('domains')" ng-show="domains">{t}Domains{/t}</th>
                    <th class="left pointer" ng-click="sort('contact_email')" ng-show="contact_mail">{t}Contact{/t}</th>
                    <th class="left" ng-show="last_access">{t}Last access{/t}</th>
                    <th class="left" ng-show="created">{t}Created{/t}</th>
                    <th class="left" ng-show="articles">{t}Articles{/t}</th>
                    <th class="center" width="70px">{t}Activated{/t}</th>
                    <th class="center" width="10px">{t}Actions{/t}</th>
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
                            <a ng-href="mailto:[% instance.configs.contact_mail %]" title="Send an email to the instance manager"> [% instance.configs.contact_mail %]</a>
                        </div>
                    </td>
                    <td class="center" ng-show="last_access">
                        [% instance.configs.last_login.date  | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                    </td>
                    <td class="nowrap left" ng-show="created">[% instance.configs.site_created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                    </td>
                    <td class="center">
                        [% instance.totals[1] %]
                    </td>
                    <td class="center">
                        <button class="btn-link" ng-class="{ loading: instance.loading == 1, published: instance.activated == '1', unpublished: instance.activated == '0' }" ng-click="updateItem($index, instance.id, 'manager_ws_instance_set_activated', 'activated', instance.activated != 1 ? 1 : 0, 'loading')" type="button"></button>
                    </td>

                    <td class="right nowrap">
                        <div class="btn-group">
                            <a href="#" class="btn info" tooltip-placement="left" tooltip-title="{t}Instance statistics{/t}" tooltip-trigger="mouseenter" tooltip-html-unsafe="[% '{t}Articles{/t}: ' + instance.totals[1] + '<br>{t}Ads{/t}: ' + instance.totals[2] + '<br>{t}Files{/t}: ' + instance.totals[3] + '<br>{t}Opinions{/t}: ' + instance.totals[4] + '<br>{t}Albums{/t}: ' + instance.totals[7] + '<br>{t}Images{/t}: ' + instance.totals[8] + '<br>{t}Videos{/t}: ' + instance.totals[9] + '<br>{t}Polls{/t}: ' + instance.totals[11] + '<br>{t}Widgets{/t}: ' + instance.totals[12] + '<br>{t}Static pages{/t}: ' + instance.totals[13] + '<br>{t}Letters{/t}: ' + instance.totals[17]%]">
                                <i class="icon-info-sign"></i>
                            </a>
                            <a class="btn" href="[% instance.show_url%]" title="{t}Edit{/t}">
                                <i class="icon-pencil"></i>
                            </a>
                            <button class="del btn btn-danger"
                                ng-click="open('modal-delete', 'manager_ws_instance_delete', $index)" type="button">
                                <i class="icon-trash icon-white"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
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
    </div>
    <script type="text/ng-template" id="modal-delete">
        {include file="instances/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="instances/modals/_modalBatchDelete.tpl"}
    </script>
</form>
{/block}
