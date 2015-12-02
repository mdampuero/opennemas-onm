{extends file="base/admin.tpl"}

{block name="header-css" append}
{stylesheets src="
    @AdminTheme/less/_domain.less"
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
      <div class="m-b-30 m-t-15">
        <div class="center">
          <h4>{t}How can I change 'opennemas.com' domain on my newspaper?{/t}</h4>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
      <div class="grid simple">
        <div class="grid-title">
          <div class="row">
            <div class="col-sm-6">
              <a class="btn btn-block btn-white" href="{url name=admin_domain_management_add}">
                <i class="block fa fa-retweet fa-2x m-b-15"></i>
                <h4 class="block uppercase">{t}Redirect your domain{/t}</h4>
                <h5 class="wrap">
                  {t}I have an existing domain and I want to redirect it to my Opennemas digital newspaper.{/t}
                </h5>
              </a>
            </div>
            <div class="col-sm-6">
              <a class="btn btn-block btn-success" href="{url name=admin_domain_management_add create=1}">
                <i class="block fa fa-plus fa-2x m-b-15"></i>
                <h4 class="block uppercase text-white">{t}Add new domain{/t}</h4>
                <h5 class="text-white wrap">
                  {t}I DO NOT have my own domain and I want to create one and redirect it to my Opennemas digital newspaper{/t}
                </h5>
              </a>
            </div>
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
                <div ng-if="!domain.loading">
                  <div class="row">
                    <div class="col-sm-6">
                      <strong>{t}Points to{/t}:</strong> [% domain.target %]
                    </div>
                    {*<div class="col-sm-6">
                      <strong>{t}Expires{/t}:</strong> [% domain.expires %]
                    </div>*}
                    <div class="col-sm-6">
                      <a href="#" ng-click="showDnsModal(domain)"><span class="fa fa-cog"></span> How to configure your DNS</a>
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
</div>
<script type="text/ng-template" id="modal-dns-changes">
  {include file="domain_management/modal/_dns_changes.tpl"}
</script>
{/block}
