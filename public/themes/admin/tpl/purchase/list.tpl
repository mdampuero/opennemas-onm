{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="PurchaseListCtrl" ng-init="list()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-shopping-cart page-navbar-icon"></i>
                {t}Purchases{/t}
              </h4>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="page-navbar filters-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section filter-components">
            <li class=" ng-cloak quicklinks hidden-xs">
              <div class="input-group input-group-animated">
                <span class="input-group-addon cursor-pointer" ng-click="pickerFrom.toggle()">
                  <i class="fa fa-calendar m-r-5"></i>
                  {t}From{/t}
                </span>
                <input class="input-100" datetime-picker="pickerFrom" datetime-picker-format="YYYY-MM-DD" name="from" ng-class="{ 'dirty': criteria.from }" ng-model="criteria.from" type="text">
                <span class="input-group-addon input-group-addon-inside no-animate pointer" ng-click="criteria.from = null" ng-show="criteria.from">
                  <i class="fa fa-times"></i>
                </span>
              </div>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="ng-cloak quicklinks hidden-xs">
              <div class="input-group input-group-animated">
                <span class="input-group-addon cursor-pointer" ng-click="pickerTo.toggle()">
                  <i class="fa fa-calendar m-r-5"></i>
                  {t}To{/t}
                </span>
                <input class="input-100" datetime-picker="pickerTo" datetime-picker-format="YYYY-MM-DD" name="to" ng-class="{ 'dirty': criteria.to }" ng-model="criteria.to" type="text">
                <span class="input-group-addon input-group-addon-inside no-animate pointer" ng-click="criteria.to = null" ng-show="criteria.to">
                  <i class="fa fa-times"></i>
                </span>
              </div>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-sm hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="criteria.epp">
                <ui-select-match>
                  <strong>{t}View{/t}:</strong> [% $select.selected %]
                </ui-select-match>
                <ui-select-choices repeat="item in views  | filter: $select.search">
                  <div ng-bind-html="item | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
          </ul>
          <ul class="nav quick-section pull-right ng-cloak" ng-if="items.length > 0">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="ng-cloak p-b-100 p-t-100 text-center" ng-if="!loading && (!items || items.length === 0)">
        <i class="fa fa-7x fa-user-secret"></i>
        <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
      </div>
      <div class="grid simple" ng-if="!loading && (!items || items.length > 0)">
        <div class="grid-body no-padding">
          <div class="table-wrapper ng-cloak">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="pointer" ng-click="sort('updated')">
                    {t}Date{/t}
                    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('updated') == 'asc', 'fa fa-caret-down': isOrderedBy('updated') == 'desc'}"></i>
                  </th>
                  <th  width="150">
                    {t}Method{/t}
                  </th>
                  <th class="pointer text-right" ng-click="sort('total')" width="150">
                    {t}Total{/t}
                    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('total') == 'asc', 'fa fa-caret-down': isOrderedBy('total') == 'desc'}"></i>
                  </th>
                  <th class="text-center pointer" width="150">
                    {t}Status{/t}
                  </th>
                  <th width="150">
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                  <td>
                    [% item.updated | moment : 'LL' : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                  </td>
                  <td>
                    <i class="fa" ng-class="{ 'fa-paypal': item.method === 'PayPalAccount', 'fa-credit-card': item.method === 'CreditCard' }" ng-if="item.total !== 0"></i>
                    <span ng-if="item.total !== 0">[% item.method === 'PayPalAccount' ? '{t}PayPal{/t}' : '{t}Credit Card{/t}' %]</span>
                    <span ng-if="item.total === 0">-</span>
                  </td>
                  <td class="text-right">
                    [% item.total | number : 2 %] €
                  </td>
                  <td class="text-center">
                    {t}Paid{/t}
                  </td>
                  <td>
                    <a ng-href="[% routing.generate('backend_purchase_show', { id: item.id }) %]" title="{t}Show{/t}">
                      {t}View invoice{/t}
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="grid-footer clearfix ng-cloak" ng-if="!loading && items.length > 0">
          <div class="pull-right">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}
