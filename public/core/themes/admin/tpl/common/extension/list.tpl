{extends file="base/admin.tpl"}

{block name="content"}
  <div {block name="ngInit"}{/block}>
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                {block name="icon"}{/block}
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                {block name="title"}{/block}
              </h4>
            </li>
            {block name="extraTitle"}{/block}
            {block name="translator"}
              <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="hasMultilanguage()">
                <h4>
                  <i class="fa fa-angle-right"></i>
                </h4>
              </li>
              <li class="hidden-xs ng-cloak quicklinks" ng-if="hasMultilanguage()">
                <translator keys="data.extra.keys" ng-model="config.locale.selected" class="btn-group" options="data.extra.locale"></translator>
              </li>
            {/block}
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {block name="primaryActions"}{/block}
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section pull-left">
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
                <i class="fa fa-arrow-left fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <h4>
                [% countSelectedItems() %]
                <span class="hidden-xs">{t}items selected{/t}</span>
              </h4>
            </li>
          </ul>
          <ul class="nav quick-section pull-right">
            {block name="selectedActions"}{/block}
          </ul>
        </div>
      </div>
    </div>
    {block name="filters"}
      <div class="page-navbar filters-navbar">
        <div class="navbar navbar-inverse">
          <div class="navbar-inner">
            <ul class="nav quick-section">
              <li class="m-r-10 quicklinks ng-cloak" ng-if="isModeSupported() && app.mode === 'grid'" uib-tooltip="{t}Mosaic{/t}" tooltip-placement="bottom">
                <button class="btn btn-link" ng-click="setMode('list')">
                  <i class="fa fa-lg fa-th"></i>
                </button>
              </li>
              <li class="m-r-10 quicklinks ng-cloak" ng-if="isModeSupported() && app.mode === 'list'" uib-tooltip="{t}List{/t}" tooltip-placement="bottom">
                <button class="btn btn-link" ng-click="setMode('grid')">
                  <i class="fa fa-lg fa-list"></i>
                </button>
              </li>
              {block name="leftFilters"}{/block}
              <li class="quicklinks hidden-xs ng-cloak" ng-show="!isModeSupported() || app.mode === 'list'">
                <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-lg fa-refresh m-l-5 m-r-5" ng-class="{ 'fa-spin': flags.http.loading }"></i>
                </button>
              </li>
            </ul>
            <ul class="nav quick-section quick-section-fixed ng-cloak" ng-if="data.items.length > 0">
              {block name="rightFilters"}
                <li class="quicklinks">
                  <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total" hide-views="isModeSupported() && app.mode === 'grid'"></onm-pagination>
                </li>
              {/block}
            </ul>
          </div>
        </div>
      </div>
    {/block}
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-show="!flags.http.loading && items.length == 0">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find any item or you don't have permission to see the list.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      {block name="list"}{/block}
    </div>
    {block name="modals"}{/block}
  </div>
{/block}
