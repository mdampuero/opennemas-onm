<div class="modal-header hidden-md hidden-lg">
  <button aria-hidden="true" class="close" data-dismiss="modal" ng-click="close()" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="no-margin uppercase">[% template.item.name %]</h3>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-8">
      <uib-carousel active="1" class="carousel-big" ng-if="template.item.images.length > 0">
        <uib-slide index="$index" ng-repeat="screenshot in template.item.images">
          <img class="img-responsive" ng-src="[% '/asset/thumbnail,1024,768' + template.item.path + '/' + screenshot %]">
        </uib-slide>
      </uib-carousel>
      <uib-carousel active="1" class="carousel-big" ng-if="!template.item.images">
        <uib-slide index="1">
          <img class="img-responsive" src="//placehold.it/1024x768">
        </uib-slide>
      </uib-carousel>
    </div>
    <div class="col-md-4">
      <div class="row hidden-xs hidden-sm">
        <div class="clearfix col-xs-12">
          <button aria-hidden="true" class="close pull-right" data-dismiss="modal" ng-click="close()" type="button">
            <i class="fa fa-times"></i>
          </button>
          <h3 class="no-margin uppercase pull-left">[% template.item.name %]</h3>
        </div>
        <div class="col-xs-12">
          <hr>
        </div>
      </div>
      <div class="clearfix">
        <button class="btn fly-to-cart m-b-15 pull-right" ng-class="{ 'btn-danger': template.isInCart(template.item), 'btn-success': !template.isInCart(template.item) }" ng-click="template.addToCart(template.item);$event.stopPropagation()" ng-disabled="template.isInCart(template.item)" ng-if="!template.isPurchased(template.item)">
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
        <button class="btn m-b-15 pull-right" ng-class="{ 'btn-white': template.isPurchased(template.item) && !template.isActive(template.item), 'btn-success': template.isActive(template.item) }" ng-click="template.enable(template.item)" ng-disabled="template.isActive(template.item)" ng-if="template.isPurchased(template.item)" style="width: 100px;">
          <h5 class="semi-bold uppercase" ng-if="template.isPurchased(template.item) && !template.isActive(template.item) && !template.item.loading">{t}Enable{/t}</h5>
          <h5 class="semi-bold text-white uppercase" ng-if="template.isPurchased(template.item) && template.isActive(template.item)">{t}Active{/t}</h5>
          <h5 class="semi-bold uppercase" ng-if="template.isPurchased(template.item) && !template.isActive(template.item) && template.item.loading">{t}Enabling{/t}...</h5>
        </button>
        <a class="btn btn-white m-b-15 pull-left" href="[% template.item.parameters.preview_url %]" ng-click="$event.stopPropagation()" ng-if="!template.isPurchased(template.item)" target="_blank">
          <h5 class="uppercase">
            <i class="fa fa-globe"></i>
            {t}Live demo{/t}
          </h5>
        </a>
      </div>
      <div class="row m-b-15 m-t-10">
        <div class="col-xs-6">
          <div class="checkbox p-t-7">
            <input id="custom" ng-change="template.toggleCustom(template.item)" ng-disabled="template.isInCart(template.item) || template.isPurchased(template.item)" ng-model="template.item.customize" type="checkbox">
            <label for="custom" style="font-size:1.3em">{t}Custom{/t}</label>
          </div>
        </div>
        <div class="col-xs-6">
          <h3 class="no-margin text-right" ng-if="!template.isPurchased(template.item)">
            <span ng-if="template.getPrice(template.item)">
              <strong>[% template.getPrice(template.item, template.item.priceType).value %]</strong>
              <small ng-if="['monthly', 'monthly_custom'].indexOf(template.getPrice(template.item, template.item.priceType).type) !== -1">€/{t}month{/t}</small>
              <small ng-if="['yearly', 'yearly_custom'].indexOf(template.getPrice(template.item, template.item.priceType).type) !== -1">€/{t}year{/t}</small>
              <small ng-if="['single', 'single_custom'].indexOf(template.getPrice(template.item, template.item.priceType).type) !== -1">€</small>
            </span>
            <span class="semi-bold uppercase" ng-if="!add && !template.getPrice(template.item)">
              {t}Free{/t}
            </span>
          </h3>
        </div>
      </div>
      <div class="description-wrapper">
        <div ng-if="!template.item.customize">
          {t escape=off}
            <h4>Newspaper Web Site with standard widgets developed by Opennemas team. No customization available.</h4>
            <p>Personalization allowed in the platform: colour of menu and logo.</p>
            <p><strong>Cost:</strong> Standard widgets included. To add a widget please contact us at <a href="mailto:sales@openhost.es">sales@openhost.es</a></p>
            <p><strong>Exclusivity:</strong> This template is not exclusive</p>
            <p><strong>Delivery time:</strong> 1 day after payment</p>
            <p>Change Request BEFORE launch: No Change included. For change request please check out Custom Option.</p>
            <p>Change Request AFTER launch: No Change included. For change request please check out Custom Option.</p>
            <p>Cost:</p>
            <ul class="alternate" type="square">
              <li>350€* (one pay)</li>
              <li>35€* (12 months)</li>
            </ul>
          {/t}
        </div>
        <div ng-if="template.item.customize">
          {t escape=off}
            <h4>Newspaper Web Site Template that can be customized to reflect better brand guidelines and customer preferences.</h4>
            <p><strong>Widgets:</strong> Standard widgets included. To add a widget please contact us at <a href="mailto:sales@openhost.es">sales@openhost.es</a></p>
            <p><strong>Exclusivity:</strong> This template is not exclusive</p>
            <p><strong>Delivery time:</strong> From 2 weeks up to 1 month depending on customization work.</p>
            <p>Change Request BEFORE launch:
            <ul class="alternate" type="square">
              <li>Typography, newspaper colours and style.</li>
              <li>Changes NOT included: Widgets, Menus, Titles, Pretitle, Inner Article Disposition, Images Size, Headers and footers.</li>
              <li>1 iteration of feedback and change request included before production.</li>
            </ul>
            </li>
            <p>Change Request AFTER launch: Monitoring and Bug fixing (if any) included 30 days post production.</li>
          <p>Steps to activate the template:
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
          <p>Cost:</p>
          <ul class="alternate" type="square">
            <li>1450€ (one pay)*</li>
            <li>135€ (12 months)*<br>
              *VAT not included</li>
          </ul>
        {/t}
        </div>
      </div>
    </div>
  </div>
</div>
