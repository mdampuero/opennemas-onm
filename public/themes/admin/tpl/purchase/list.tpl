{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="PurchaseListCtrl">

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
    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-if="!loading && items.length == 0">
          <div class="center">
            <h4>{t}Unable to find any article that matches your search.{/t}</h4>
            <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
          </div>
        </div>
        <div class="table-wrapper ng-cloak" ng-if="!loading && items.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th class="pointer" ng-click="sort('id')" width="50">
                  {t}#{/t}
                  <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
                </th>
                <th class="pointer" ng-click="sort('client')">
                  {t}Name{/t}
                  <i ng-class="{ 'fa fa-caret-up': isOrderedBy('client') == 'asc', 'fa fa-caret-down': isOrderedBy('client') == 'desc'}"></i>
                </th>
                <th class="text-center" width="250">
                  {t}Email{/t}
                </th>
                <th class="pointer text-right" ng-click="sort('total')" width="80">
                  {t}Total{/t}
                  <i ng-class="{ 'fa fa-caret-up': isOrderedBy('total') == 'asc', 'fa fa-caret-down': isOrderedBy('total') == 'desc'}"></i>
                </th>
                <th class="pointer" width="50">
                  {t}Method{/t}
                </th>
                <th class="pointer text-center" ng-click="sort('updated')" width="250">
                  {t}Updated{/t}
                  <i ng-class="{ 'fa fa-caret-up': isOrderedBy('updated') == 'asc', 'fa fa-caret-down': isOrderedBy('updated') == 'desc'}"></i>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                <td>
                  [% item.id %]
                </td>
                <td>
                  <span ng-if="item.client">
                    [% item.client.last_name %], [% item.client.first_name %]
                  </span>
                  <i ng-if="!item.client">{t}No information{/t}</i>
                  <div class="listing-inline-actions">
                    <a class="btn btn-link" ng-href="[% routing.generate('backend_purchase_show', { id: item.id }) %]" title="{t}Show{/t}">
                      <i class="fa fa-eye m-r-5"></i>{t}Show{/t}
                    </a>
                  </div>
                </td>
                <td class="text-center">
                  [% item.client.email %]
                </td>
                <td class="text-right">
                  [% item.total | number : 2 %] €
                </td>
                <td class="text-center">
                  <i class="fa" ng-class="{ 'fa-paypal': item.method === 'PayPalAccount', 'fa-credit-card': item.method === 'CreditCard' }" ng-if="item.total !== 0"></i>
                  <span ng-if="item.total === 0">-</span>
                </td>
                <td class="text-center">
                  [% item.updated %]
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
</div>
{/block}
