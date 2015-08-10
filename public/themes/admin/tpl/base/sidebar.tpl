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
        <div class="user-action" ng-click="mode = 'notifications'">
          <i class="fa fa-bell"></i>
        </div>
        <div class="user-action no-padding" ng-click="mode = 'profile'">
          <div class="profile-pic">
            {gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true size="35"}
          </div>
        </div>
      </div>
    </div>
    <ul class="collapsed notification-list" ng-class="{ 'collapsed': mode != 'notifications'}">
      <li class="list-title">
        <span class="title">{t}Notifications{/t}</span>
      </li>
      <li class="notification">
        <p>{t}No notifications for now{/t}</p>
      </li>
      <!-- <li class="notification notification-success">
        <div class="notification-title">Success!</div>
        <p>{t}This is a notification{/t}</p>
      </li>
      <li class="notification notification-error">
        <div class="notification-title">Error!</div>
        <p>{t}This is a notification{/t}</p>
      </li>
      <li class="notification notification-warning">
        <div class="notification-title">Warning!</div>
        <p>{t}This is a notification{/t}</p>
      </li> -->
    </ul>
    <ul class="collapsed" ng-class="{ 'collapsed': mode != 'profile'}">
      <li class="list-title">
        <span class="title">{$smarty.session.realname}</span>
      </li>
      <li>
        {acl isAllowed="USER_EDIT_OWN_PROFILE"}
        <a href="{url name=admin_acl_user_show id=me}">
          <i class="fa fa-user"></i>
          <span class="title">{t}Profile{/t}</span>
        </a>
        {/acl}
      </li>
      <li>
        <a href="{url name=admin_getting_started}">
          <i class="fa fa-rocket"></i>
          <span class="title">{t}Getting started{/t}</span>
        </a>
      </li>
      <li>
        <a role="menuitem" tabindex="-1" href="{url name=admin_logout}">
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
      <li class="pin" ng-click="sidebar.pin()" tooltip="{t}Show/hide sidebar{/t}" tooltip-placement="right">
        <i class="fa fa-lg" ng-class="{ 'fa-angle-double-left': sidebar.isPinned(), 'fa-angle-double-right': !sidebar.isPinned()}"></i>
      </li>
    </ul>
  </div>
</div>
