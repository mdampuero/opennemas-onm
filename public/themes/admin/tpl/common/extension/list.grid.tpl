{block name="begin-wrapper"}{/block}
<div class="m-b-0 grid simple ng-cloak no-animate row" ng-if="!flags.http.loading && data.items.length > 0 && isModeSupported() && app.mode === 'grid'">
  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item" ng-class="{ 'selectable': isSelectable(item), 'selected': isSelected(getItemId(item)) }" ng-repeat="item in items">
    <div class="dynamic-image-placeholder">
      {block name="item"}{/block}
    </div>
  </div>
</div>
{block name="master-row"}{/block}
{block name="end-wrapper"}{/block}
{block name="sidebar"}{/block}
