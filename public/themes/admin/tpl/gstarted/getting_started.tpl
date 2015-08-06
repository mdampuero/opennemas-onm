{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="@AdminTheme/less/_wizard.less" filters="cssrewrite,less"}
    <link rel="stylesheet" href="{$asset_url}">
  {/stylesheets}
  {if $master}
  <style>
    .wizard-step { width: 230px; }
  </style>
  {/if}
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
    {if $master}
    <div class="wizard-container ready" ng-class="{ 'active': step == 4 }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <div class="wizard-title">
          <h1>{t}That's it!{/t}</h1>
        </div>
        <h4>{t}You can start to use your newspaper.{/t}</h4>
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
    {else}
    <div class="wizard-container social-networks" ng-class="{ 'active': step == 4 }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <div class="wizard-title">
          <h1>{t}Do you have a Facebook or a Twitter account?{/t}</h1>
        </div>
        <p>{t}Then you can associate those accounts to access your opennemas. It will make easier to get into your administration panel.{/t}</p>
        <div class="social-items-wrapper clearfix">
          <div class="social-item">
            <iframe src="{url name=admin_acl_user_social id=$user->id resource='facebook' style='orb'}" frameborder="0"></iframe>
          </div>
          <div class="social-item">
            <iframe src="{url name=admin_acl_user_social id=$user->id resource='twitter' style='orb'}" frameborder="0"></iframe>
          </div>
        </div>
        <div class="wizard-button">
          <button class="btn btn-block btn-success" ng-click="goToStep(5)">
            <h4>{t}Next{/t}</h4>
          </button>
        </div>
      </div>
    </div>
    <div class="wizard-container ready" ng-class="{ 'active': step == 5 }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <div class="wizard-title">
          <h1>{t}That's it!{/t}</h1>
        </div>
        <h4>{t}You can start to use your newspaper.{/t}</h4>
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
    {/if}
    <div class="wizard-footer">
      <div class="wizard-footer-wrapper">
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
        {if $master}
        <button class="wizard-step"  ng-class="{ 'active': step > 3 }" ng-click="goToStep(4)" ng-disabled="!termsAccepted">
          <div class="wizard-orb">
            <h4>4</h4>
          </div>
          <h5>{t}Ready!{/t}</h5>
        </button>
        {else}
        <button class="wizard-step"  ng-class="{ 'active': step > 3 }" ng-click="goToStep(4)" ng-disabled="!termsAccepted">
          <div class="wizard-orb">
            <h4>4</h4>
          </div>
          <h5>{t}Social Network{/t}</h5>
        </button>
        <button class="wizard-step"  ng-class="{ 'active': step > 4 }" ng-click="goToStep(5)" ng-disabled="!termsAccepted">
          <div class="wizard-orb">
            <h4>5</h4>
          </div>
          <h5>{t}Ready!{/t}</h5>
        </button>
        {/if}
      </div>
    </div>
  </div>
{/block}
