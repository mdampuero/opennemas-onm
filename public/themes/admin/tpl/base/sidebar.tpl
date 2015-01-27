<div class="sidebar" id="main-menu" ng-mouseleave="sidebar.forced ? sidebar.collapsed = 1 : sidebar.collapsed = !sidebar.pinned" ng-mouseenter="sidebar.collapsed = 0">
    <div class="overlay"></div>
    <scrollable>
        <div class="sidebar-wrapper">
            {admin_menu file='/Backend/Resources/Menu.php' base=$smarty.const.SRC_PATH}
        </div>
    </scrollable>
    <div class="sidebar-footer-widget">
        <ul>
            <li class="profile-info">
                <a href="{url name=admin_acl_user_show id=me}">
                    <div class="profile-pic">
                        {if $smarty.session.avatar_url}
                            <img src="{$smarty.session.avatar_url}" alt="{t}Photo{/t}"/>
                        {else}
                            {gravatar email=$smarty.session.email image_dir="{$params.COMMON_ASSET_DIR}images/" image=true size="32"}
                        {/if}
                    </div>
                    <div class="username">
                        {$smarty.session.realname}
                    </div>
                </a>
                <div class="logout" ng-click="logout();">
                    <a href="javascript:salir('{t}Do you really want to exit from backend?{/t}','{url name="admin_logout"  csrf=$smarty.session.csrf}');">
                        <i class="fa fa-power-off"></i>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
