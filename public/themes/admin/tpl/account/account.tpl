{extends file="base/admin.tpl"}

{block name="content"}
<div class="content my-account-page" ng-controller="AccountCtrl" ng-init="instance = {json_encode($instance)|clear_json}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-home fa-lg"></i>
                {t}My account{/t}
              </h4>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="row" id="info-page" >
      <div class="col-xs-12 col-sm-6">
        <div class="row instance-info">
          <div class="col-xs-12 m-b-15">
            <div class="tiles white">
              <div class="tiles green body-wrapper">
                <div class="tiles-body">
                  <div class="instance-name-wrapper">
                    <h3 class="text-white semi-bold">{$instance->name}</h3>
                    {foreach $instance->domains as $domain}
                    <h5 class="text-white">
                      <i class="fa fa-globe"></i>
                      <a href="http://{$domain}" target="_" class="text-white">{$domain}</a>
                    </h5>
                    {/foreach}
                  </div>
                </div>
                <div class="tile-footer clearfix">
                  <div class="row">
                    <a class="text-white contact-email col-xs-12 col-md-6" href="mailto:{$instance->contact_mail}" uib-tooltip="{t}This is the email used to create your newspaper{/t}" tooltip-placement="bottom">
                      <i class="fa fa-envelope"></i>
                      {$instance->contact_mail}
                    </a>
                    <a href="#" class="text-white created-at col-xs-12 col-md-6">
                      <i class="fa fa-calendar"></i>
                      <span uib-tooltip="{t 1=$instance->created->format('Y-m-d H:i:s')}Your newspaper was created on %1{/t}" tooltip-placement="bottom">{$instance->created->format('Y-m-d H:i:s')}</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="tiles green m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Support plan{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last">
                    <div class="item-count">
                      {$instance->support_plan} <i class="fa fa-info-circle" uib-tooltip="{t}Support by tickets{/t}" tooltip-placement="bottom"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tiles purple m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Storage size{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last">
                    <span class="item-count">{$instance->media_size|string_format:"%.2f"} MB</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="tiles blue m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Users{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last">
                    <span class="item-title">{t}Activated{/t}</span>
                    <span class="item-count">{$instance->users}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tiles yellow m-b-15">
              <div class="tiles-body">
                <div class="tiles-title text-uppercase text-black">
                  {t}Page views this month{/t}
                </div>
                <div class="widget-stats">
                  <div class="wrapper last">
                    <span class="item-count">
                      {t}coming soon... work in progress...{/t}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="tiles white">
              <div class="tiles-body clearfix">
                <div>
                  <div class="more-plans clearfix">
                    <p class="col-xs-12 col-md-8">{t}Opennemas offers many more modules and solutions{/t}</p>
                    <a href="{url name=admin_store_list}" target="_blank" class="btn btn-primary btn-large col-xs-12 col-md-4">
                      {t}Check out our modules{/t}
                    </a>
                  </div>
                  <div class="get-help clearfix">
                    <p class="col-xs-12 col-md-8">{t}If you need a custom plan or you want to purchase a plan or module please click in the next link:{/t}</p>
                    <a href="mailto:sales@openhost.es" class="btn btn-white btn-large col-xs-12 col-md-4">
                      {t}Contact Us{/t}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6">
        <div class="tiles white clearfix">
          <div class="tiles-body">
            <div class="tiles-title text-uppercase text-black">
              {t}Billing information{/t}
            </div>
            {if !empty($client)}
              <div class="row p-b-15 p-t-15">
                <div class="col-sm-6">
                  <strong>{t}Name{/t}:</strong> {$client->last_name}, {$client->first_name}
                </div>
                <div class="col-sm-6">
                  <strong>{t}Company{/t}:</strong> {$client->company}
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-6">
                  <strong>{t}VAT number{/t}:</strong> {$client->vat_number}
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-6">
                  <strong>{t}Email{/t}:</strong> {$client->email}
                </div>
                <div class="col-sm-6">
                  <strong>{t}Phone{/t}:</strong> {$client->phone}
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-8">
                  <strong>{t}Address{/t}:</strong> {$client->address}
                </div>
                <div class="col-sm-4">
                  <strong>{t}Postal code{/t}:</strong> {$client->postal_code}
                </div>
              </div>
              <div class="row p-b-15">
                <div class="col-sm-4">
                  <strong>{t}City{/t}:</strong> {$client->city}
                </div>
                <div class="col-sm-4">
                  <strong>{t}State{/t}:</strong> {$client->state}
                </div>
                <div class="col-sm-4">
                  <strong>{t}Country{/t}:</strong> {$countries[$client->country]}
                </div>
              </div>
              <div class="row p-t-15">
                <div class="col-md-12">
                  <h5>{t escape=off}Something wrong? Contact our <a href="javascript:UserVoice.showPopupWidget();">support team</a>.{/t}</h5>
                </div>
              </div>
            {else}
              <h4 class="p-t-30 text-center">{t}You have no billing information{/t}</h4>
              <h5 class="p-b-30 text-center">{t escape=off}You will be asked to add it during the checkout in our store{/t}</h5>
            {/if}
          </div>
        </div>
        <div class="m-t-15 tiles white clearfix" ng-controller="PurchaseListCtrl" ng-init="criteria = { epp: 5, orderBy: { updated: 'desc' }, page: 1 }; list();">
          <div class="tiles-body">
            <div class="m-b-15 tiles-title text-uppercase text-black">
              {t}Last purchases{/t}
            </div>
            <div ng-if="loading">
              <div class="spinner-wrapper">
                <div class="loading-spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
              </div>
            </div>
            <div class="ng-cloak" ng-if="!loading && (!items || items.length === 0)">
              <div class="p-t-50 p-b-50 text-center">
                <i class="fa fa-stack fa-3x">
                  <i class="fa fa-shopping-cart fa-stack-1x"></i>
                  <i class="fa fa-ban fa-stack-2x"></i>
                </i>
                <h4>{t}There are no purchases for now.{/t}</h4>
                <h5>{t escape=off}Check our <a class="bold" href="#">store</a> and improve your newspaper.{/t}</h4>
              </div>
            </div>
            <div class="table-wrapper ng-cloak" ng-if="items && items.length > 0">
              <table class="table">
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
              <div class="text-center" ng-if="items.length > 0">
                <a class="bold text-uppercase" href="{url name=backend_purchases_list}">{t}More{/t}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-xs-12 m-b-15">
        <input name="hasChanges" ng-value="hasChanges" type="hidden">
        <input name="modules" ng-value="activatedModules" type="hidden">
      </div>
      <div class="col-sm-6 col-xs-12 m-b-15">
      </div>
    </div>
  </div>
{/block}

