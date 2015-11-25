<div class="modal-header">
  <button aria-hidden="true" class="close" data-dismiss="modal" ng-click="close()" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="no-margin uppercase">[% template.item.name %]</h3>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-6">
      <carousel ng-if="template.item.screenshots.length > 0">
        <slide ng-repeat="screenshot in template.item.screenshots">
          <img class="img-responsive" ng-src="[% '/asset/thumbnail,1024,768' + template.item.path + '/' + screenshot %]">
        </slide>
      </carousel>
      <carousel ng-if="!template.item.screenshots">
        <slide>
          <img class="img-responsive" src="http://placehold.it/1024x768">
        </slide>
        <slide>
          <img class="img-responsive" src="http://placehold.it/1024x768">
        </slide>
        <slide>
          <img class="img-responsive" src="http://placehold.it/1024x768">
        </slide>
        <slide>
      </carousel>
    </div>
    <div class="col-md-6 m-t-15">
      <div class="clearfix">
        <button class="btn fly-to-cart m-b-15 pull-right" ng-class="{ 'btn-danger': template.isInCart(template.item), 'btn-success': !template.isInCart(template.item) }" ng-click="template.addToCart(template.item);$event.stopPropagation()" ng-disabled="template.isInCart(template.item)" ng-if="!template.isPurchased(template.item)" style="width: 100px;">
          <h5 class="text-white">
            <span class="m-r-15 semi-bold text-white uppercase" ng-if="!template.isInCart(template.item) && !template.isPurchased(template.item)">
              <i class="fa fa-shopping-cart m-r-5"></i>
              {t}Add{/t}
            </span>
            <span class="m-b-15 semi-bold text-white uppercase" ng-if="template.isInCart(template.item)">
              <i class="fa fa-shopping-cart m-r-5"></i>
              {t}In cart{/t}
            </span>
          </h5>
        </button>
        <button class="btn btn-info m-b-15 pull-right" ng-class="{ 'btn-info': template.isPurchased(template.item), 'btn-success': template.isActive(template.item) }" ng-click="template.enable(template.item)" ng-disabled="template.isActive(template.item)" ng-if="template.isPurchased(template.item)" style="width: 100px;">
          <h5 class="semi-bold text-white uppercase" ng-if="template.isPurchased(template.item) && !template.isActive(template.item) && !template.item.loading">{t}Enable{/t}</h5>
          <h5 class="semi-bold text-white uppercase" ng-if="template.isPurchased(template.item) && template.isActive(template.item)">{t}Enabled{/t}</h5>
          <h5 class="semi-bold text-white uppercase" ng-if="template.isPurchased(template.item) && !template.isActive(template.item) && template.item.loading">{t}Enabling{/t}...</h5>
        </button>
        <a class="btn btn-link m-b-15 pull-left" href="#" ng-click="$event.stopPropagation()" target="_blank">
          <h5 class="uppercase">
            <i class="fa fa-globe"></i>
            {t}Live demo{/t}
          </h5>
        </a>
      </div>
      <div class="row">
        <div class="col-xs-6">
          <div class="checkbox m-l-10 m-t-5">
            <input id="custom" ng-change="template.toggleCustom(template.item)" ng-disabled="template.isInCart(template.item) || template.isPurchased(template.item)" ng-model="template.item.customize" type="checkbox">
            <label for="custom">{t}Custom{/t}</label>
          </div>
        </div>
        <div class="col-xs-6">
          <h4 class="text-right" ng-if="!template.isPurchased(template.item)">
            <span ng-if="template.item.price.month">
              <strong>[% template.item.price.month %]</strong>
              <small>€ / {t}month{/t}</small>
            </span>
            <span ng-if="!template.item.price.month && template.item.price.single">
              <strong>[% template.item.price.single %]</strong>
              <small>€</small>
            </span>
            <span class="semi-bold uppercase" ng-if="!add && (!template.item.price || template.item.price.month == 0)">
              {t}Free{/t}
            </span>
          </h4>
        </div>
      </div>
      <div ng-if="!template.item.customize">
        {t escape=off}
          <ul class="alternate" type="square">
            <li>Newspaper Web Site with standard widgets developed by Opennemas team. No customization available.</li>
            <li>Personalization allowed in the platform: colour of menu and logo.</li>
            <li>Widgets: Standard widgets included. To add a widget please contact us at sales@openhost.es</li>
            <li>Exclusivity: This template is not exclusive</li>
            <li>Delivery time: 1 day after payment</li>
            <li>Change Request BEFORE launch: No Change included. For change request please check out Custom Option.</li>
            <li>Change Request AFTER launch: No Change included. For change request please check out Custom Option.</li>
            <li>Cost:
              <ul class="alternate" type="square">
                <li>350€* (one pay)</li>
                <li>35€* (12 months)<br>
                  *VAT not included</li>
              </ul>
            </li>
          </ul>
        {/t}
      </div>
      <div ng-if="template.item.customize">
        {t escape=off}
          <ul class="alternate" type="square">
            <li>Newspaper Web Site Template that can be customized to reflect better brand guidelines and customer preferences.</li>
            <li>Widgets: Standard widgets included. To add a widget please contact us at sales@openhost.es</li>
            <li>Exclusivity: This template is not exclusive</li>
            <li>Delivery time: From 2 weeks up to 1 month depending on customization work.</li>
            <li>Change Request BEFORE launch:
              <ul class="alternate" type="square">
                <li>Typography, newspaper colours and style.</li>
                <li>Changes NOT included: Widgets, Menus, Titles, Pretitle, Inner Article Disposition, Images Size, Headers and footers.</li>
                <li>1 iteration of feedback and change request included before production.</li>
              </ul>
            </li>
            <li>Change Request AFTER launch: Monitoring and Bug fixing (if any) included 30 days post production.</li>
            <li>Steps to activate the template:
              <ul class="alternate" type="square">
                <li>Order it.</li>
                <li>We will contact you for gathering of requirements and purchase process.</li>
                <li>In 2 to 4 weeks after first payment your template will be active</li>
                <li>If any bug fixing is required we will take care of it, as in the first 30 days we will be monitoring your newspaper</li>
                <li>Add On:
                  <ul class="alternate" type="square">
                    <li>New widgets: 120€* each</li>
                    <li>Get newspaper one week in advance: 500€*</li>
                    <li>Support cost after launch</li>
                  </ul>
                </li>
              </ul>
            </li>
            <li>Cost:
              <ul class="alternate" type="square">
                <li>1450€ (one pay)*</li>
                <li>135€ (12 months)*<br>
                  *VAT not included</li>
              </ul>
            </li>
          </ul>
        {/t}
      </div>
    </div>
  </div>
</div>
