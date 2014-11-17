<div class="page-sidebar" id="main-menu" ng-mouseleave="sidebar.forced ? sidebar.current = 1 || sidebar.current = sidebar.wanted" ng-mouseenter="sidebar.current = 0" ng-show="loaded && auth.status" ng-swipe-right="sidebar.current = 0" ng-swipe-left="sidebar.current = 1">
    <div class="overlay" ng-click="sidebar.current = 0"></div>
    <scrollable>
        <div class="page-sidebar-wrapper">
            <ul>
                <li class="start" ng-class="{ 'active': false }" ng-click="goTo('manager_welcome', 'dashboard')">
                    <a href="#">
                        <i class="fa fa-home" ng-class="{ 'fa-circle-o-notch fa-spin': changing.dashboard }"></i>
                        <span class="title">{t}Dashboard{/t}</span>
                    </a>
                </li>
                <li ng-class="{ 'active': isActive('manager_instances_list') }" ng-click="goTo('manager_instances_list', 'instances')">
                    <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instances_list') %]">
                        <i class="fa fa-cubes" ng-class="{ 'fa-circle-o-notch fa-spin': changing.instances }"></i>
                        <span class="title">{t}Instances{/t}</span>
                    </a>
                </li>
                <li ng-class="{ 'active open': isActive('manager_framework_commands') || isActive('manager_framework_opcache_status') }">
                    <a href="#">
                        <i class="fa fa-flask"></i>
                        <span class="title"> {t}Framework{/t}</span>
                        <span class="arrow" ng-class="{ 'open': isActive('manager_framework_commands') || isActive('manager_framework_opcache_status') }"></span>
                    </a>
                    <ul class="sub-menu">
                        <li ng-class="{ 'active': isActive('manager_framework_commands') }" ng-click="goTo('manager_framework_commands', 'commands')">
                            <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_framework_commands') %]">
                                <i class="fa fa-code" ng-class="{ 'fa-circle-o-notch fa-spin': changing.commands }"></i>
                                <span class="title">{t}Commands{/t}</span>
                            </a>
                        </li>
                        <li ng-class="{ 'active': isActive('manager_framework_opcache_status') }" ng-click="goTo('manager_framework_opcache_status', 'cache')">
                            <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_framework_opcache_status') %]">
                                <i class="fa fa-database" ng-class="{ 'fa-circle-o-notch fa-spin': changing.cache }"></i>
                                <span class="title">{t}OPCache Status{/t}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li ng-class="{ 'active open': isActive('manager_users_list') || isActive('manager_user_groups_list'),  'active': isActive('manager_users_list') || isActive('manager_user_groups_list') }">
                    <a href="#">
                        <i class="fa fa-gears"></i>
                        <span class="title">{t}Settings{/t}</span>
                        <span class="arrow" ng-class="{ 'open': isActive('manager_users_list') || isActive('manager_user_groups_list') }"></span>
                    </a>
                    <ul class="sub-menu">
                        <li ng-class="{ 'active': isActive('manager_users_list') }" ng-click="goTo('manager_users_list', 'users')">
                            <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_users_list') %]">
                                <i class="fa fa-user" ng-class="{ 'fa-circle-o-notch fa-spin': changing.users }"></i>
                                <span class="title">{t}Users{/t}</span>
                            </a>
                        </li>
                        <li ng-class="{ 'active': isActive('manager_user_groups_list') }" ng-click="goTo('manager_user_groups_list', 'groups')">
                            <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_groups_list') %]">
                                <i class="fa fa-users" ng-class="{ 'fa-circle-o-notch fa-spin': changing.groups }"></i>
                                <span class="title">{t}User groups{/t}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </scrollable>
    <div class="footer-widget">
        <ul>
            <li class="profile-info">
                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_show', { id: 'me' }) %]">
                    <div class="profile-pic">
                        <img class="gravatar" email="[% user.email %]" image="1" size="32" width=32 height=32 >
                    </div>
                    <div class="username">
                        [% user.name %]
                    </div>
                </a>
                <div class="logout" ng-click="logout();">
                    <i class="fa fa-power-off"></i>
                </div>
            </li>
        </ul>
    </div>
</div>
