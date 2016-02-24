<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_clients_list') %]">
              <i class="fa fa-user"></i>
              {t}Clients{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-success text-uppercase text-white" ng-href="[% routing.ngGenerate('manager_client_create') %]">
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
            <input class="input-min-45 input-200" ng-class="{ 'dirty': criteria.name_like[0].value }" ng-keyup="searchByKeypress($event)" ng-model="criteria.name_like[0].value" placeholder="{t}Search{/t}" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="criteria.name_like[0].value = null" ng-show="criteria.name_like[0].value">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="pagination.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="resetFilters()" tooltip="{t}Reset filters{/t}" tooltip-placement="bottom">
            <i class="fa fa-fire fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="list()" tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-refresh fa-lg" ng-class="{ 'fa-spin': loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
  </form>
</div>
<div class="content">
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="dropdown dropdown-table-columns" ng-class="{ 'dropdown-table-columns-hidden': !header, 'open': open }" ng-mouseover="header = 1; openColumns()" ng-mouseleave="header = 0;closeColumns()">
        <button class="btn btn-white dropdown-toggle" data-toggle="dropdown"  tooltip="{t}Columns{/t}" type="button">
          <i class="fa fa-th-list"></i>
        </button>
        <div class="dropdown-menu pull-right" ng-mouseleave="closeColumns()">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
            <label for="checkbox-name">
              {t}Name{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-email" checklist-model="columns.selected" checklist-value="'email'" type="checkbox">
            <label for="checkbox-email">
              {t}Email{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-vat-number" checklist-model="columns.selected" checklist-value="'vat_number'" type="checkbox">
            <label for="checkbox-vat-number">
              {t}Vat number{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-phone" checklist-model="columns.selected" checklist-value="'phone'" type="checkbox">
            <label for="checkbox-phone">
              {t}Phone{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-address" checklist-model="columns.selected" checklist-value="'address'" type="checkbox">
            <label for="checkbox-address">
              {t}Address{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-postal-code" checklist-model="columns.selected" checklist-value="'postal_code'" type="checkbox">
            <label for="checkbox-postal-code">
              {t}Postal code{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-city" checklist-model="columns.selected" checklist-value="'city'" type="checkbox">
            <label for="checkbox-city">
              {t}City{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-state" checklist-model="columns.selected" checklist-value="'state'" type="checkbox">
            <label for="checkbox-state">
              {t}State{/t}
            </label>
          </div>
          <div class="checkbox check-default">
            <input id="checkbox-country" checklist-model="columns.selected" checklist-value="'country'" type="checkbox">
            <label for="checkbox-country">
              {t}Country{/t}
            </label>
          </div>
        </div>
      </div>
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead ng-show="items.length >= 0">
            <tr ng-mouseover="header = 1" ng-mouseleave="header = 0">
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
              <th class="pointer" ng-click="sort('last_name')" ng-show="isColumnEnabled('name')">
                {t}Name{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('last_name') == 'asc', 'fa fa-caret-down': isOrderedBy('last_name') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('email')" ng-show="isColumnEnabled('email')" width="250">
                {t}Email{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('email') == 'asc', 'fa fa-caret-down': isOrderedBy('email') == 'desc'}"></i>
              </th>
              <th class="pointer text-center " ng-click="sort('vat_number')" ng-show="isColumnEnabled('vat_number')" width="120">
                {t}VAT No.{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('vat_number') == 'asc', 'fa fa-caret-down': isOrderedBy('vat_number') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('phone')" ng-show="isColumnEnabled('phone')" width="150">
                {t}Phone{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('phone') == 'asc', 'fa fa-caret-down': isOrderedBy('phone') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('address')" ng-show="isColumnEnabled('address')" width="120">
                {t}Address{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('address') == 'asc', 'fa fa-caret-down': isOrderedBy('address') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('postal_code')" ng-show="isColumnEnabled('postal_code')" width="80">
                {t}P.O.{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('postal_code') == 'asc', 'fa fa-caret-down': isOrderedBy('postal_code') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('city')" ng-show="isColumnEnabled('city')" width="200">
                {t}City{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('city') == 'asc', 'fa fa-caret-down': isOrderedBy('city') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('state')" ng-show="isColumnEnabled('state')" width="150">
                {t}State{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('state') == 'asc', 'fa fa-caret-down': isOrderedBy('state') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('country')" ng-show="isColumnEnabled('country')" width="150">
                {t}Country{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('country') == 'asc', 'fa fa-caret-down': isOrderedBy('country') == 'desc'}"></i>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-if="items.length == 0">
              <td class="empty" colspan="10">{t}There is no available instances yet{/t}</td>
            </tr>
            <tr ng-if="items.length >= 0" ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
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
                [% item.last_name %], [% item.first_name %]
                <div class="listing-inline-actions">
                  <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_client_show', { id: item.id }) %]" title="{t}Edit{/t}">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <button class="btn btn-link text-danger" ng-click="delete(item)" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td ng-show="isColumnEnabled('email')">
                [% item.email %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('vat_number')">
                [% item.vat_number %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('phone')">
                [% item.phone %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('address')">
                [% item.address %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('postal_code')">
                [% item.postal_code %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('city')">
                [% item.city %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('state')">
                [% item.state %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('country')">
                [% extra.countries[item.country] %]
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-right" ng-if="items.length > 0">
        <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
