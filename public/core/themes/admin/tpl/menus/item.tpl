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

.
{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iShowSlug="true" iFilterData="filterData" iType="category" iIcon="fa fa-newspaper-o" iSearchModel="search_manual_categories" iName="{t}Manual categories{/t}" iData="dragables.category" iFilter=true}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iShowSlug="true" iFilterData="filterData" iType="blog-category" iIcon="fa fa-bookmark" iSearchModel="search_categories" iName="{t}Automatic categories{/t}" iData="dragables['blog-category']" iFilter=true}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iShowSlug="true" iFilterData="filterData" iType="static" iIcon="fa fa-file" iSearchModel="search_pages" iName="{t}Static pages{/t}" iData="dragables.static" iFilter=true}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="internal" iIcon="fa fa-cube" iSearchModel="search_modules" iName="{t}Modules{/t}" iData="dragables.internal" iFilter=true}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iShowSlug="true" iFilterData="filterData" iType="tags" iIcon="fa fa-tags" iName="{t}Tags{/t}" iData="dragables.tags" iFilter=true}
      {is_module_activated name="SYNC_MANAGER"}
        {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="syncBlogCategory" iIcon="fa fa-exchange" iSearchModel="search_sites" iName="{t}Synchronized sites{/t}" iFilter=false iData="dragables.syncBlogCategory"}
      {/is_module_activated}
      {include file="ui/component/content-editor/accordion/dragable_list.tpl" iFilterData="filterData" iType="external" iIcon="fa fa-external-link" iName="{t}Custom link{/t}" iData="linkData" iSimple=true iFilter=false}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-title">
      <h4>
        {t}Menu structure{/t}
      </h4>
      <br>
      <div class="form-group no-margin">
        <span class="help"><span class="fa fa-info-circle text-info"></span> {t}Use drag and drop to sort and nest elements.{/t}</span>
      </div>
    </div>
    <div class="grid-body">
      <div class="menu-items ng-cloak angular-ui-tree" ui-tree="treeOptions" data-dropzone-enabled="true" data-max-depth="2">
        <ol ui-tree-nodes="" ng-model="parents">
          <li ng-repeat="item in parents track by item.pk_item+item.locale" ui-tree-node ng-include="'menu-item'" ng-show="visible(item, false)"></li>
        </ol>
      </div>
    </div>
  </div>
{/block}

{block name="topColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="row">
        <div class="col-lg-8 col-md-7 col-sm-12">
          {include file="ui/component/input/text.tpl" iField="name" iTitle="{t}Name{/t}" iRequired=true iValidation=true}
        </div>
        <div class="col-lg-4 col-md-5 col-sm-12">
        <div class="form-group no-margin">
          <label for="name" class="form-label">{t}Position{/t}</label>
          <div class="controls" >
            <select name="position" ng-model="item.position" class="w-100">
              <option ng-repeat="(positionKey, positionValue) in data.extra.menu_positions" value="[% positionKey %]">[% positionValue %]</option>
            </select>
            <br>
            <span class="help"><span class="fa fa-info-circle text-info"></span> {t}If your theme has defined positions for menus you can assign one menu to each of them{/t}</span>
          </div>
        </div>
        </div>
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
