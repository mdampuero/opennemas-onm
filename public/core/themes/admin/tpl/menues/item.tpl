{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Menus{/t} >
    {t}Edit{/t} ({$id})
{/block}

{block name="ngInit"}
  ng-controller="MenuCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-list-alt m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_menus_list}">
    {t}Menus{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="category" iField="category" iIcon="fa fa-newspaper-o" iSearcModel="search_categories" iName="Automatic categories" iData="menuData.category"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="blog-category" iField="blog-category" iIcon="fa fa-bookmark" iSearcModel="search_manual_categories" iName="Manual categories" iData="menuData['blog-category']"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="static" iField="static" iIcon="fa fa-file" iSearcModel="search_pages" iName="Static pages" iData="menuData.static"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="internal" iField="internal" iIcon="fa fa-cube" iSearcModel="search_modules" iName="Modules" iData="menuData.internal"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="syncBlogCategory" iField="syncBlogCategory" iIcon="fa fa-exchange" iSearcModel="search_sites" iName="Synchronized sites" iData="menuData.syncBlogCategory"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="external" iIcon="fa fa-external-link" iName="Custom link" iData="linkData"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iField="name" iTitle="{t}Name{/t}" iRequired=true iValidation=true}
        <div class="form-group no-margin">
          <label for="name" class="form-label">{t}Position{/t}</label>
          <div class="controls" >
            <select name="position" ng-model="item.position">
              <option ng-repeat="(positionKey, positionValue) in data.extra.menu_positions" value="[% positionKey %]">[% positionValue %]</option>
            </select>
            <br>
            <span class="help"><span class="fa fa-info-circle text-info"></span> {t}If your theme has defined positions for menus you can assign one menu to each of them{/t}</span>
          </div>
        </div>
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-title">
      <h4>
        {t}Menu structure{/t}
      </h4>
    </div>
    <div class="grid-body">
      <p>
        {t}Use drag and drop to sort and nest elements.{/t}
      </p>
      <div class="menu-items ng-cloak angular-ui-tree" ui-tree data-max-depth="2">
        <ol ui-tree-nodes="" ng-model="item.menu_items">
          <li ng-repeat="item in item.menu_items track by item.uniqueID" ui-tree-node ng-include="'menu-item'" ng-init="item.position = $index + 1; parentIndex = $index" ></li>
        </ol>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="menu-item">
    {include file="menues/partials/_menu_item.tpl"}
  </script>
  <script type="text/ng-template" id="menu-sub-item">
    {include file="menues/partials/_menu_item.tpl" subitem="true"}
  </script>
{/block}
