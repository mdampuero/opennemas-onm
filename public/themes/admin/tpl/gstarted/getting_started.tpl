{extends file="base/admin.tpl"}

{block name="body"}
<div class="wizard-wrapper clearfix" ng-class="{ 'active': previous }" ng-controller="GettingStartedCtrl">
    <div class="wizard-container welcome" ng-class="{ 'active': step == 1, 'previous': previous == 1 }">
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
    <div class="wizard-container terms-and-conditions" ng-class="{ 'active': step == 2, 'previous': previous == 2 }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <p>
          {t}In order to use Opennemas you must accept the Legal notice & Terms of use{/t}
        </p>
        <div class="terms-wrapper">
          <div class="terms-container">
            <iframe class="terms-of-use" src="/terms_of_use.html" frameborder="0"></iframe>
          </div>
          <div class="checkbox">
            <input name="accept-terms" id="accept-terms" ng-click="acceptTerms()" ng-model="termsAccepted" ng-value="termsAccepted" type="checkbox">
            <label for="accept-terms">
              {t}Accept the Legal notice & Terms of use{/t}
            </label>
            <div class="arrow hidden-sm hidden-xs" ng-class="{ 'warning': warning && !termsAccepted }"></div>
          </div>
        </div>
        <div class="wizard-button">
          <button class="btn btn-block btn-success" ng-click="goToStep(3)" ng-disabled="!termsAccepted">
            <h4>{t}Next{/t}</h4>
          </button>
        </div>
      </div>
    </div>
    <div class="wizard-container help" ng-class="{ 'active': step == 3, 'previous': previous == 3 }">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <div class="wizard-title">
          <h1>{t}Do you need some help?{/t}</h1>
        </div>
        <p>
          {t}You can read and learn how to use your Opennemas by using our online documentation and videos.
          Take a look around and you will find it.{/t}
        </p>
        <ul class="wizard-list clearfix">
          <li class="wizard-list-item">
            <div class="wizard-list-item-icon">
              <i class="fa fa-support fa-3x"></i>
            </div>
            <div class="wizard-list-item-text">
              {t escape=off}Our <a href="http://help.opennemas.com/" target="_blank">knownledge base</a> has manuals and howtos about how to create contents and improve your newspaper.{/t}
            </div>
          </li>
          <li class="wizard-list-item">
            <div class="wizard-list-item-icon">
              <i class="fa fa-youtube fa-3x"></i>
            </div>
            <div class="wizard-list-item-text">
              {t escape=off}See our <a href="http://www.youtube.com/user/OpennemasPublishing" target="_blank">video tutorials</a> for getting step-by-step guidance.{/t}
            </div>
          </li>
          <li class="wizard-list-item">
            <div class="wizard-list-item-icon">
              <i class="fa fa-question fa-3x"></i>
            </div>
            <div class="wizard-list-item-text">
              {t escape=off}If you need further information you can always contact us by using the <span class="fa fa-support"></span> Help button in the upper right corner.{/t}
            </div>
          </li>
        </ul>
        <div class="wizard-button">
          <button class="btn btn-block btn-success" ng-click="goToStep(4)">
            <h4>{t}Next{/t}</h4>
          </button>
        </div>
      </div>
    </div>
    {if $app.security->hasPermission('ADMIN')}
      <div class="wizard-container store" ng-class="{ 'active': step == 4, 'previous': previous == 4 }">
        <div class="wizard-overlay"></div>
        <div class="wizard-content">
          <div class="wizard-title">
            <h1>{t}Opennemas Store{/t}</h1>
          </div>
          <p>{t}Find and try new add-ons and take your newspaper to the next level.{/t}</p>
          <ul class="wizard-list clearfix">
            <li class="wizard-list-item">
              <div class="wizard-list-item-icon">
                <i class="fa fa-dropbox fa-3x"></i>
              </div>
              <div class="wizard-list-item-text">
                {t escape=off}Do you need more? Check our packs and power your newspaper with extra features to make it the best.{/t}
              </div>
            </li>
            <li class="wizard-list-item">
              <div class="wizard-list-item-icon">
                <i class="fa fa-cube fa-3x"></i>
              </div>
              <div class="wizard-list-item-text">
                {t escape=off}We offer a huge list of add-ons to improve your experience. Check our store and pay only for what you really want.{/t}
              </div>
            </li>
            <li class="wizard-list-item">
              <div class="wizard-list-item-icon">
                <i class="fa fa-eye fa-3x"></i>
              </div>
              <div class="wizard-list-item-text">
                {t escape=off}Make your own style. Choose a template or request a exclusive design to make your newspaper look awesome.{/t}
              </div>
            </li>
          </ul>
          <div class="wizard-button">
            <button class="btn btn-block btn-success" ng-click="goToStep(5)">
              <h4>{t}Next{/t}</h4>
            </button>
          </div>
        </div>
      </div>
    {/if}
    <div class="wizard-container ready" ng-class="{ 'active': step == {if $app.security->hasPermission('ADMIN')}5{else}4{/if},  'previous': previous == {if $app.security->hasPermission('ADMIN')}5{else}4{/if}}">
      <div class="wizard-overlay"></div>
      <div class="wizard-content">
        <div class="wizard-title">
          <h1>{t}That's it!{/t}</h1>
        </div>
        {if $user->getOrigin() !== 'manager'}
          <h4>{t}Wait...Do you have a Facebook or a Twitter account?{/t}</h4>
          <p>
            {t}Then you can associate those accounts to access your opennemas. It will make easier to get into your administration panel.{/t}
          </p>
          <div class="social-items-wrapper clearfix">
            <div class="social-item">
              <iframe src="{url name=backend_user_social id=$user->id resource='facebook' style='orb'}" allowtransparency="true" frameborder="0"></iframe>
            </div>
            <div class="social-item">
              <iframe src="{url name=backend_user_social id=$user->id resource='twitter' style='orb'}" allowtransparency="true" frameborder="0"></iframe>
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
      <ul class="wizard-step-list">
        <li class="wizard-step-list-item" ng-class="{ 'active': !step || step >= 1 }" ng-click="goToStep(1)">
          <div class="wizard-step-list-item-fill">
            <h5 class="wizard-step-list-item-text top">{t}Welcome!{/t}</h5>
          </div>
        </li>
        <li class="wizard-step-list-item"  ng-class="{ 'active': step > 1 }" ng-click="goToStep(2)">
          <div class="wizard-step-list-item-fill">
            <h5 class="wizard-step-list-item-text bottom">{t}Terms & conditions{/t}</h5>
          </div>
        </li>
        <li class="wizard-step-list-item"  ng-class="{ 'active': step > 2 }" ng-click="goToStep(3)" ng-disabled="!termsAccepted">
          <div class="wizard-step-list-item-fill">
            <h5 class="wizard-step-list-item-text top">{t}Help{/t}</h5>
          </div>
        </li>
        {if $app.security->hasPermission('ADMIN')}
          <li class="wizard-step-list-item" ng-class="{ 'active': step > 3 }" ng-click="goToStep(4)" ng-disabled="!termsAccepted">
            <div class="wizard-step-list-item-fill">
              <h5 class="wizard-step-list-item-text bottom">{t}Opennemas Store{/t}</h5>
            </div>
          </li>
        {/if}
        <li class="wizard-step-list-item"  ng-class="{ 'active': step > {if $app.security->hasPermission('ADMIN')}4{else}3{/if} }" ng-click="goToStep({if $app.security->hasPermission('ADMIN')}5{else}4{/if})" ng-disabled="!termsAccepted">
          <div class="wizard-step-list-item-fill">
            <h5 class="wizard-step-list-item-text top">{t}Ready!{/t}</h5>
          </div>
        </li>
      </li>
    </div>
  </div>
{/block}
