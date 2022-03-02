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
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="category" iIcon="fa fa-newspaper-o" iSearchModel="search_categories" iName="{t}Automatic categories{/t}" iData="menuData.category"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="blog-category" iIcon="fa fa-bookmark" iSearchModel="search_manual_categories" iName="{t}Manual categories{/t}" iData="menuData['blog-category']"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="static" iIcon="fa fa-file" iSearchModel="search_pages" iName="{t}Static pages{/t}" iData="menuData.static"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="internal" iIcon="fa fa-cube" iSearchModel="search_modules" iName="{t}Modules{/t}" iData="menuData.internal"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="syncBlogCategory" iIcon="fa fa-exchange" iSearchModel="search_sites" iName="{t}Synchronized sites{/t}" iData="menuData.syncBlogCategory"}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="external" iIcon="fa fa-external-link" iName="{t}Custom link{/t}" iData="linkData" iSimple=true}
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
          <li ng-repeat="item in item.menu_items" ui-tree-node ng-include="'menu-item'"></li>
        </ol>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="menu-item">
    {include file="menus/partials/_menu_item.tpl"}
  </script>
  <script type="text/ng-template" id="menu-sub-item">
    {include file="menus/partials/_menu_item.tpl" subitem="true"}
  </script>
{/block}
