<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  <h3>{t}Delete album{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off 1="[% content.name %]"}Are you sure that do you want delete "%1"?{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary yes"  ng-click="confirm()" ng-if="deleting == 1" type="button">{t}Yes, delete{/t}</button>
    <button class="btn secondary no" ng-click="close()" type="button">{t}No{/t}</button>
</div>
