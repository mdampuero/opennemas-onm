  <div class="modal-body p-r-3">
    <h4 class="no-margin text-center uppercase">
      {t}Items in your cart{/t}
    </h4>
    <div class="p-t-15" style="height: 400px;">
      <scrollable>
        <ul class="cart-list">
          <li class="clearfix" ng-repeat="item in template.cart">
            <img class="img-responsive pull-left" src="http://placehold.it/300x300">
            <div class="p-l-100">
              <h5>[% item.name %]</h5>
              <p class="description">[% item.description %]</p>
              <div class="text-right p-r-15">
                <div class="price">
                  <h3 class="no-margin">
                    <strong>35</strong>
                    <small>€/{t}month{/t}</small>
                  </h3>
                </div>
              </div>
            </div>
            <i class="fa fa-times pull-left" ng-click="removeFromCart(item, $event)"></i>
          </li>
        </ul>
      </scrollable>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-default uppercase" ng-click="close()" type="button">{t}Save for later{/t}</button>
    <button class="btn btn-success uppercase" ng-click="confirm()" type="button">
      <i class="fa fa-circle-o-notch fa-3x" ng-show="saving"></i>
      {t}Checkout{/t}
    </button>
