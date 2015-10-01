<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h4 class="modal-title">{t}Content preview{/t}</h4>
</div>
<div class="modal-body">
  <h5><strong>[% template.selected.title %]</strong></h5>
  <p ng-bind-html="template.selected.summary"></p>
  <strong class="pull-right">
    [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
  </strong>
  <div ng-bind-html="template.selected.body" ng-if="template.selected.type === 'text'"></div>
  <img ng-src="[% template.selected.body %]" ng-if="template.selected.type === 'photo'"/>
</div>
<div class="modal-footer">
  <button class="btn btn-link" ng-click="close()" type="button">{t}Close{/t}</button>
</div>
