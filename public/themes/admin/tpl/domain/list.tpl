{extends file="base/admin.tpl"}

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
<div class="content" ng-controller="DomainListCtrl" ng-init="list()">
  <div class="row">
    <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
      <div class="m-b-30 m-t-15">
        <div class="center">
          <h3 class="semi-bold">{t}How can I change 'opennemas.com' domain on my newspaper?{/t}</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-vlg-6 col-vlg-offset-3 col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
      <div class="row p-b-20">
        <div class="col-sm-6">
          <a class="btn btn-block btn-white" href="{url name=backend_domain_add}" id="add-redirection-button">
            <i class="block fa fa-retweet fa-2x m-b-15"></i>
            <h4 class="block uppercase">{t}Redirect your own domain{/t}</h4>
            <h5 class="wrap">
              {t}I have an existing domain and I want to redirect it to my Opennemas digital newspaper.{/t}
            </h5>
          </a>
        </div>
        <div class="col-sm-6">
          <a class="btn btn-block btn-white" href="{url name=backend_domain_add create=1}" id="add-domain-button">
            <i class="block fa fa-plus fa-2x m-b-15"></i>
            <h4 class="block uppercase">{t}Add new domain{/t}</h4>
            <h5 class="wrap">
              {t}I DO NOT have my own domain and I want to create one and redirect it to my Opennemas digital newspaper{/t}
            </h5>
          </a>
        </div>
      </div>
      {if $ssl_enabled}
      <div class="tiles green m-b-15">
        <div class="tiles-body clearfix">
          <div class="col-sm-2">
            <div class="icon">
              <span class="fa-stack fa-3x">
                <i class="fa fa-shield fa-stack-2x"></i>
                <i class="fa fa-lock fa-stack-1x text-danger m-t-15 m-l-15"></i>
              </span>
            </div>
          </div>
          <div class="col-sm-10">
            <h4 class="text-white">{t escape=off}You have <strong><a href="https://en.wikipedia.org/wiki/HTTPS" class="text-white" title="What is SSL encryption?" target="_blank">SSL encription enabled</a></strong> for your main domain.{/t}</h4>
            <p>{t}Congrats, your newspaper is secure!{/t}</p>
          </div>
        </div>
      </div>
      {/if}
      <div>
        <h4>{t}Your domains{/t}</h4>
      </div>
      <div class="grid simple">
        <div class="grid-body no-padding">
          <div class="spinner-wrapper" ng-if="loading">
            <div class="loading-spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
          </div>
          <ul class="domain-list ng-cloak" ng-if="!loading">
            <li class="domain-list-item" ng-repeat="domain in domains">
              <div class="clearfix pointer" ng-click="expand($index)">
                <h4 class="m-r-10 pull-left">[% domain.name %]</h4>
                <span class="label label-success pull-left uppercase" ng-if="domain.main">
                  {t}Main{/t}
                </span>
                <span class="label label-info pull-left uppercase" ng-if="domain.free">
                  {t}Free{/t}
                </span>
                <span class="p-t-15 pull-right">
                  <span class="p-r-20" ng-if="domain.free || isRight(domain)"><i class="fa fa-lg fa-check text-success"></i></span>
                  <span class="p-r-20" ng-if="!domain.free && !isRight(domain)"><i class="fa fa-lg fa-exclamation-triangle text-danger"></i></span>
                  <i class="fa fa-chevron-right fa-lg " ng-class="{ 'fa-rotate-90': expanded[$index]}"></i>
                </span>
              </div>
              <div class="domain-list-item-details " ng-class="{ 'expanded': expanded[$index] }">
                <div class="sk-three-bounce" ng-if="domain.loading">
                  <div class="sk-child sk-bounce1"></div>
                  <div class="sk-child sk-bounce2"></div>
                  <div class="sk-child sk-bounce3"></div>
                </div>
                <div ng-if="!domain.loading">
                  <div class="row">
                    <div class="col-sm-12" ng-if="!domain.free">
                      <p><strong>{t}Points to{/t}:</strong> [% domain.target %]</p>
                      <p ng-if="isRight(domain)">
                        <i class="fa fa-lg fa-check text-success"></i>
                        {t}Your domain is properly configured{/t}
                      </p>
                      <p ng-if="!isRight(domain)">
                        <i class="fa fa-lg fa-exclamation-triangle text-danger"></i>
                        {t}Your domain is not properly configured, check the instructions below.{/t}
                      </p>
                    </div>
                    <div class="col-sm-6 hidden" ng-if="!domain.free">
                      <strong>{t}Expires{/t}:</strong> [% domain.expires %]
                    </div>
                    <div class="col-sm-12" ng-if="domain.free">
                      <i class="fa fa-lg fa-check text-success"></i>
                      {t}This is your opennemas address{/t}
                    </div>
                    <div class="col-sm-12" ng-if="!domain.free && !isRight(domain)">
                      <h4>{t}Update your DNS records{/t}</h4>
                      <h5 class="semi-bold">{t}Point the www entrace in your domain to the Opennemas service.{/t}</h5>
                      <div>
                        <pre style="font-size:1.05em; padding:15px; display:block; width:90%; margin:20px auto;">www     IN     CNAME     [% domain.name.replace('www.', '') %].opennemas.net.</pre>
                      </div>
                      <h4 class="m-t-30">{t}Redirect traffic from [% domain.name.replace('www.', '') %] to  www.[% domain.name.replace('www.', '') %]{/t}</h4>
                      <p>{t}Web Traffic -> domain.com -> redirect -> www.domain.com (this should be done by the hosting provider for your domain){/t}</p>
                      <p>
                        <span class="semi-bold">{t}NOTE{/t}:</span>
                        {t}This change is NOT done in the DNS section{/t}
                      </p>
                      <a href="javascript:UserVoice.showPopupWidget();">
                        <span class="fa fa-info-circle"></span>
                        {t}If you need more help configuring your domains contact our support team{/t}
                      </a>
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
{/block}
