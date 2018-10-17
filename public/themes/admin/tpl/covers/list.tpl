{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-newspaper-o m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_covers}" title="{t}Go back to list{/t}">
                {t}Covers{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <div data-toggle="dropdown">
              {if $category == 'widget'}
                {t}Widget Home{/t}
              {else}
                {t}Listing{/t}
              {/if}
              <span class="caret"></span>
            </div>
            <ul class="dropdown-menu">
              <li>
                <a href="{url name=backend_covers_widget}" {if $category == 'widget'}class="active"{/if}>
                  {t}Widget Home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=backend_covers}" {if $category != 'widget'}class="active"{/if}>
                  {t}Listing{/t}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              {acl isAllowed="KIOSKO_ADMIN"}
              <li>
                <a class="btn btn-link" href="{url name=backend_covers_config}" title="{t}Config covers module{/t}">
                  <span class="fa fa-cog fa-lg"></span>
                </a>
              </li>
              {/acl}
            </li>
            {acl isAllowed="KIOSKO_WIDGET"}
            {if $category eq 'widget'}
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li>
              <a class="btn btn-white" href="#" ng-click="savePositions('backend_ws_contents_save_positions')"  title="{t}Save positions{/t}">
                <span class="fa fa-save m-r-5"></span>{t}Save positions{/t}
              </a>
            </li>
            {/if}
            {/acl}
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {acl isAllowed="KIOSKO_CREATE"}
            <li class="m-l-10">
              <a class="btn btn-primary" href="{url name=backend_cover_create}" title="{t}New cover{/t}" id="create-button">
                <i class="fa fa-plus m-r-5"></i>{t}Create{/t}
              </a>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>

  {if $category == 'widget'}
    {include file="covers/partials/_cover_list_home.tpl"}
  {else}
    {include file="covers/partials/_cover_list.tpl"}
  {/if}
</div>
{/block}
