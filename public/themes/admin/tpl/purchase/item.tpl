{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-controller="PurchaseCtrl" ng-init="getPurchase({$id})">
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
            <li class="quicklinks seperate hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {t}Show{/t}
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_my_newspaper}" title="{t}Go back{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content ng-cloak no-animate" ng-if="loading">
      <div class="spinner-wrapper">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
    </div>
    <div class="content ng-cloak" ng-if="!error && !loading && purchase">
      <div class="row">
        <div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2">
          <div class="grid simple">
            <div class="grid-body">
              <div class="p-t-5 pull-left">
                <h4 class="semi-bold">[% purchase.client.last_name %], [% purchase.client.first_name %]</h4>
                <address>
                  <strong ng-if="client.company">[% client.company %]</strong><br>
                  [% purchase.client.address %]<br>
                  [% purchase.client.postal_code %], [% purchase.client.city %], [% purchase.client.state %]<br>
                  [% extra.countries[purchase.client.country] %]<br>
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
                <div class="pull-right m-r-15 m-t-30">
                  <strong class="k">{t}Date{/t}:</strong>
                  [% purchase.date | moment : 'YYYY-MM-DD' %]
                </div>
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
                    <tr ng-repeat="item in purchase.details">
                      <td>[% item.description %]</td>
                      <td class="text-right">[% item.unit_cost %] €</td>
                      <td class="text-right">[% item.unit_cost %] €</td>
                    </tr>
                    <tr>
                      <td rowspan="[% payment.type === 'CreditCard' && payment.nonce ? 4 : 3 %]">
                      </td>
                      <td class="text-right"><strong>Subtotal</strong></td>
                      <td class="text-right">[% subtotal %] €</td>
                    </tr>
                    <tr>
                      <td class="text-right no-border"><strong>{t}VAT{/t} ([% purchase.details[0].tax1_percent %]%)</strong></td>
                      <td class="text-right">[% tax %] €</td>
                    </tr>
                    <tr ng-if="purchase.method === 'CreditCard'">
                      <td class="text-right no-border"><strong>{t}Pay with credit card{/t}</strong></td>
                      <td class="text-right">[% purchase.fee | number : 2 %] €</td>
                    </tr>
                    <tr>
                      <td class="text-right no-border"><div class="well well-small green"><strong>Total</strong></div></td>
                      <td class="text-right"><strong>[% purchase.total | number : 2 %] €</strong></td>
                    </tr>
                  </tbody>
                </table>
                <h5 class="semi-bold" ng-if="purchase.terms">{t}Terms{/t}</h5>
                <div class="m-b-30" ng-bind-html="getTerms()" ng-if="purchase.terms"></div>
                <h5 class="semi-bold" ng-if="purchase.notes">{t}Notes{/t}</h5>
                <div class="m-b-30" ng-bind-html="getNotes()" ng-if="purchase.notes"></div>
              </div>
              <div class="row p-t-30" ng-if="purchase.total > 0">
                <div class="col-lg-4 col-lg-offset-4">
                  <a class="btn btn-block btn-loading btn-success" ng-href="[% routing.generate('backend_ws_purchase_get_pdf', { id: purchase.id }) %]" target="_blank">
                    <h4 class="text-uppercase text-white">
                      <i class="fa fa-absolute fa-download m-l-15 m-t-5"></i>
                      {t}Download{/t}
                    </h4>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}
