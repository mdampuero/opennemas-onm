<div ng-init="template.extra.client ? template.step = 2 : template.step = 1">
  <div class="modal-body">
    <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss()" type="button">
      <i class="fa fa-times"></i>
    </button>
    <h3 class="p-b-30 p-t-30 text-center">{t}Are you sure?{/t}</h3>
    <p class="text-center">{t}Activate users has a cost of 12€ user/month.{/t} {t escape=off}If you have questions please have a look to our help article and/or contact us at <a href="mailto:sales@openhost.es">sales@openhost.es</a>{/t}</p>
    <div ng-if="template.step != 2">
      <div ng-init="countries = template.extra.countries; taxes = template.extra.taxes">
        <p class="text-center">{t}Please, fill billing information below, we will send you an invoice for all activated users at the end of the month.{/t}</p>
        {include file="client/form.tpl" modal="true"}
      </div>
    </div>
    <div ng-show="template.step === 2">
      <div class="text-center">
        <div class="checkbox inline p-b-15 p-t-30 text-left">
          <input id="terms" name="terms" ng-model="terms" type="checkbox">
          <label for="terms">{t}I understand and accept the cost of the operation.{/t}</label>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer row">
    <div class="col-xs-6">
      <button class="btn btn-block btn-danger text-uppercase" data-dismiss="modal" ng-click="dismiss()" ng-disabled="loading" type="button">
        <h4 class="bold text-uppercase text-white">
          <i class="fa fa-times m-r-5"></i>
          {t}No{/t}
        </h4>
      </button>
    </div>
    <div class="col-xs-6">
      <button type="button" class="btn btn-block btn-success btn-loading" ng-click="confirm()" ng-disabled="!terms || loading">
        <h4 class="bold text-uppercase text-white">
          <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
          {t}Yes{/t}
        </h4>
      </button>
    </div>
  </div>
</div>
