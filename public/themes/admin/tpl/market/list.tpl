{extends file="base/admin.tpl"}

{block name="content"}
<div ng-controller="MarketListCtrl">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-shopping-cart"></i>
              {t}Market{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks shopping-cart dropdown">
              <div class="p-10" data-toggle="dropdown">
                <i class="fa fa-shopping-cart"></i>
                <span class="p-l-5 p-r-5">
                  {t}Cart{/t}
                </span>
                <i class="fa fa-caret-down"></i>
              </div>
              <div class="dropdown-menu pull-right">
                <div class="shopping-cart-placeholder">
                  <scrollable>
                    <ul>
                      <li class="clearfix" ng-repeat="item in cart">
                        <a href="#">[% item.name %]</a>
                        <button class="btn btn-white pull-right" ng-click="removeFromCart(item, $event)">
                          <i class="fa fa-times fa-lg text-danger"></i>
                        </button>
                      </li>
                    </ul>
                  </scrollable>
                </div>
                <div class="p-r-10 p-t-15">
                  <button class="btn btn-block btn-white">
                    <i class="fa fa-shopping-cart"></i>
                    {t}Checkout{/t}
                  </button>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" tooltip="Clear selection" tooltip-placement="right"type="button">
              <i class="fa fa-arrow-left fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <h4>
              [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
            </h4>
          </li>
        </ul>
        <ul class="nav quick-section pull-right">
          {acl isAllowed="ADVERTISEMENT_DELETE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="removeSelectedMenus()" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-trash-o fa-lg"></i>
            </button>
          </li>
          {/acl}
        </ul>
      </div>
    </div>
  </div>
  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="name" ng-model="criteria.name" placeholder="{t}Search by name{/t}" type="text"/>
          </li>
          <li class="quicklinks"><span class="h-seperate"></span></li>
          {*<li class="quicklinks hidden-xs ng-cloak">
            <ui-select name="view" theme="select2" ng-model="pagination.epp">
              <ui-select-match>
                <strong>{t}View{/t}:</strong> [% $select.selected %]
              </ui-select-match>
              <ui-select-choices repeat="item in views  | filter: $select.search">
                <div ng-bind-html="item | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>*}
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    {render_messages}
    <div class="spinner-wrapper" ng-if="loading">
      <div class="loading-spinner"></div>
      <div class="spinner-text">{t}Loading{/t}...</div>
    </div>
    <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
      <div class="center">
        <h4>{t}Unable to find any menu that matches your search.{/t}</h4>
        <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
      </div>
    </div>
    <div class="infinite-row clearfix">
      <div class="col-lg-4 col-sm-6" ng-repeat="content in contents | filter:criteria">
        <div class="grid simple">
          <div class="grid-body">
            <div class="row">
              <div class="col-sm-4">
                <img class="img-responsive" src="http://placehold.it/300x300" alt="">
              </div>
              <div class="col-sm-8">
                <h4>[% content.name %]</h4>
                <p class="p-b-15 hidden-md">
                  [% content.description | limitTo: 140 %]
                  [% content.description.length > 140 ? '...' : '' %]
                </p>
                <div class="text-right">
                  <button class="btn btn-white" ng-click="addToCart(content)" ng-disabled="isInCart(content)">
                    <i class="fa fa-plus"></i>
                    {t}Add to cart{/t}
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="grid-footer">
            <div class="row">
              <div class="col-sm-4">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star-o"></i>
              </div>
              <div class="col-sm-8 text-right">
                {t}Updated{/t}: [% content.last_updated | moment %]
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-remove-permanently">
    {include file="common/modals/_modalDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-batch-remove-permanently">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
</div>
{/block}
