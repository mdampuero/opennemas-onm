<div class="modal-header">
  <button aria-hidden="true" class="close" data-dismiss="modal" ng-click="close()" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h4 class="modal-title">{t}Payment{/t}</h4>
</div>
<div class="modal-body no-padding">
  <uib-tabset active="active" justified="true">
    <uib-tab index="0" heading="{t}First payment{/t}">
      <p class="m-b-15">{t}When you want to pay you can use your paypal account or your credit card.{/t}</p>
      <h4 class="semi-bold">{t}Pay with Paypal{/t}</h4>
      <ol class="m-b-30">
        <li>
          {t}To pay with your paypal account click on the Paypal button.{/t}
          <div><img src="/themes/admin/images/help/braintree_2.png" alt="Paypal"></div>
        </li>
        <li>
          {t}A popup window will be opened in order to log in with your paypal account.{/t}
        </li>
        <li>
          {t}After logging in with your paypal account the popup window will automatically close and your account will appear as your selected payment method.{/t}
          <div class="m-b-15 m-t-5"><img class="img-responsive" src="/themes/admin/images/help/braintree_4.png" alt="Paypal"></div>
        </li>
        <li>
          {t}Click 'Next' button to continue with your purchase.{/t}
        </li>
      </ol>
      <h4 class="semi-bold">{t}Pay with Credit Card{/t}</h4>
      <ol>
        <li>
          {t}To pay with your credit card fill the form with your credit card information.{/t}
          <div class="m-b-15 m-t-5"><img class="img-responsive" src="/themes/admin/images/help/braintree_3.png" alt="Paypal"></div>
        </li>
        <li>
          {t}While typing your credit card information will be automatically validated.{/t}
          {t}If one or more fields are incorrect you will not be able to continue with the purchase.{/t}
        </li>
        <li>
          {t}Click 'Next' button to select the credit card as your payment method and continue with the purchase.{/t}
          <div class="m-t-5"><img class="img-responsive" src="/themes/admin/images/help/braintree_5.png" alt="Paypal"></div>
        </li>
      </ol>
    </uib-tab>
    <uib-tab index="1" heading="{t}Not your first payment{/t}">
      <p class="m-b-15">{t}If this is not your first purchase, the last used payment method will be automatically selected.{/t}</p>
      <p class="m-b-15">{t}You can change your payment method following the steps below.{/t}</p>
      <p class="m-b-15">{t escape=off}If you want to delete a payment method, please <a href="mailto:sales@openhost.es">contact us</a>.{/t}</p>
      <h4 class="semi-bold">{t}Change your payment method{/t}</h4>
      <ol>
        <li>
          {t}Click on 'Change payment method'.{/t}
          <div class="m-t-5"><img class="img-responsive" src="/themes/admin/images/help/braintree_5.png" alt="Paypal"></div>
        </li>
        <li>
          {t}A list with all payment method used will appear.{/t}
          <div class="m-b-15 m-t-5"><img class="img-responsive" src="/themes/admin/images/help/braintree_6.png" alt="Paypal"></div>
        </li>
        <li>
          {t}Select one of the listed payment methods or add a new payment method.{/t}
          <div class="m-b-15 m-t-5"><img class="img-responsive" src="/themes/admin/images/help/braintree_4.png" alt="Paypal"></div>
        </li>
        <li>
          {t}Click 'Next' button to continue with your purchase.{/t}
        </li>
      </ol>
    </uib-tab>
  </uib-tabset>
</div>
