<div class="modal-body" ng-init="strings = [ '{t}user{/t}', '{t}subscriber{/t}', '{t}subscriber{/t} + {t}user{/t}' ]; source = strings[template.item.type] ; target= strings[template.type]">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="p-b-30 p-t-30 text-center">{t}Are you sure?{/t}</h3>
  <h4 class="p-b-30 text-center">{t escape=off 1="[% source %]" 2="[% target %]"}Do you want to convert the <strong>%1</strong> to <strong>%2</strong>?{/t}</h4>
  <p class="text-center" ng-if="template.type === 0">
    {t 1="[% source %]"}This means the item will not be a %1 anymore. It will only appear in the list of users and the subscriptions will be removed too.{/t}
  </p>
  <p class="text-center" ng-if="template.type === 1">
    {t 1="[% source %]"}This means the item will not be a  anymore. It will only appear in the list of subscribers and the user groups will be removed too.{/t}
  </p>
  <p class="text-center" ng-if="template.type === 2">
    {t}This means the item will be a subscriber and a user in the system. It will appear in the list of users and the list of subscribers.{/t}
  </p>
  <p class="text-center">
    {t}You will be able to convert the item again in the future.{/t}
  </p>
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
    <button type="button" class="btn btn-block btn-success btn-loading" ng-click="confirm()" ng-disabled="loading">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
        {t}Yes{/t}
      </h4>
    </button>
  </div>
</div>
