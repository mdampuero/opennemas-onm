{extends file="common/extension/list.grid.tpl"}

{block name="item"}
  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(content.id) }" ng-repeat="content in contents">
    <div class="dynamic-image-placeholder no-margin" ng-click="select(content);xsOnly($event, toggle, content)">
      <dynamic-image class="img-thumbnail" path="[% routing.generate('backend_ws_news_agency_show_image', { source: content.source, id: content.id }) %]" raw="true">
        <div class="hidden-select" ng-click="toggle(content)"></div>
        <div class="thumbnail-actions thumbnail-actions-fixed text-right">
          <span class="badge badge-success" ng-if="imported.indexOf(content.urn) !== -1">{t}Imported{/t}</span>
        </div>
      </dynamic-image>
    </div>
  </div>
{/block}
