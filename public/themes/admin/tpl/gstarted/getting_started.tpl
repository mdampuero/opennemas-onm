{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="@AdminTheme/less/_wizard.less" filters="cssrewrite,less"}
    <link rel="stylesheet" href="{$asset_url}">
  {/stylesheets}
{/block}

{block name="body"}
  <div class="wizard-wrapper clearfix" ng-controller="GettingStartedCtrl">
    <div class="wizard-container welcome active" ng-class="{ 'active': !step || step == 1 }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <div class="wizard-title">
          <h1>{t}Welcome to Opennemas{/t}</h1>
        </div>
        <p>
          {t}Now you will be able to publish your own news, articles and take part of the information around everyone.{/t}
        </p>
        <p>
          {t}Before starting to work on it you have to perform some tasks, sush as setup your social networks and get some information about how to use Opennemas{/t}
        </p>
        <div class="wizard-button">
          <button class="btn btn-block btn-success" ng-click="goToStep(2)">
            <h4>{t}Next{/t}</h4>
          </button>
        </div>
      </div>
    </div>
    <div class="wizard-container terms-and-conditions" ng-class="{ 'active': step == 2 }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <p>
          {t}In order to use Opennemas you must accept the terms of use{/t}
        </p>
        <div class="terms-wrapper">
          <div class="terms-container">
            <iframe class="terms-of-use" src="/terms_of_use.html" frameborder="0"></iframe>
          </div>
          <div class="checkbox">
            <input name="accept-terms" id="accept-terms" ng-click="acceptTerms()" ng-model="termsAccepted" ng-value="termsAccepted" type="checkbox">
            <label for="accept-terms">
              {t}Accept the terms of use{/t}
            </label>
            <div class="arrow"></div>
          </div>
        </div>
        <div class="wizard-button">
          <button class="btn btn-block btn-success" ng-click="goToStep(3)" ng-disabled="!termsAccepted">
            <h4>{t}Next{/t}</h4>
          </button>
        </div>
      </div>
    </div>
    <div class="wizard-container help" ng-class="{ 'active': step == 3 }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <div class="wizard-title">
          <h1>{t}Do you need some help?{/t}</h1>
        </div>
        <p>
          {t}You can read and learn how to use your Opennemas by using our online documentation and videos.
          Take a look around and you will find it.{/t}
        </p>
        <div class="help-items-wrapper clearfix">
          <div class="help-item">
            <div class="orb">
              <i class="fa fa-support fa-3x"></i>
            </div>
            <div class="item-text">
              {t escape=off}Our <a href="http://help.opennemas.com/" target="_blank">knownledge base</a> has manuals and howtos about how to create contents and improve your newspaper.{/t}
            </div>
          </div>
          <div class="help-item">
            <div class="orb">
              <i class="fa fa-youtube fa-3x"></i>
            </div>
            <div class="item-text">
              {t escape=off}See our <a href="http://www.youtube.com/user/OpennemasPublishing" target="_blank">video tutorials</a> for getting step-by-step guidance.{/t}
            </div>
          </div>
          <div class="help-item">
            <div class="orb">
              <i class="fa fa-question fa-3x"></i>
            </div>
            <div class="item-text">
              {t escape=off}If you need further information you can always contact us by using the <span class="fa fa-support"></span> Help button in the upper right corner.{/t}
            </div>
          </div>
        </div>
        <div class="wizard-button">
          <button class="btn btn-block btn-success" ng-click="goToStep(4)">
            <h4>{t}Next{/t}</h4>
          </button>
        </div>
      </div>
    </div>
    {if $smarty.session._sf2_attributes.user->isAdmin()}
      <div class="wizard-container payment-info" ng-class="{ 'active': step == 4 }">
        <div class="wizard-overlay"></div>
        <div class="wizard-content">
          <div class="wizard-title">
            <h1>{t}Do you want to use our market?{/t}</h1>
          </div>
          <p>{t}Then you can add your payment information now. You can add it later too during the checkout proccess.{/t}</p>
          <div class="grid-wrapper">
            <div class="grid simple">
              <div class="grid-body">
                <div class="row">
                                  </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="contact-name">{t}Contact name{/t}</label>
                      <input class="form-control" id="contact-name" ng-model="billing.contact_name" ng-init="billing.contact_name='{if !empty($billing) && !empty($billin.contact_name)}{$billing.contact_name}{else}{$smarty.session._sf2_attributes.user->name}{/if}'" type="text">
                    </div>
                    <div class="form-group">
                      <label for="contact-email">{t}Email{/t}</label>
                      <input class="form-control" id="contact-email" ng-model="billing.contact_email" ng-init="billing.contact_email='{if !empty($billing) && !empty($billing.contact_email)}{$billing.contact_email}{else}{$smarty.session._sf2_attributes.user->email}{/if}'" type="text">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group p-b-10">
                      <label>{t}Are you a company?{/t}</label>
                      <div class="checkbox">
                        <input id="company" ng-checked="billing.company === '1'" ng-false-value="'0'" {if !empty($billing)}ng-init="billing.company='{if $billing.company}1{else}0{/if}'"{/if} ng-model="billing.company" ng-true-value="'1'" type="checkbox"/>
                        <label for="company">{t}Yes, I am a company{/t}</label>
                      </div>
                    </div>
                    <div class="form-group" ng-show="billing.company === '1'">
                      <label for="company-name">{t}Company name{/t}</label>
                      <input class="form-control" id="company-name" {if !empty($billing)}ng-init="billing.company_name='{$billing.company_name}'"{/if} ng-model="billing.company_name" type="text">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-6">
                    <label for="nif">
                      <span ng-if="billing.type != 'company'">NIF</span>
                      <span ng-if="billing.type == 'company'">CIF</span>
                    </label>
                    <input class="form-control" id="nif" {if !empty($billing)}ng-init="billing.nif='{$billing.nif}'"{/if} ng-model="billing.nif" type="text">
                  </div>
                  <div class="form-group col-sm-6">
                    <label for="phone">{t}Phone number{/t}</label>
                    <input class="form-control" id="phone" {if !empty($billing)}ng-init="billing.phone='{$billing.phone}'"{/if} ng-model="billing.phone" type="text">
                  </div>
                </div>
                <div class="form-group no-margin">
                  <label for="address">{t}Address{/t}</label>
                  <input class="form-control" id="address" {if !empty($billing)}ng-init="billing.address='{$billing.address}'"{/if} ng-model="billing.address" type="text">
                </div>
              </div>
            </div>
          </div>
          <div class="wizard-button">
            <button class="btn btn-block btn-success" ng-click="savePaymentInfo();goToStep({if $smarty.session._sf2_attributes.user->isAdmin()}5{else}4{/if})">
              <h4>{t}Next{/t}</h4>
            </button>
          </div>
        </div>
      </div>
    {/if}
    <div class="wizard-container ready" ng-class="{ 'active': step == {if $smarty.session._sf2_attributes.user->isAdmin()}5{else}4{/if} }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <div class="wizard-title">
          <h1>{t}That's it!{/t}</h1>
        </div>
        {if !$smarty.session._sf2_attributes.user->isMaster()}
          <h4>{t}Wait...Do you have a Facebook or a Twitter account?{/t}</h4>
          <p>
            {t}Then you can associate those accounts to access your opennemas. It will make easier to get into your administration panel.{/t}
          </p>
          <div class="social-items-wrapper clearfix">
            <div class="social-item">
              <iframe src="{url name=admin_acl_user_social id=$user->id resource='facebook' style='orb'}" frameborder="0"></iframe>
            </div>
            <div class="social-item">
              <iframe src="{url name=admin_acl_user_social id=$user->id resource='twitter' style='orb'}" frameborder="0"></iframe>
            </div>
          </div>
        {/if}
        <h4>{t}Now, you can start to use your newspaper.{/t}</h4>
        <p>
          {t}Hope you will enjoy opennemas!{/t}
        </p>
        <div class="wizard-button">
          <a class="btn btn-block btn-success" href="{url name='admin_getting_started_finish'}">
            <h4>{t}Finish{/t}</h4>
          </a>
        </div>
      </div>
    </div>
    <div class="wizard-footer">
      <div class="wizard-footer-wrapper"{if !$smarty.session._sf2_attributes.user->isAdmin()} style="width: 780px;"{/if}>
        <button class="wizard-step" ng-class="{ 'active': !step || step >= 1 }" ng-click="goToStep(1)">
          <div class="wizard-orb">
            <h4>1</h4>
          </div>
          <h5>{t}Welcome!{/t}</h5>
        </button>
        <button class="wizard-step"  ng-class="{ 'active': step > 1 }" ng-click="goToStep(2)">
          <div class="wizard-orb">
            <h4>2</h4>
          </div>
          <h5>{t}Terms & conditions{/t}</h5>
        </button>
        <button class="wizard-step"  ng-class="{ 'active': step > 2 }" ng-click="goToStep(3)" ng-disabled="!termsAccepted">
          <div class="wizard-orb">
            <h4>3</h4>
          </div>
          <h5>{t}Getting help{/t}</h5>
        </button>
        {if $smarty.session._sf2_attributes.user->isAdmin()}
          <button class="wizard-step"  ng-class="{ 'active': step > 3 }" ng-click="goToStep(4)" ng-disabled="!termsAccepted">
            <div class="wizard-orb">
              <h4>4</h4>
            </div>
            <h5>{t}Payment info{/t}</h5>
          </button>
        {/if}
        <button class="wizard-step"  ng-class="{ 'active': step > {if $smarty.session._sf2_attributes.user->isAdmin()}4{else}3{/if} }" ng-click="goToStep({if $smarty.session._sf2_attributes.user->isAdmin()}5{else}4{/if})" ng-disabled="!termsAccepted">
          <div class="wizard-orb">
            <h4>{if $smarty.session._sf2_attributes.user->isAdmin()}5{else}4{/if}</h4>
          </div>
          <h5>{t}Ready!{/t}</h5>
        </button>
      </div>
    </div>
  </div>
{/block}
