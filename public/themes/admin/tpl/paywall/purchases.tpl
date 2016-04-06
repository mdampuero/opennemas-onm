{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_paywall_purchases}" method="get">
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
            <h5>{t}Purchases{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_paywall}" title="{t}Go back to list{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_paywall_purchases_list_export order=$smarty.request.order searchname=$smarty.request.searchname}">
                <span class="fa fa-download"></span>
                {t}Export list{/t}
              </a>
            </li>
          </ul>
        </div>
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
            <input class="no-boarder" name="searchname" value="{$smarty.request.searchname|default:""}" placeholder="{t}Filter by name or email{/t}" type="text"/>
          </li>

          <!--
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks">{t}Sort by{/t}</li>
          <li class="quicklinks">
            <div class="input-append">
                <select id="order" name="order" class="span2">
                    {assign var=order value=$smarty.request.order}
                    <option value="" {if $order eq ""}selected{/if}>{t}Payment date{/t}</option>
                    <option value="username" {if ($order eq "username")}selected{/if}>{t}User name{/t}</option>
                    <option value="name" {if ($order eq "name")}selected{/if}>{t}Full name{/t}</option>
                </select>
            </div>
          </li>
          -->
        </ul>
        <ul class="nav quick-section pull-right hidden-xs">
          <li class="quicklinks">
            <span class="info">
              {t 1=$pagination->_totalItems}%1 purchases{/t}
            </span>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks form-inline pagination-links">
            <div class="btn-group">
              <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                <i class="fa fa-chevron-left"></i>
              </button>
              <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                <i class="fa fa-chevron-right"></i>
              </button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content clearfix">


    <div class="grid simple">
      <div class="grid-body">
        {include file="paywall/partials/purchases_listing.tpl"}
      </div>
    </div>

  </div>
</form>
{/block}
