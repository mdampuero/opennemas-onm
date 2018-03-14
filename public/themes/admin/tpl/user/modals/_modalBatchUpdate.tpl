<div ng-init="(!template.backend_access || template.extra.client) ? template.step = 2 : template.step = 1">
  <div ng-if="template.step != 2">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
      <h4 class="modal-title">
        {t}Billing information{/t}
      </h4>
    </div>
    <div class="modal-body" ng-init="countries = template.extra.countries; taxes = template.extra.taxes">
      <p>{t}Activate the access to Backend means to add a User to your platform and has a cost of 12€/month.{/t} {t escape=off}If you have questions please have a look to our help article and/or contact us at <a href="mailto:sales@openhost.es">sales@openhost.es</a>{/t}</p>
      <p>{t}Please, fill billing information below, we will send you an invoice for all activated users at the end of the month.{/t} </p>
      {include file="client/form.tpl" modal="true"}
    </div>
  </div>
  <div ng-if="template.step === 2">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
      <h4 class="modal-title">
        {t}Update selected items{/t}
      </h4>
    </div>
    <div class="modal-body" ng-init="template.value == 1 && template.backend_access ? terms = false : terms = true">
      <p ng-if="template.value == 1 && template.backend_access">{t}Activate the access to Backend means to add a User to your platform and has a cost of 12€/month.{/t} {t escape=off}If you have questions please have a look to our help article and/or contact us at <a href="mailto:sales@openhost.es">sales@openhost.es</a>{/t}</p>
      <p ng-if="template.name == 'create'">{t escape=off}Are you sure you want to create a new user?{/t}</p>
      <p ng-if="template.name == 'update'">{t escape=off}Are you sure you want to update the user?{/t}</p>
      <p ng-if="template.name == 'activated' && template.value == 0">{t escape=off}Are you sure you want to disable [% template.selected.items.length %] user(s)?{/t}</p>
      <p ng-if="template.name == 'activated' && template.value == 1">{t escape=off}Are you sure you want to enable [% template.selected.items.length %] user(s)?{/t}</p>
      <p class="text-danger" ng-show="template.name == 'activated' && template.value == 1">

      <div class="checkbox" ng-show="template.value == 1 && template.backend_access">
        <input id="terms" name="terms" ng-model="terms" type="checkbox">
        <label for="terms">{t}I understand and accept the cost of the operation.{/t}</label>
      </div>
    </div>
    <div class="modal-footer">
      <span class="loading" ng-if="deleting == 1"></span>
      <button class="btn btn-primary" ng-click="confirm()" ng-disabled="template.backend_access && !terms" type="button">
        <span ng-show="template.name != 'create' && template.name != 'update'">{t}Yes, update all{/t}</span>
        <span ng-show="template.name == 'create'">{t}Yes, save it{/t}</span>
        <span ng-show="template.name == 'update'">{t}Yes, update it{/t}</span>
      </button>
      <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
    </div>
  </div>
</div>
