{extends file="domain_management/list.tpl"}

{block name="content"}
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-indent fa-server fa-lg"></i>
              {t}Domain Mapping{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_domain_management}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content" ng-controller="DomainManagementCtrl">
    <div class="row">
      <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
        <div class="grid simple">
          <div class="grid-body clearfix">
            <div class="clearfix">
              <div class="pull-left">
                <h4>{t}Map this domain to use it as your site's address.{/t}</h4>
              </div>
              <div class="pull-right">
                <h4>12.00</h4> <span class="muted">€/{t}year{/t}</span>
              </div>
            </div>
            <div class="input-group pull-left" style="width:80%;">
              <span class="input-group-addon">www</span>
              <input class="form-control" ng-model="domain" placeholder="{t}Enter a domain{/t}" type="text">
              <span class="input-group-btn">
                <span class="arrow"></span>
                <button class="btn btn-success" ng-click="map()" ng-disabled="!isValid()">
                  {t}Map it{/t}
                </button>
              </span>
            </div>
            <div class="pull-left">
              <div class="sk-three-bounce sk-inline sk-small ng-cloak" ng-if="loading">
                <div class="sk-child sk-bounce1"></div>
                <div class="sk-child sk-bounce2"></div>
                <div class="sk-child sk-bounce3"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
        <div class="grid simple ng-cloak" ng-if="domains.length > 0">
          <div class="grid-body no-padding">
            <ul class="domain-list">
              <li class="domain-list-item" ng-repeat="domain in domains">
                {t}Domain mapping{/t}: [% domain %]
                <span class="pull-right">12 €/year</span>
              </li>
              <li class="domain-list-item text-right">
                <div class="p-b-10">
                  <strong>{t}Subtotal{/t}:</strong>
                  [% subtotal %] €/{t}year{/t}
                </div>
                <div class="p-b-10">
                  <strong>{t}VAT{/t}:</strong>
                  [% vat %] €/{t}year{/t}
                </div>
                <div>
                  <strong>{t}Total{/t}:</strong>
                  [% total %] €/{t}year{/t}
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
        <div class="grid simple">
          <div class="grid-body">
            write here instructions to map a domain and implement a checker that uses dig under the hood to know if xxxxxx
            domain is mapped to xxxxxx.opennemas.net.
            <br>
            After clicking Map it
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}
