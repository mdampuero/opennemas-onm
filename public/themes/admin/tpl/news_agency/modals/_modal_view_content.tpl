<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h4 class="modal-title">{t}Content preview{/t}</h4>
</div>
<div class="modal-body">
  <div class="content-body" ng-class="{ 'content-body-related': template.related.length > 0 }">
    <h5><strong>[% template.content.title %]</strong></h5>
    <div class="p-b-10 p-t-5 clearfix">
      <div class="pull-left">
        <strong>{t}Date{/t}:</strong> [% template.content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : template.timezone %]
      </div>
      <div class="pull-right">
      <strong>{t}Priority{/t}:</strong>
        <span class="priority">
          <span ng-if="template.content.priority == 1" class="badge badge-important">{t}Urgent{/t}</span>
          <span ng-if="template.content.priority == 2" class="badge badge-warning">{t}Important{/t}</span>
          <span ng-if="template.content.priority == 3" class="badge badge-info">{t}Normal{/t}</span>
          <span ng-if="template.content.priority < 1 || template.content.priority > 3" class="badge">{t}Basic{/t}</span>
        </span>
      </div>
    </div>
    <p ng-bind-html="template.content.summary"></p>
    <hr>
    <div ng-bind-html="template.content.body" ng-if="template.content.type === 'text'"></div>
    <img ng-src="[% routing.generate('backend_ws_news_agency_show_image', { source: template.content.source, id: template.content.id }) %]" ng-if="template.content.type === 'photo'"/>
  </div>
  <div class="content-related-wrapper" ng-if="template.related.length > 0 && template.content.type == 'text'">
    <div class="content-related" ng-if="template.content.type == 'text'">
      <img class="img-thumbnail" ng-if="related.type === 'photo'" ng-repeat="related in template.related" ng-src="[% template.routing.generate('backend_ws_news_agency_show_image', { source: related.source, id: related.id }) %]" />
    </div>
  </div>
</div>
<div class="modal-footer">
  <button class="btn btn-white" ng-click="close(1)" ng-if="template.imported" type="button">{t}Close{/t}</button>
  <button class="btn btn-success" ng-click="confirm(1)" ng-if="!template.imported" type="button">{t}Import{/t}</button>
</div>
