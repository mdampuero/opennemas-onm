<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h4 class="modal-title">{t}Content preview{/t}</h4>
</div>
<div class="modal-body">
  <h5><strong>[% template.selected.title %]</strong></h5>
  <p ng-bind-html="template.selected.summary"></p>
  <div class="p-b-15 p-t-15 text-right">
    [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
  </div>
  <div ng-bind-html="template.selected.body" ng-if="template.selected.type === 'text'"></div>
  <img ng-src="[% routing.generate('backend_ws_news_agency_show_image', { source: template.selected.source, id: template.selected.id }) %]" ng-if="template.selected.type === 'photo'"/>
</div>
