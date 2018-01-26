<div class="sidebar" id="main-menu" ng-swipe-right="sidebar.swipeOpen()" ng-swipe-left="sidebar.swipeClose()" ng-mouseleave="sidebar.mouseLeave()">
  <div class="overlay" ng-click="sidebar.open()"></div>
  <div class="sidebar-wrapper">
  <scrollable>
    <div class="user-info-wrapper clearfix">
      <div class="profile-wrapper">
          <img src="/assets/images/launcher-icons/IOS-60@2x.png" />
      </div>
      <div class="user-info" ng-click="mode = 'list'">
        <div class="greeting">{t}Welcome{/t}</div>
        <div class="username" title="{$smarty.session._sf2_attributes.instance->name}">{$smarty.session._sf2_attributes.instance->name}</div>
      </div>
    </div>
    <div class="user-action-wrapper visible-xs">
      <div class="user-actions cleafix">
        <a class="user-action" href="javascript:UserVoice.showPopupWidget();">
          <i class="fa fa-question fa-lg"></i>
        </a>
        <span class="pointer" ng-click="mode = 'notifications'">
          <div class="user-action">
            <i class="fa fa-bell"></i>
          </div>
          <span class="ng-cloak notifications-orb animated bounceIn" ng-class="{ 'bounceIn': bounce, 'pulse': pulse }" ng-if="notifications.length > 0">
            [% notifications.length %]
          </span>
        </span>
        <div class="user-action no-padding" ng-click="mode = 'profile'">
          <div class="profile-pic">
            {gravatar email=$app.user->email image_dir=$_template->getImageDir() image=true size="35"}
          </div>
        </div>
      </div>
    </div>
    <ul class="collapsed notification-list" ng-class="{ 'collapsed': mode != 'notifications'}">
      <li class="list-title">
        <span class="title">
          <a href="{url name=backend_notifications_list}">{t}Notifications{/t}</a>
        </span>
      </li>
      <li class="notification notification-[% item.style %]" ng-repeat="item in notifications">
        <div class="notification-title">[% item.title %]</div>
        <div ng-bind-html="item.body"></div>
      </li>
    </ul>
    <ul class="collapsed" ng-class="{ 'collapsed': mode != 'profile'}">
      <li class="list-title">
        <span class="title">{$app.user->name}</span>
      </li>
      <li>
        {if is_object($app.user) && $app.user->isMaster()}
          <a ng-href="{get_parameter name=manager_url}manager#/user/{$app.user->id}/show" target="_blank">
            <i class="fa fa-user"></i>
            <span class="title">{t}Profile{/t}</span>
          </a>
        {else}
          {acl isAllowed="USER_EDIT_OWN_PROFILE"}
          <a href="{url name=admin_acl_user_show id=me}">
            <i class="fa fa-user"></i>
            <span class="title">{t}Profile{/t}</span>
          </a>
          {/acl}
        {/if}
      </li>
      <li>
        <a href="{url name=admin_getting_started}">
          <i class="fa fa-rocket"></i>
          <span class="title">{t}Getting started{/t}</span>
        </a>
      </li>
      <li>
        <a role="menuitem" tabindex="-1" href="{url name=core_authentication_logout}">
          <i class="fa fa-power-off m-r-10"></i>
          <span class="title">{t}Log out{/t}</span>
        </a>
      </li>
    </ul>
    {admin_menu file='/Backend/Resources/Menu.php' base=$smarty.const.SRC_PATH}
  </scrollable>
  </div>
  <div class="sidebar-footer-widget">
    <ul>
      <li class="support">
      </li>
      <li class="pin" ng-click="sidebar.pin()" uib-tooltip="{t}Show/hide sidebar{/t}" tooltip-placement="right">
        <i class="fa fa-lg" ng-class="{ 'fa-angle-double-left': sidebar.isPinned(), 'fa-angle-double-right': !sidebar.isPinned()}"></i>
      </li>
    </ul>
  </div>
</div>
