<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.lists = !expanded.lists">
  <i class="fa fa-list m-r-10"></i>{t}Lists{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.lists }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded' : expanded.lists }">
  <div ng-show="!data.extra.subscriptions || data.extra.subscriptions.length === 0">
    <i class="fa fa-warning m-r-5 text-warning"></i>
    {t escape=off 1="[% routing.generate('backend_subscriptions_list') %]"}There are no enabled <a href="%1">subscriptions</a>{/t}
  </div>
  <div class="form-group no-margin" ng-show="data.extra.subscriptions && data.extra.subscriptions.length > 0">
    <div class="checkbox m-b-5" ng-repeat="subscription in data.extra.subscriptions">
      <input checklist-model="item.subscriptions" checklist-value="subscription.pk_user_group" id="checkbox-[% $index %]" type="checkbox">
      <label for="checkbox-[% $index %]">[% subscription.name %]</label>
    </div>
    <div class="help m-l-3" ng-if="isHelpEnabled()">
      <i class="fa fa-info-circle m-r-5 text-info"></i>
      {t}The content will be fully available only for subscribers in the selected subscriptions{/t}
    </div>
  </div>
</div>
