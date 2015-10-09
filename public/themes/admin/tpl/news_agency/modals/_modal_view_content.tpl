<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h4 class="modal-title">{t}Content preview{/t}</h4>
</div>
<div class="modal-body">
  <div class="content-body" ng-class="{ 'content-body-related': template.related.length > 0 }">
    <h5><strong>[% template.content.title %]</strong></h5>
    <p ng-bind-html="template.content.summary"></p>
    <div class="p-b-15 p-t-15 text-right">
      [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
    </div>
    <div ng-bind-html="template.content.body" ng-if="template.content.type === 'text'"></div>
    <img ng-src="[% routing.generate('backend_ws_news_agency_show_image', { source: template.content.source, id: template.content.id }) %]" ng-if="template.content.type === 'photo'"/>
  </div>
  <div class="content-related-wrapper" ng-if="template.related.length > 0 && template.content.type == 'text'">
    <div class="content-related" ng-if="template.content.type == 'text'">
      <img class="img-thumbnail" ng-if="related.type === 'photo'" ng-repeat="related in template.related" ng-src="[% template.routing.generate('backend_ws_news_agency_show_image', { source: related.source, id: related.id }) %]" />
    </div>
  </div>
</div>
