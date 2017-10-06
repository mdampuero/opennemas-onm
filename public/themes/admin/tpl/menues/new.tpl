{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if isset($menu->pk_menu)}{url name=admin_menu_update id=$menu->pk_menu}{else}{url name=admin_menu_create}{/if}" method="post" name="formulario" ng-controller="MenuCtrl" ng-init="init({json_encode($menu)|clear_json}, {json_encode($language_data)|clear_json})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-list-alt page-navbar-icon"></i>
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/221738-opennemas-c%C3%B3mo-cambiar-el-men%C3%BA-de-mi-peri%C3%B3dico" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
                {t}Menus{/t}
              </h4>
            </li>
            <li class="quicklinks visible-xs">
              <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/221738-opennemas-c%C3%B3mo-cambiar-el-men%C3%BA-de-mi-peri%C3%B3dico" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question fa-lg"></i>
              </a>
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
            {if $multilanguage}
            <li class="hidden-xs quicklinks ng-cloak">
              <span class="h-seperate"></span>
            </li>
            <li class="hidden-xs ng-cloak">
              <translator ng-model="lang" options="{json_encode($language_data)|clear_json}"/>
            </li>
            {/if}
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_menus}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks hidden-xs">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}..." id="save-button">
                  <span class="fa fa-save"></span>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple">
        <div class="grid-body">
          <div class="row">
            {if $multilanguage}
            <div class="col-md-ng-cloak hidden-md hidden-lg hidden-sm clearfix">
              Language:
              <div class="cleafix pull-right">
                <translator ng-model="lang" options="{json_encode($language_data)|clear_json}"/>
              </div>
              <hr>
            </div>
            {/if}
            <div class="col-sm-6 col-xs-12 form-group">
              <label for="name" class="form-label">{t}Name{/t}</label>
              <div class="controls">
                <input type="text" id="name" name="name" value="{$menu->name|default:""}"
                maxlength="120" tabindex="1" required class="form-control"
                {if (!empty($menu->id) && $menu->type neq 'user')} readonly="readonly" {/if} />
              </div>
            </div>
            {if count($menu_positions) > 1}
            <div class="col-sm-6 col-xs-12 form-group">
              <label for="name" class="form-label">{t}Position{/t}</label>
              <div class="controls">
                {html_options options=$menu_positions selected=$menu->position name=position}
                <br>
                <span class="help"><span class="fa fa-info-circle text-info"></span> {t}If your theme has defined positions for menus you can assign one menu to each of them{/t}</span>
              </div>
            </div>
            {/if}
          </div>
        </div>
      </div>
      <div class="grid simple">
        <div class="grid-title clearfix">
          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <h4><span class="semi-bold">{t}Menu structure{/t}</span></h4>
              <h6>
                {t}Use drag and drop to sort and nest elements.{/t}
                <br> {t}Click on arrow to reveal more configuration options.{/t}
              </h6>
            </div>
            <div class="col-xs-12 col-sm-6 right">
              <button class="btn btn-white" type="button" ng-click="open('modal-add-item')">
                <i class="fa fa-plus"></i>
                {t}Add items{/t}
              </button>
            </div>
          </div>
        </div>
        <div class="grid-body">
          <div class="menu-items ng-cloak" ui-tree data-max-depth="2">
            <ol ui-tree-nodes="" ng-model="menu.items">
              <li ng-repeat="item in menu.items" ui-tree-node ng-include="'menu-item'" ng-init="parentIndex = $index"></li>
            </ol>
          </div>
        </div>
      </div>

      <input type="hidden" name="items" ng-value="menuItems"/>
    </div>
    <script type="text/ng-template" id="menu-item">
      {include file="menues/partials/_menu_item.tpl"}
    </script>
    <script type="text/ng-template" id="menu-sub-item">
      {include file="menues/partials/_menu_item.tpl" subitem="true"}
    </script>
    <script type="text/ng-template" id="modal-add-item">
      {include file="menues/modals/_modalAddItem.tpl"}
    </script>
 </form>
{/block}
