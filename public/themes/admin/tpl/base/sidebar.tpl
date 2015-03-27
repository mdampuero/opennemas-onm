<div class="sidebar" id="main-menu" ng-swipe-right="sidebar.swipeOpen()" ng-swipe-left="sidebar.swipeClose()" ng-mouseenter="sidebar.mouseEnter()" ng-mouseleave="sidebar.mouseLeave()">
  <div class="overlay" ng-click="sidebar.open()"></div>
  <div class="sidebar-wrapper">
  <scrollable>
    <a class="header-logo pull-left" href="{url name=admin_welcome}">
      <h1>
      <span class="first-char">o</span><span class="title-token">pen<strong>nemas</strong></span>
      </h1>
    </a>
    <div class="user-info-wrapper clearfix">
      <div class="profile-wrapper">
        {if $smarty.session._sf2_attributes.instance.logo}
          <img src="{$smarty.const.INSTANCE_MEDIA}/sections/{$smarty.session._sf2_attributes.instance.logo}" />
        {else}
          <img src="/assets/images/launcher-icons/IOS-60@2x.png" />
        {/if}
      </div>
      <div class="user-info">
        <div class="greeting">{t}Welcome{/t}</div>
        <div class="username" title="{$smarty.session._sf2_attributes.instance.name}">{$smarty.session._sf2_attributes.instance.name}</div>
      </div>
    </div>
    {admin_menu file='/Backend/Resources/Menu.php' base=$smarty.const.SRC_PATH}
  </scrollable>
  </div>
  <div class="sidebar-footer-widget">
    <ul>
      <li class="support">
      </li>
      <li class="pin" ng-click="sidebar.pin()" tooltip="{t}Show/hide sidebar{/t}">
        <i class="fa fa-lg" ng-class="{ 'fa-angle-double-left': sidebar.isPinned(), 'fa-angle-double-right': !sidebar.isPinned()}"></i>
      </li>
    </ul>
  </div>
</div>
