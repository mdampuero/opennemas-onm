<div class="p-t-5 pull-left">
  <h4 class="semi-bold">[% client.last_name %], [% client.first_name %]</h4>
  <address>
    <strong ng-if="client.company">[% client.company %]</strong><br>
    [% client.address %]<br>
    [% client.postal_code %], [% client.city %], [% client.state %]<br>
    [% countries[client.country] %]<br>
  </address>
</div>
<div class="pull-right">
  <img alt="" class="invoice-logo p-b-15" height="50" src="/assets/images/logos/opennemas-powered-horizontal.png">
  <address>
    <strong>Openhost, S.L.</strong><br>
    Progreso 64, 4º A<br>
    32003, Ourense, Ourense<br>
    [% countries['ES']%]<br>
  </address>
</div>
<div class="clearfix"></div>
<div class="table-wrapper">
  <table class="m-t-30 table table-invoice">
    <thead>
      <tr>
        <th class="text-left uppercase">{t}Description{/t}</th>
        <th class="text-right uppercase" width="140">{t}Unit price{/t}</th>
        <th class="text-right uppercase" width="90">{t}Total{/t}</th>
      </tr>
    </thead>
    <tbody>
      <tr ng-repeat="item in cart" ng-controller="CartCtrl">
        <td>[% item.name %]</td>
        <td class="text-right">[% getPrice(item, item.priceType).value %] €</td>
        <td class="text-right">[% getPrice(item, item.priceType).value %] €</td>
      </tr>
      <tr>
        <td rowspan="[% payment.type === 'CreditCard' && payment.nonce ? 4 : 3 %]">
        </td>
        <td class="text-right"><strong>Subtotal</strong></td>
        <td class="text-right">[% subtotal %] €</td>
      </tr>
      <tr ng-if="payment.type === 'CreditCard' && payment.nonce">
        <td class="text-right no-border"><strong>{t}Pay with credit card{/t}</strong></td>
        <td class="text-right">[% fee | number : 2 %] €</td>
      </tr>
      <tr>
        <td class="text-right no-border"><strong>{t}VAT{/t} ([% vatTax %]%)</strong></td>
        <td class="text-right">[% tax | number : 2 %] €</td>
      </tr>
      <tr>
        <td class="text-right no-border"><div class="well well-small green"><strong>Total</strong></div></td>
        <td class="text-right"><strong>[% total | number : 2 %] €</strong></td>
      </tr>
    </tbody>
  </table>
  <div ng-if="getTerms()">
    <h5 class="semi-bold">{t}Terms{/t}</h5>
    <div class="m-b-30" ng-bind-html="getTerms()"></div>
  </div>
  <div ng-if="getNotes()">
    <h5 class="semi-bold">{t}Notes{/t}</h5>
    <div class="m-b-30" ng-bind-html="getNotes()"></div>
  </div>
</div>
