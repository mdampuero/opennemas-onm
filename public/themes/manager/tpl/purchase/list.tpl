<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_purchases_list') %]">
              <i class="fa fa-shopping-cart"></i>
              {t}Purchases{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-success" ng-href="[% routing.ngGenerate('manager_notification_create') %]">
              <i class="fa fa-plus"></i>
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
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="m-r-10 input-prepend inside search-input no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by name{/t}" ng-model="criteria.title_like[0].value" type="text" style="width:250px;"/>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <button class="btn" ng-click="picker.toggle()">Open</button>
        <li class="quicklinks">
          <span class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
          <input class="btn btn-white" datetime-picker="picker" ng-model="criteria.created" type="text">
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
          <button class="btn btn-link" ng-click="criteria = {  title_like: [ { value: '', operator: 'like' } ]}; orderBy = [ { name: 'title', value: 'desc' } ]; pagination = { page: 1, epp: 25 }; refresh()">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="refresh()">
            <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks toggle-columns">
          <div class="btn btn-link" ng-class="{ 'active': !columns.collapsed }" ng-click="toggleColumns()" tooltip-html="'{t}Columns{/t}'" tooltip-placement="left">
            <i class="fa fa-columns"></i>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content">
  <div class="row column-filters collapsed" ng-class="{ 'collapsed': columns.collapsed }">
    <div class="row">
      <div class="col-xs-12 title">
        <h5>{t}Columns{/t}</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-md-3 column">
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-client" checklist-model="columns.selected" checklist-value="'client'" type="checkbox">
            <label for="checkbox-client">
              {t}Client{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-domains" checklist-model="columns.selected" checklist-value="'domains'" type="checkbox">
            <label for="checkbox-domains">
              {t}Client ID{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-invoice-id" checklist-model="columns.selected" checklist-value="'domain_expire'" type="checkbox">
            <label for="checkbox-invoice-id">
              {t}Invoice ID{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-payment-id" checklist-model="columns.selected" checklist-value="'payment-id_mail'" type="checkbox">
            <label for="checkbox-payment-id">
              {t}Payment ID{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'last_login'" type="checkbox">
            <label for="checkbox-created">
              {t}Created{/t}
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead ng-if="items.length >= 0">
            <tr>
              <th style="width:15px;">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="pointer" ng-click="sort('id')" width="50">
                {t}#{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
              </th>
              <th class="pointer" ng-click="sort('title')" ng-show="isEnabled('name')">
                {t}Client{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('title') == 'asc', 'fa fa-caret-down': isOrderedBy('title') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('instance')" width="120">
                {t}Client ID{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('instance') == 'asc', 'fa fa-caret-down': isOrderedBy('instance') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('type')" width="100">
                {t}Payment ID{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('type') == 'asc', 'fa fa-caret-down': isOrderedBy('type') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('start')" width="100">
                {t}Invoice ID{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('start') == 'asc', 'fa fa-caret-down': isOrderedBy('start') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('end')" width="250">
                {t}Created{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('end') == 'asc', 'fa fa-caret-down': isOrderedBy('end') == 'desc'}"></i>
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
              <td ng-show="isEnabled('name')">
                <a ng-href="[% item.show_url %]" title="{t}Edit{/t}">
                  [% item.client.last_name %], [% item.client.first_name %]
                </a>
                <div class="listing-inline-actions">
                  <a class="link" ng-href="[% routing.ngGenerate('manager_purchase_show', { id: item.id }) %]" title="{t}Edit{/t}">
                    <i class="fa fa-pencil"></i>{t}Edit{/t}
                  </a>
                  <button class="link link-danger" ng-click="delete(item)" type="button">
                    <i class="fa fa-trash-o"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td class="text-center">
                <a ng-href="[% extra.freshbooks.url %]/showUser?userid=[% item.client.client_id %]" target="_blank">
                  [% item.client.client_id %]
                </a>
              </td>
              <td class="text-center">
                <a ng-href="[% extra.braintree.url %]/merchants/[% extra.braintree.merchant_id %]/transactions/[% item.payment_id %]" target="_blank">
                  [% item.payment_id %]
                </a>
              </td>
              <td class="text-center">
                <a ng-href="[% extra.freshbooks.url %]/showInvoice?invoiceid=[% item.invoice_id %]" target="_blank">
                  [% item.invoice_id %]
                </a>
              </td>
              <td class="text-center">
                [% item.created %]
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
