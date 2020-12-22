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
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=admin_menus}">
                  {t}Menus{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks visible-xs">
              <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/221738-opennemas-c%C3%B3mo-cambiar-el-men%C3%BA-de-mi-peri%C3%B3dico" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question fa-lg"></i>
              </a>
            </li>
            <li class="hidden-xs m-l-5 m-r-5 quicklinks">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="hidden-xs quicklinks">
              <h4>
                {if empty($menu->name)}{t}Create{/t}{else}{t}Edit{/t}{/if}
              </h4>
            </li>
            {if $multilanguage}
            <li class="hidden-xs m-l-5 m-r-5 quicklinks">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="hidden-xs ng-cloak quicklinks">
              <translator ng-model="lang" options="{json_encode($language_data)|clear_json}"/>
            </li>
            {/if}
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" type="submit" data-text="{t}Saving{/t}..." id="save-button">
                  <i class="fa fa-save m-r-4"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="row">
        <div class="col-md-4 col-md-push-8">
          {block name="rightColumn"}{/block}
        </div>
        <div class="col-md-8 col-md-pull-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label class="form-label" for="name">
                  {t}Name{/t}
                </label>
                <div class="controls">
                  <input class="form-control" type="text" id="name" name="name" value="{$menu->name|default:""}" maxlength="120" required>
                </div>
              </div>
              {if !empty($menu_positions) && count($menu_positions) > 1}
                <div class="form-group no-margin">
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
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                {t}Menu structure{/t}
              </h4>
              <button class="btn btn-white pull-right btn-small" type="button" ng-click="open('modal-add-item')">
                <i class="fa fa-plus"></i>
                {t}Add items{/t}
              </button>
            </div>
            <div class="grid-body">
              <p>
                {t}Use drag and drop to sort and nest elements.{/t}
              </p>
              <div class="menu-items ng-cloak" ui-tree data-max-depth="2">
                <ol ui-tree-nodes="" ng-model="menu.items">
                  <li ng-repeat="item in menu.items" ui-tree-node ng-include="'menu-item'" ng-init="parentIndex = $index"></li>
                </ol>
              </div>
            </div>
          </div>
      <input type="hidden" name="items" ng-value="menuItems"/>
      <input type="hidden" name="lang" ng-value="lang"/>

        </div>

      </div>
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
