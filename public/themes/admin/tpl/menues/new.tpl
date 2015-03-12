{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="
    @Common/components/angular-ui-tree/dist/angular-ui-tree.min.css,
    @AdminTheme/less/_menu.less
  " filters="cssrewrite,less"}
    <link rel="stylesheet" type="text/css" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="footer-js" append}
    {javascripts src="@AdminTheme/js/jquery/jquery.nestedSortable.js,
        @AdminTheme/js/onm/menues.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
    <script type="text/javascript">
    $('[rel=tooltip]').tooltip();
    var menu_messages = {
        remember_save: "{t}Please, remember save changes after finish.{/t}"
    }
    </script>
{/block}

{block name="content"}
<form action="{if isset($menu->pk_menu)}{url name=admin_menu_update id=$menu->pk_menu}{else}{url name=admin_menu_create}{/if}" method="post" name="formulario" id="formulario" ng-controller="MenuCtrl">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-list-alt"></i>
                            {t}Menus{/t}
                        </h4>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <h5>
                            {if isset($menu->name)}
                                {t}Editing menu{/t}
                            {else}
                                {t}Creating menu{/t}
                            {/if}
                        </h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_menus}" title="{t}Go back to list{/t}">
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i> {t}Save{/t}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="content">

        {render_messages}

        <div class="grid simple">
            <div class="grid-title">
                <h4><span class="semi-bold">{t}Basic information{/t}</span></h4>
            </div>
            <div class="grid-body">
                <div class="form-group">
                    <label for="name" class="form-label">{t}Name{/t}</label>
                    <div class="controls">
                        <input type="text" id="name" name="name" value="{$menu->name|default:""}"
                               maxlength="120" tabindex="1" required="required" class="form-input"
                               {if (!empty($menu) && $menu->type neq 'user')} readonly="readonly" {/if} />
                    </div>
                </div>

                {if count($menu_positions) > 1}
                <div class="form-group">
                    <label for="name" class="form-label">{t}Position{/t}</label>
                    <span class="help">{t}(If your theme has defined positions for menus you can assign one menu to each of them){/t}</span>
                    <div class="controls">
                        {html_options options=$menu_positions selected=$menu->position name=position}
                    </div>
                </div>
                {/if}
            </div>
        </div>
        <div class="grid simple">
            <div class="grid-title">
                <h4><span class="semi-bold">{t}Menu contents{/t}</span></h4>
            </div>
            <div class="grid-body" ng-init="menu = {json_encode($menu)|replace:'"':'\''}">
                <p>{t}Pick elements from the right column and drag them to the left column to include them as elements of this menu.{/t}</p>
                <div ui-tree data-max-depth="2">
                  <ol ui-tree-nodes="" ng-model="menu.items">
                    <li ng-repeat="item in menu.items" ui-tree-node ng-include="'menu-item'"></li>
                  </ol>
                </div>
                <button class="btn btn-large" type="button" ng-click="open('modal-add-item')">
                  Add
                </button>
            </div>
        </div>

        <input type="hidden" name="items" id="items" value="" />
        <input type="hidden" name="items-hierarchy" id="items-hierarchy" value="" />
    </div>
    <script type="text/ng-template" id="menu-item">
      <div class="menu-item" ui-tree-handle>
        <span class="item-type" ng-if="item.type == 'external'">
          {t}External link{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'internal'">
          {t}Module{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'blog-category'">
          {t}Category blog{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'category'">
          {t}Frontpage{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'albumCategory'">
          {t}Album category{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'pollCategory'">
          {t}Poll category{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'videoCategory'">
          {t}Video Category{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'static'">
          {t}Static Page{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'syncCategory'">
          {t}Synched category{/t}
        </span>
        <span class="item-type" ng-if="item.type == 'syncBlogCategory'">
          {t}Synched blog category{/t}
        </span>
        <input ng-model="item.title" type="text">
        <button class="btn btn-white pull-right" type="button">
          <i class="fa fa-trash-o text-danger"></i>
        </button>
      </div>
      <ol ui-tree-nodes="" ng-model="item.submenu">
        <li ng-repeat="item in item.submenu" ui-tree-node ng-include="'menu-item'">
        </li>
      </ol>
    </script>
    <script type="text/ng-template" id="modal-add-item">
      {include file="menues/modals/_modalAddItem.tpl"}
    </script>
 </form>
{/block}
