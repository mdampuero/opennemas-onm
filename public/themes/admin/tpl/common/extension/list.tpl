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
            <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
              <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
            </li>
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
                [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
              </h4>
            </li>
          </ul>
          <ul class="nav quick-section pull-right">
            {block name="selectedActions"}{/block}
          </ul>
        </div>
      </div>
    </div>
    <div class="page-navbar filters-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            {block name="leftFilters"}{/block}
          </ul>
          <ul class="nav quick-section pull-right ng-cloak" ng-if="app.mode === 'list' && items.length > 0">
            {block name="rightFilters"}
              <li class="quicklinks hidden-xs">
                <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
              </li>
            {/block}
          </ul>
        </div>
      </div>
    </div>
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
          <h3>{t}Unable to find any item that matches your search.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      {block name="list"}{/block}
    </div>
    {block name="modals"}{/block}
  </div>
{/block}
