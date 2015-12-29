<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
    {t}Update selected items{/t}
  </h4>
</div>
<div class="modal-body" ng-init="template.value == 1 ? terms = false : terms = true">
  <p ng-if="template.name == 'create'">{t escape=off}Are you sure you want to create a new user?{/t}</p>
  <p ng-if="template.name == 'activated' && template.value == 0">{t escape=off}Are you sure you want to disable [% template.selected.contents.length %] user(s)?{/t}</p>
  <p ng-if="template.name == 'activated' && template.value == 1">{t escape=off}Are you sure you want to enable [% template.selected.contents.length %] user(s)?{/t}</p>
  <p class="text-danger" ng-show="template.name == 'activated' && template.value == 1">
    <strong>{t}Warning{/t}:</strong> {t}Enable this users will cost you [% Math.round(template.selected.contents.length * 40) / 100 %] €/day.{/t}
  </p>
  <p class="text-danger" ng-show="template.name == 'create'">
    <strong>{t}Warning{/t}:</strong> {t}Create this user will cost you 0.4 €/day.{/t}
  </p>
  <div class="checkbox" ng-show="template.value == 1">
    <input id="terms" name="terms" ng-model="terms" type="checkbox">
    <label for="terms">{t}I understand and accept the charges.{/t}</label>
  </div>
</div>
<div class="modal-footer">
  <span class="loading" ng-if="deleting == 1"></span>
  <button class="btn btn-primary" ng-click="confirm()" ng-disabled="!terms" type="button">
    <span ng-show="template.name != 'create'">{t}Yes, update all{/t}</span>
    <span ng-show="template.name == 'create'">{t}Yes, save it{/t}</span>
  </button>
  <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
</div>
