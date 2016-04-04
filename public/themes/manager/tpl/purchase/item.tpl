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
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            <span ng-if="!purchase.id">{t}New purchase{/t}</span>
            <span ng-if="purchase.id">{t}Edit purchase{/t}</span>
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_purchases_list') %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <form name="purchaseForm" novalidate>
    <div class="row">
      <div class="col-lg-8">
        <div class="grid simple">
          <div class="grid-body">
            <h4>{t}Client{/t}</h4>
            <div class="row">
              <div class="col-lg-8 col-md-7">
                <div class="row">
                  <div class="col-sm-6 m-b-10">
                    <label><strong>{t}First name{/t}</strong></label>
                    [% purchase.client.first_name %]
                  </div>
                  <div class="col-sm-6 m-b-10">
                    <label><strong>{t}Last name{/t}</strong></label>
                    [% purchase.client.last_name %]</br>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 m-b-10">
                    <label><strong>{t}Company{/t}</strong></label>
                    [% purchase.client.company %]
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-8 m-b-10">
                    <label><strong>{t}Address{/t}</strong></label> [% purchase.client.address %]
                  </div>
                  <div class="col-sm-4 m-b-10">
                    <label><strong>{t}Postal code{/t}</strong></label> [% purchase.client.postal_code %]<br>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-4 m-b-10">
                    <label><strong>{t}City{/t}</strong></label> [% purchase.client.city %]
                  </div>
                  <div class="col-sm-4 m-b-10">
                    <label><strong>{t}State{/t}</strong></label> [% purchase.client.state %]
                  </div>
                  <div class="col-sm-4 m-b-10">
                    <label><strong>{t}Country{/t}</strong></label> [% extra.countries[purchase.client.country] %]
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-lg-offset-1 col-md-4">
                <h5 class="text-center">{t}View on{/t}</h5>
                <a class="btn btn-block btn-white text-uppercase" ng-href="[% routing.ngGenerate('manager_client_show', { 'id': purchase.client.id }) %]">
                  <strong>Opennemas</strong>
                </a>
                <a class="btn btn-block btn-white text-uppercase" ng-href="[% extra.braintree.url %]/merchants/[% extra.braintree.merchant_id %]/customers/[% purchase.client.id %]" target="_blank">
                  <strong>Braintree</strong>
                </a>
                <a class="btn btn-block btn-white text-uppercase text-success" ng-href="[% extra.freshbooks.url %]/showUser?userid=[% purchase.client.id %]" target="_blank">
                  <strong>Freshbooks</strong>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="grid simple">
          <div class="grid-body">
            <h4>{t}Payment{/t}</h4>
            <a class="btn btn-block btn-white text-uppercase" ng-href="[% extra.braintree.url %]/merchants/[% extra.braintree.merchant_id %]/transactions/[% purchase.payment_id %]" target="_blank">
              <strong>Braintree</strong>
            </a>
            <h4 class="p-t-30">{t}Invoice{/t}</h4>
            <a class="btn btn-block btn-white text-uppercase" ng-href="[% routing.generate('manager_ws_purchase_get_pdf', { id: purchase.id, token: token }) %]" target="_blank">
              <strong>PDF</strong>
            </a>
            <a class="btn btn-block btn-white text-uppercase text-success" ng-href="[% extra.freshbooks.url %]/showInvoice?invoiceid=[% purchase.invoice_id %]" target="_blank">
              <strong>Freshbooks</strong>
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="grid simple">
      <div class="grid-body">
        <table class="no-margin table table-invoice">
          <thead>
            <tr>
              <th>{t}Description{/t}</th>
              <th class="text-right" width="120">{t}Quantity{/t}</th>
              <th class="text-right" width="120">{t}Cost{/t}</th>
            </tr>
          </thead>
          <tr ng-repeat="line in purchase.details">
            <td>[% line.name %]</td>
            <td class="text-right">[% line.quantity %]</td>
            <td class="text-right">[% line.unit_cost %] €</td>
          </tr>
          <tr>
            <td rowspan="3"></td>
            <td class="text-right">
              <strong>{t}Subtotal{/t}</strong>
            </td>
            <td class="text-right">
              [% subtotal | number : 2 %] €
            </td>
          </tr>
          <tr>
            <td class="text-right no-border">
              <strong>
                [% purchase.details[0].tax1_name %] ([% purchase.details[0].tax1_percent %])%
              </strong>
            </td>
            <td class="text-right">[% tax | number : 2 %] €</td>
          </tr>
          <tr>
            <td class="text-right no-border">
              <div class="no-margin well well-small green">
                <strong>{t}Total{/t}</strong>
              </div>
            </td>
            <td class="text-right">[% total | number : 2 %] €</td>
          </tr>
        </table>
      </div>
    </div>
  </form>
</div>
