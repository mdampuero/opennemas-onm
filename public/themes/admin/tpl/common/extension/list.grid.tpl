{block name="begin-wrapper"}{/block}
<div class="grid simple ng-cloak no-animate row" ng-if="!flags.http.loading && data.items.length > 0 && isModeSupported() && app.mode === 'grid'">
  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item" ng-class="{ 'selectable': isSelectable(item), 'selected': isSelected(getItemId(item)) }" ng-repeat="item in items">
    <div class="dynamic-image-placeholder">
      {block name="item"}{/block}
    </div>
  </div>
</div>
{block name="sidebar"}{/block}
<div class="ng-cloak p-t-15 p-b-15 pointer text-center" ng-click="scroll()" ng-if="!flags.http.loading && data.total != data.items.length && isModeSupported() && app.mode === 'grid'">
  <h5>
    <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="flags.http.loadingMore"></i>
    <span ng-if="!flags.http.loadingMore">{t}Load more{/t}</span>
    <span ng-if="flags.http.loadingMore">{t}Loading{/t}</span>
  </h5>
</div>
{block name="end-wrapper"}{/block}
