{extends file="base/admin.tpl"}

{block name="header-css" append}
<style>
    /*#add_payment_mode { margin-top:15px;}*/
    .payment_mode {
        margin-bottom:10px;
    }
    .settings-header {
        border-bottom:1px solid #eeeeee;
        margin-bottom:20px;
        padding-bottom:10px;
    }
    fieldset {
        margin-bottom:40px;
    }
    ol li {
        margin-bottom:5px;
    }
</style>
{/block}

{block name="content"}
<form action="{url name=admin_paywall_settings_save}" method="post" ng-controller="PaywallSettingsCtrl" ng-init="parseSettings({json_encode($settings)|clear_json})">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-paypal"></i>
              {t}Paywall{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Settings{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_paywall}" title="{t}Go back to list{/t}">
                <i class="fa fa-reply"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-primary" ng-disabled="!settings.terms" type="submit">
                <i class="fa fa-save"></i> {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-title">
        <h4><div class="step-number">1</div> {t}Paypal API authentication{/t}</h4>
        <div class="pull-right">
          <span class="fa fa-question-circle"></span>
          {t}Get this parameters from your {/t}
          <a href="#" ng-click="getIdentification()">
            {t}Paypal identification data{/t}
          </a>
        </div>
      </div>
      <div class="grid-body">
        <div class="row">
          <div class="col-md-7">
            <div class="form-group">
              <label class="form-label" for="paypal_username">{t}User name{/t}</label>
              <div class="controls">
                <input class="form-control" id="username" ng-model="settings.paypal_username" required type="text">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="paypal_password">{t}Password{/t}</label>
              <div class="controls">
                <input class="form-control" id="password" ng-model="settings.paypal_password" required type="text">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="paypal_signature">{t}Signature{/t}</label>
              <div class="controls">
                <input class="form-control" id="signature" ng-model="settings.paypal_signature" required type="text">
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="p-l-15">
              <div class="form-group">
                <label class="form-label">
                  {t}Use the testing environment Sandbox{/t}
                </label>
              </div>
              <div class="form-group">
                <div class="radio">
                  <input id="developer_mode_no" ng-model="settings.developer_mode" ng-value="0" type="radio">
                  <label for="developer_mode_no">
                    {t}Real mode (recommended){/t}
                  </label>
                </div>
              </div>
              <div class="form-group">
                <div class="radio">
                  <input id="developer_mode_yes" ng-model="settings.developer_mode" ng-value="1" type="radio">
                  <label for="developer_mode_yes">
                    {t}Testing mode{/t}
                  </label>
                </div>
              </div>
              <div class="help-block">
                <p>{t escape=off}Paypal allows you to enable a testing environment where <strong>all the transactions will not be real</strong>, so you can test if the paywall is working well.{/t}</p>
                {t}Active a testing environment in your Paypal account (only if you are a developer){/t} <a href="https://developer.paypal.com/">{t}More information{/t}</a>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            {t}Validate here your Paypal API credentials in the selected mode{/t}
            <button class="btn pull-right" ng-class="{ 'btn-danger': !settings.valid_credentials, 'btn-success': settings.valid_credentials }" ng-click="validateCredentials()" type="button">
              <i class="fa fa-circle-o-notch fa-spin" ng-if="validatingCredentials"></i>
              {t}Validate{/t}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="grid simple">
      <div class="grid-title">
        <h4><div class="step-number">2</div> {t}Currency & taxes{/t}</h4>
        </div>
      <div class="grid-body">
        <div class="row">
          <div class="col-sm-6">
            <div id="money" class="form-group">
              <label for="money_unit" class="form-label">{t}Money unit{/t}</label>
              <div class="controls">
                <select id="money_unit" ng-model="settings.money_unit">
                  {html_options options=$money_units selected=$settings['money_unit']}
                </select>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label for="vat_percentage" class="form-label">{t}VAT %{/t}</label>
              <div class="controls">
                <input min="0" ng-model="settings.vat_percentage" required type="number">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="grid simple">
      <div class="grid-title">
        <h4><div class="step-number">3</div> {t}Payment modes{/t}</h4>
      </div>
      <div class="grid-body">
        <p>{t}Below you can add different payment modes by including the time range that the user can purchase, the description and the price{/t}</p>
        <div class="form-group">
          <div class="controls">
            <div class="modes">
              <div class="m-b-5" ng-repeat="item in settings.payment_modes" ng-include="'payment-mode'"></div>
            </div>
            <button class="btn btn-default" ng-click="addPaymentMode()" type="button">
              <i class="fa fa-plus"></i>
              {t}Add new payment mode{/t}
            </a>
          </div>
        </div>
      </div>
    </div>
    {*<div class="grid simple">
      <div class="grid-title">
        <h4><div class="step-number">4</div> {t}Recurring payments (optional){/t}</h4>
      </div>
      <div class="grid-body">
        <p>
          {t}Paypal allow your users to subscribe to your Paywall through recurring payments. This means that your users will be charged periodically without having to worry about payments and due dates, and will allow you to increase the user engagement.{/t}
        </p>
        <div class="checkbox">
          <input id="recurring" ng-checked="settings.recurring" ng-model="settings.recurring" type="checkbox">
          <label for="recurring">
            {t}Mark this if you want to enable recurring payments{/t}
          </label>
        </div>
        {capture name=ipn}{setting name=valid_ipn}{/capture}
        <div class="p-t-15" ng-if="settings.recurring">
          <p>{t}You have to activate some options in the Paypal configuration to make recurring payments work. Please follow next steps:{/t}</p>
          <ol>
            <li>
              {t}Go to your merchant Paypal{/t}
              <a class="btn btn-mini" href="https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-ipn-notify" target="_blank">
                {t}IPN web configuration page {/t}
                <i class="icon icon-external-link"></i>
              </a>
              {t} and log in with your merchant account{/t}.</li>
            <li>
              {t}Click in the "Choose IPN configuration" button{/t}.
            </li>
            <li>
              {t}Fill in the "Notification URL" field with this address{/t}
              <input class="form-control" readonly="readonly" type="text" value="{url name='frontend_ws_paypal_ipn' absolute=true}">
            </li>
            <li>
              {t}Enable the "Receive IPN messages" checkbox{/t}.
            </li>
            <li>
              {t}Click on the validate button to check ipn is working fine and enable recurring payment{/t}.
              <button class="btn" ng-class="{ 'btn-danger': !settings.valid_ipn || settings.valid_ipn == 'invalid', 'btn-success': settings.valid_ipn == 'valid'}" ng-click="validateIpn()" type="button">
                <i class="fa fa-circle-o-notch fa-spin" ng-if="validatingIpn"></i>
                <span ng-if="!settings.valid_ipn || settings.valid_ipn == 'invalid'">{t}Validate{/t}</span>
                <span ng-if="settings.valid_ipn == 'waiting'">{t}Waiting{/t}</span>
                <span ng-if="settings.valid_ipn == 'valid'">{t}Valid{/t}</span>
              </button>
            </li>
            <li>
              {t}Finally, click in the "Save" button to save this configuration{/t}.
            </li>
          </ol>
        </div>
      </div>
    </div>*}
    <div class="grid simple">
      <div class="grid-title">
        <h4>
          <div class="step-number">4</div>
          {t}Accept Opennemas payment agreements terms{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div class="controls">
          <div class="checkbox">
            <input id="terms" ng-checked="settings.terms" ng-model="settings.terms" required type="checkbox">
            <label for="terms">
              {t escape=off}Read and accept the <a href="http://help.opennemas.com/" target="_blank">payment agreements terms</a> of Opennemas{/t}
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
  <input name="settings" ng-value="fsettings" type="hidden">
  <script type="text/ng-template" id="payment-mode">
    <div class="form-inline">
      <div class="form-group">
        <select ng-model="item.time">
          {html_options options=$times}
        </select>
      </div>
      <div class="form-group">
        <input type="text" ng-model="item.description" placeholder="{t}Description{/t}" required>
      </div>
      <div class="form-group">
        <div class="input-group">
          <input ng-model="item.price" min="0" placeholder="{t}Set a price{/t}" required type="number"/>
          <div class="input-group-addon">
            <i class="fa" ng-class="{ 'fa-euro': settings.money_unit == 'EUR', 'fa-dollar': settings.money_unit == 'USD'}"></i>
          </div>
        </div>
      </div>
      <div class="form-group">
        <button class="btn btn-danger" ng-click="removePaymentMode($index)" type="button">
          <i class="fa fa-trash"></i>
        </button>
      </div>
    </div>
  </script>
</form>
{/block}
