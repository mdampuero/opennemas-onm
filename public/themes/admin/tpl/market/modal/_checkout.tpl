  <div class="modal-body" ng-class="{ 'p-r-3': step == 1 }">
    <h5 class="no-margin text-center uppercase" ng-if="step != 3">
     <strong ng-class="{ 'muted': step != 1 }">{t}Cart review{/t}</strong>
     <i class="fa fa-angle-right p-l-5 p-r-5"></i>
     <strong ng-class="{ 'muted': step != 2 }">{t}Checkout{/t}</strong>
     <i class="fa fa-angle-right p-l-5 p-r-5"></i>
     <strong ng-class="{ 'muted': step != 3 }">{t}Success{/t}</strong>
    </h5>
    <div ng-show="!step || step == 1">
      <div class="p-t-15" style="height: 360px;">
        <scrollable>
          <ul class="cart-list">
            <li class="clearfix" ng-repeat="item in template.cart">
              <img ng-if="item.type == 'module'" class="img-responsive pull-left" ng-src="/assets/images/market/generic-modules.{$smarty.const.DEPLOYED_AT}.jpg">
              <img ng-if="item.type == 'pack'" class="img-responsive pull-left" ng-src="/assets/images/market/generic-pack.{$smarty.const.DEPLOYED_AT}.jpg">
              <img ng-if="item.type == 'service'" class="img-responsive pull-left" ng-src="/assets/images/market/generic-service-support.{$smarty.const.DEPLOYED_AT}.jpg">
              <img ng-if="item.type == 'theme'" class="img-responsive pull-left" ng-src="/assets/images/market/generic-pack.{$smarty.const.DEPLOYED_AT}.jpg">
              <div class="p-l-100">
                <h5>[% item.name %]</h5>
                <p class="description">[% item.description %]</p>
                <div class="text-right p-r-15">
                  <div class="price">
                    <h3 class="no-margin">
                      <strong>[% item.price.month %]</strong><small>€ / {t}month{/t}</small>
                    </h3>
                  </div>
                </div>
              </div>
              <i class="fa fa-times pull-left" ng-click="removeFromCart(item, $event)"></i>
            </li>
          </ul>
        </scrollable>
      </div>
      <hr class="m-r-15">
      <div class="p-r-15 text-right">
        <h3 class="no-margin">
          <span class="p-r-15 uppercase">{t}Total{/t}:</span>
          <strong>[% template.total %]</strong><small>€ / {t}month{/t}</small>
        </h3>
      </div>
    </div>
    <div class="p-t-30 p-b-30" ng-if="step == 2">
      <p class="p-b-15">{t}To confirm your purchase press the button below.{/t}</p>
      <p class="p-b-15">{t}Our sales department will receive an email with the new features you want to include in your newspaper.{/t}</p>
      <p>{t}You will receive an email with the list of features you are purchasing.{/t}</p>
    </div>
    <div class="text-center" ng-if="step == 3">
      <div class="p-b-30">
        <button aria-hidden="true" class="close" data-dismiss="modal" ng-click="close()" type="button">
          <i class="fa fa-times"></i>
        </button>
      </div>
      <i class="fa fa-heart fa-3x"></i>
      <h3 class="p-b-30">{t}Nice{/t}!</h3>
      <p class="p-b-15">
        {t}Thank you! We have received your request and will get back to you as soon as possible. You will receive a confirmation e-mail too.{/t}
      </p>
      <p class="p-b-30">
        {t escape=off}Meanwhile, you can go to your <a href="{url name='admin_client_info_page'}">My newspaper</a> and check your active features, navigate our help to familiarize with your news tool, or check some awesome videos to see how easy are to manage Opennemas.{/t}
      </p>
      <p class="p-b-15">
        {t}
          Oh!, it would be a good time to share with your friends your newspaper's improvements
        {/t}
      </p>
      <div>
        <a href="http://www.facebook.com" target="_blank">
          <i class="fa fa-lg fa-facebook m-r-30"></i>
        </a>
        <a href="http://twitter.com" target="_blank">
          <i class="fa fa-lg fa-twitter m-r-30"></i>
        </a>
        <a href="https://plus.google.com/" target="_blank">
          <i class="fa fa-lg fa-google-plus m-r-30"></i>
        </a>
        <a href="https://www.linkedin.com/" target="_blank">
          <i class="fa fa-lg fa-linkedin"></i>
        </a>
      </div>
    </div>
  </div>
  <div class="modal-footer" ng-if="step != 3">
    <button class="btn btn-default uppercase pull-left" ng-click="dismiss()" type="button">{t}Save for later{/t}</button>
    <button class="btn btn-success uppercase" ng-click="next()" ng-if="step == 1" type="button">
      {t}Next{/t}
    </button>
    <button class="btn btn-success uppercase" ng-click="confirm()" ng-if="step  == 2" type="button">
      <i class="fa fa-circle-o-notch fa-spin" ng-show="saving"></i>
      {t}Confirm{/t}
    </button>
