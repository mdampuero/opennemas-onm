<div class="page-sidebar" id="main-menu">
    <div class="overlay"></div>
    <scrollable>
        <div class="page-sidebar-wrapper">
            {admin_menu file='/Backend/Resources/Menu.php' base=$smarty.const.SRC_PATH}
        </div>
    </scrollable>
    <div class="footer-widget">
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
        <div class="nav-collapse collapse navbar-inverse-collapse">
            <ul class="nav pull-right">
                <li>
                    <form action="{url name=admin_search}" class="navbar-search global-search nofillonhover pull-right">
                        <input type="search" name="search_string" placeholder="{t}Search...{/t}" class="string-search" accesskey="s">
                    </form>
                </li>
                {if is_null($errorMessage)}
                {if {count_pending_comments} gt 0}
                <li class="notification-messages">
                    <a class="" title="{count_pending_comments} {t}Pending comments{/t}"
                        href="{url name=admin_comments}">
                        <span class="icon icon-inbox icon-large"></span>
                        <span class="icon count">{count_pending_comments} <span class="longtext">{t}Pending comments{/t}</span></span>
                    </a>
                </li>
                {/if}
                {/if}
            </ul>
        </div>
    </div>
</div>
