<div class="modal-body">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="p-b-30 p-t-30 text-center">{t}Are you sure?{/t}</h3>
  <h4 class="p-b-30 text-center" ng-if="template.type === 0">{t escape=off}Do you want to convert the <strong>subscriber</strong> to an <strong>user</strong>?{/t}</h4>
  <h4 class="p-b-30 text-center" ng-if="template.type === 2">{t escape=off}Do you want to convert the<strong> subscriber</strong> to a <strong>user + subscriber</strong>?{/t}</h4>
  <p class="text-center" ng-if="template.type === 0">
    {t}This means the subscriber will not be a subscriber anymore. The user will only appear in the list of users and the subscriptions will be removed too.{/t}
  </p>
  <p class="text-center" ng-if="template.type === 0">
    {t}You will be able to convert the user to subscriber again.{/t}
  </p>
  <p class="text-center" ng-if="template.type === 2">
    {t}This means the subscriber will be a subscriber and a user in the system. It will appear in both list of users and list of subscribers. The subscriptions will not be modified.{/t}
  </p>
  <p class="text-center" ng-if="template.type === 2">
    {t}You will be able to convert the user + subscriber to only user or only subscriber again.{/t}
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
