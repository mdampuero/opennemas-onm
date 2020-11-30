<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-trash-o"></i>
      {t}Update selected items{/t}
  </h4>
</div>
<div class="modal-body">
    <p ng-if="template.name == 'content_status' && template.value == 0">{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to unpublish %1 item(s)?{/t}</p>
    <p ng-if="template.name == 'content_status' && template.value == 1">{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to publish %1 item(s)?{/t}</p>
    <p ng-if="template.name == 'activated' && template.value == 0">{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to disable %1 item(s)?{/t}</p>
    <p ng-if="template.name == 'activated' && template.value == 1">{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to enable %1 item(s)?{/t}</p>
    <p ng-if="template.name == 'status' && template.value == 'rejected'">{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to reject %1 item(s)?{/t}</p>
    <p ng-if="template.name == 'status' && template.value == 'accept'">{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to accept %1 item(s)?{/t}</p>
    <p ng-if="template.name == 'in_home' && template.value == 0">{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to drop from home %1 item(s)?{/t}</p>
    <p ng-if="template.name == 'in_home' && template.value == 1">{t escape=off 1="[% template.selected.contents.length %]"}Are you sure you want to add to home %1 item(s)?{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, update all{/t}</button>
    <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
</div>
