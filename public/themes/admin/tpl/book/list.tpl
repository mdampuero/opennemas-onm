{extends file="base/admin.tpl"}
{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-book"></i>
              {t}Books{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
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
                <a href="{url name=admin_books_widget}" {if $category=='widget'}class="active"{/if}>
                  {t}Widget Home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=admin_books}" {if $category != 'widget'}class="active"{/if}>{t}Listing{/t}</a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {if $category == 'widget' && $page <= 1}
            <li class="quicklinks">
              <a class="btn btn-white" href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}" id="save_button">
                <span class="fa fa-save"></span>
                {t}Save positions{/t}
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {/if}
            {acl isAllowed="BOOK_CREATE"}
            <li>
              <a class="btn btn-primary" href="{url name=admin_books_create}" title="{t}New book{/t}" id="create_button">
                <span class="fa fa-plus"></span>
                {t}Create{/t}
              </a>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>

  {if $category == 'widget'}
    {include file="book/partials/_book_list_home.tpl"}
  {else}
    {include file="book/partials/_book_list.tpl"}
  {/if}
</div>
{/block}
