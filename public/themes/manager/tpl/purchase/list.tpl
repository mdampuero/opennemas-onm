<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_purchases_list') %]">
              <i class="fa fa-shopping-bag"></i>
              {t}Purchases{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions hidden pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-success text-uppercase text-white" ng-href="[% routing.ngGenerate('manager_purchase_create') %]">
              <i class="fa fa-plus m-r-5"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
{include file='common/selected_navbar.tpl' list="purchase"}
<div class="page-navbar filters-navbar">
  <form name="filterForm">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="input-min-45 input-200" ng-class="{ 'dirty': criteria.client }" ng-keyup="searchByKeypress($event)" ng-model="criteria.client" placeholder="{t}Search by client{/t}" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="criteria.client = null" ng-show="criteria.client">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon cursor-pointer" ng-click="pickerFrom.toggle()">
              <i class="fa fa-calendar m-r-5"></i>
              {t}From{/t}
            </span>
            <input class="input-100" datetime-picker="pickerFrom" datetime-picker-format="YYYY-MM-DD" name="from" ng-class="{ 'dirty': criteria.from }" ng-model="criteria.from" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="criteria.from = null" ng-show="criteria.from">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon cursor-pointer" ng-click="pickerTo.toggle()">
              <i class="fa fa-calendar m-r-5"></i>
              {t}To{/t}
            </span>
            <input class="input-100" datetime-picker="pickerTo" datetime-picker-format="YYYY-MM-DD" name="to" ng-class="{ 'dirty': criteria.to }" ng-model="criteria.to" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="criteria.to = null" ng-show="criteria.to">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="criteria.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="resetFilters()" uib-tooltip="{t}Reset filters{/t}" tooltip-placement="bottom">
            <i class="fa fa-fire fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-refresh fa-lg" ng-class="{ 'fa-spin': loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
  </form>
</div>
<div class="content ng-hide" ng-show="items">
  <div class="p-b-100 p-t-100 text-center" ng-if="items.length == 0">
    <i class="fa fa-7x fa-user-secret"></i>
    <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
  </div>
  <div class="grid simple" ng-if="items.length > 0">
    <div class="column-filters-toggle hidden-sm" ng-click="toggleColumns()"></div>
    <div class="column-filters collapsed hidden-sm" ng-class="{ 'collapsed': columns.collapsed }">
      <h5>{t}Columns{/t}</h5>
      <div class="row">
        <div class="col-md-3">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
            <label for="checkbox-name">
              {t}Name{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-total" checklist-model="columns.selected" checklist-value="'total'" type="checkbox">
            <label for="checkbox-total">
              {t}Total{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-client_id" checklist-model="columns.selected" checklist-value="'client_id'" type="checkbox">
            <label for="checkbox-client_id">
              {t}Client ID{/t}
            </label>
          </div>
        </div>
        <div class="col-md-3">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-invoice-id" checklist-model="columns.selected" checklist-value="'invoice_id'" type="checkbox">
            <label for="checkbox-invoice-id">
              {t}Invoice ID{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-payment-id" checklist-model="columns.selected" checklist-value="'payment_id'" type="checkbox">
            <label for="checkbox-payment-id">
              {t}Payment ID{/t}
            </label>
          </div>
          <div class="checkbox check-default">
            <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'created'" type="checkbox">
            <label for="checkbox-created">
              {t}Created{/t}
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead>
            <tr>
              <th width="15">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="pointer" ng-click="sort('id')" width="50">
                {t}#{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
              </th>
              <th class="pointer" ng-click="sort('client')" ng-show="isColumnEnabled('name')">
                {t}Name{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('client') == 'asc', 'fa fa-caret-down': isOrderedBy('client') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('total')" ng-show="isColumnEnabled('total')" width="80">
                {t}Total{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('total') == 'asc', 'fa fa-caret-down': isOrderedBy('total') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('client_id')" ng-show="isColumnEnabled('client_id')" width="120">
                {t}Client ID{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('client_id') == 'asc', 'fa fa-caret-down': isOrderedBy('client_id') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('payment_id')" ng-show="isColumnEnabled('payment_id')" width="120">
                {t}Payment ID{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('payment_id') == 'asc', 'fa fa-caret-down': isOrderedBy('payment_id') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('invoice_id')" ng-show="isColumnEnabled('invoice_id')" width="120">
                {t}Invoice ID{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('invoice_id') == 'asc', 'fa fa-caret-down': isOrderedBy('invoice_id') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('created')" ng-show="isColumnEnabled('created')" width="250">
                {t}Created{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('created') == 'asc', 'fa fa-caret-down': isOrderedBy('created') == 'desc'}"></i>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
              <td>
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td>
                [% item.id %]
              </td>
              <td ng-show="isColumnEnabled('name')">
                [% item.client.last_name %], [% item.client.first_name %]
                <div class="listing-inline-actions">
                  <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_purchase_show', { id: item.id }) %]" title="{t}Edit{/t}">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <button class="btn-link text-danger" ng-click="delete(item.id)" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td class="text-right" ng-show="isColumnEnabled('total')">
                [% item.total | number : 2 %] €
              </td>
              <td class="text-center" ng-show="isColumnEnabled('client_id')">
                <a ng-href="[% routing.ngGenerate('manager_client_show', { id : item.client.id }) %]" target="_blank">
                  [% item.client.id %]
                </a>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('payment_id')">
                <a ng-href="[% extra.braintree.url %]/merchants/[% extra.braintree.merchant_id %]/transactions/[% item.payment_id %]" target="_blank">
                  [% item.payment_id %]
                </a>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('invoice_id')">
                <a ng-href="[% extra.freshbooks.url %]/showInvoice?invoiceid=[% item.invoice_id %]" target="_blank">
                  [% item.invoice_id %]
                </a>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('created')">
                [% item.created %]
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-right">
        <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
