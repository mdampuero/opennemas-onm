{extends file="base/admin.tpl"}

{block name="header-css" append}
{stylesheets src="
@AdminTheme/less/_domain-management.less"
filters="cssrewrite,less"}
<link rel="stylesheet" type="text/css" href="{$asset_url}">
{/stylesheets}
{/block}

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
    </div>
  </div>
</div>
<div class="content" ng-controller="DomainManagementCtrl" ng-init="list()">
  <div class="row">
    <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
      <div class="single-colored-widget m-b-15">
        <div class="content-wrapper blue p-r-30">
          <h3 class="text-white">Custom <span class="semi-bold">domains</span></h3>
          <p class="text-white">{t escape=off}Use your own domains with Opennemas... If you have your own domain or even if you don't have one we will guide you through the process of using a custom address with your opennemas newspaper{/t}</p>
          <div class="pull-right icon"> <i class="icon-repeat fa-5x custom-icon-space fa fa-server" id="icon-rotate"></i> </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
      <div class="grid simple">
        <div class="grid-title clearfix">
          <div class="pull-left">
            <h4>{t}Domains{/t}</h4>
            <p>{t}Add more domains or manage your current address{/t}</p>
          </div>
          <div class="pull-right">
            <a href="{url name="admin_domain_management_add"}" data-toggle="modal" class="btn btn-primary add-domain-button">
              <i class="fa fa-plus"></i>
              {t}Add new domain{/t}
            </a>
          </div>
        </div>
        <div class="grid-body no-padding">
          <div class="spinner-wrapper" ng-if="loading">
            <div class="loading-spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
          </div>
          <ul class="domain-list ng-cloak" ng-if="!loading">
            <li class="domain-list-item pointer" ng-repeat="domain in domains" ng-click="expand($index)">
              <div class="clearfix">
                <h4 class="m-r-10 pull-left">[% domain.name %]</h4>
                <span class="label label-success pull-left uppercase" ng-if="domain.free">
                  {t}Main{/t}
                </span>
                <span class="label label-info pull-left uppercase" ng-if="domain.main">
                  {t}Free{/t}
                </span>
                <span class="p-t-15 pull-right">
                  <i class="fa fa-chevron-right fa-lg" ng-class="{ 'fa-chevron-right': !expanded[$index], 'fa-chevron-down': expanded[$index] }"></i>
                </span>
              </div>
              <div ng-if="expanded[$index]">
                <div class="sk-three-bounce" ng-if="domain.loading">
                  <div class="sk-child sk-bounce1"></div>
                  <div class="sk-child sk-bounce2"></div>
                  <div class="sk-child sk-bounce3"></div>
                </div>
                <div class="p-l-30" ng-if="!domain.loading">
                  <div class="row">
                    <div class="col-sm-6">
                      <strong>{t}Points to{/t}:</strong> [% domain.target %]
                    </div>
                    <div class="col-sm-6">
                      <strong>{t}Expires{/t}:</strong> [% domain.expires %]
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
      <div class="alert alert-block alert-warning fade in">
        <button type="button" class="close" data-dismiss="alert"></button>
        <h4 class="alert-heading"><i class="icon-warning-sign"></i> Developer note</h4>
        <p> Maybe we could show this module to everyone even if they don't have the module activated and if they want to add a mapping move the user to the payment procedure to activate the module </p>
      </div>
    </div>
  </div>
</div>
{/block}
