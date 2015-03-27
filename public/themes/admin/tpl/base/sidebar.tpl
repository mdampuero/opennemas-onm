<div class="sidebar" id="main-menu" ng-swipe-right="sidebar.swipeOpen()" ng-swipe-left="sidebar.swipeClose()" ng-mouseleave="sidebar.mouseLeave()">
  <div class="overlay" ng-click="sidebar.open()" ng-mouseenter="sidebar.mouseEnter()"></div>
  <div class="sidebar-wrapper">
  <scrollable>
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
    <div class="user-action-wrapper visible-xs">
      <div class="user-actions cleafix">
        <div class="user-action ng-cloak" ng-click="mode = 'list'" ng-show="mode && mode != 'list'">
          <i class="fa fa-plus fa-bars"></i>
        </div>
        <div class="user-action" ng-click="mode = 'create'" ng-show="!mode || mode != 'create'">
          <i class="fa fa-plus fa-lg"></i>
        </div>
        <div class="user-action" ng-click="mode = 'notifications'" ng-show="!mode || mode != 'notifications'">
          <i class="fa fa-bell"></i>
        </div>
        <div class="user-action no-padding" ng-click="mode = 'profile'" ng-show="!mode || mode != 'profile'">
          <div class="profile-pic">
            {gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true size="35"}
          </div>
        </div>
      </div>
    </div>
    <ul class="collapsed" ng-class="{ 'collapsed': mode != 'create'}">
      <li class="list-title">
        <span class="title">{t}Create{/t}</span>
      </li>
      <li>
        <a href="{url name=admin_article_create}">
          <i class="fa fa-file-text"></i>
          <span class="title">{t}New article{/t}</span>
        </a>
      </li>
      <li>
        <a href="{url name=admin_opinion_create}">
          <i class="fa fa-quote-right"></i>
          <span class="title">{t}New opinion{/t}</span>
        </a>
      </li>
      <li>
        <a href="{url name=admin_letter_create}">
          <i class="fa fa-envelope"></i>
          <span class="title">{t}New letter{/t}</span>
        </a>
      </li>
      <li>
        <a href="{url name=admin_album_create}">
          <i class="fa fa-stack-overflow"></i>
          <span class="title">{t}New album{/t}</span>
        </a>
      </li>
      <li>
        <a href="{url name=admin_poll_create}">
          <i class="fa fa-pie-chart"></i>
          <span class="title">{t}New poll{/t}</span>
        </a>
      </li>
      <li>
        <a href="{url name=admin_special_create}">
          <i class="fa fa-star"></i>
          <span class="title">{t}New special{/t}</span>
        </a>
      </li>
      <li>
        <a href="{url name=admin_static_page_create}">
          <i class="fa fa-file"></i>
          <span class="title">{t}New static page{/t}</span>
        </a>
      </li>
    </ul>
    <ul class="collapsed" ng-class="{ 'collapsed': mode != 'profile'}">
      <li class="list-title">
        <span class="title">{$smarty.session.realname}</span>
        <div class="profile-pic">
          {gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true size="25"}
        </div>
      </li>
      <li>
        <a href="{url name=admin_acl_user_show id=me}">
          <i class="fa fa-user"></i>
          <span class="title">{t}Profile{/t}</span>
        </a>
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
