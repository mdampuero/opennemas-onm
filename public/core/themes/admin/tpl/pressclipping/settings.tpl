{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="PressClippingConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="">
                  <i class="fa fa-cog m-r-10"></i>
                  {t}Pressclipping notifications Configuration{/t}
                </a>
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success ng-cloak text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-xs-6 col-md-3">
              <h4>{t}PressClipping service{/t}</h4>
              <div class="controls">
                <select class="form-control-lg" ng-model="settings.pressclipping_service.service">
                  <option value="cedro" selected>{t}CEDRO{/t}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <div class="row" ng-if="settings.pressclipping_service.service == 'cedro'">
            <div class="col-xs-12">
              <div class="col-xs-12">
                <div class="row">
                  <h4>{t}CEDRO service credentials{/t}</h4>
                </div>
              </div>
              <div class="row">
                <div class="controls col-xs-6 col-md-6 m-b-10">
                  <label>{t}Publication ID{/t}</label>
                  <input class="form-control" ng-model="settings.pressclipping_service.pubID" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Can be found in your CEDRO Account{/t}</small>
                </div>
                <div class="controls col-xs-6 col-md-6 m-b-10">
                  <label>{t}API key{/t}</label>
                  <input class="form-control" ng-model="settings.pressclipping_service.apikey" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Can be found in your CEDRO Account{/t} | Token </small>
                </div>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="p-t-15">
                <div class="text-center">
                  <button class="btn btn-block btn-loading m-t-5" ng-class="{ 'btn-light': !status , 'btn-success': status === 'success' , 'btn-danger': status === 'failure' }" ng-click="check()" ng-disabled="!settings.pressclipping_service.apikey || flags.http.checking" type="button">
                    <i class="fa fa-plug m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.checking }"></i>
                    {t}Connect{/t}
                    <i class="fa fa-check m-l-5" ng-show="status === 'success'"></i>
                    <i class="fa fa-exclamation-circle m-l-5" ng-show="status === 'failure'"></i>
                  </button>
                </div>
              </div>
              <div class="p-t-15">
                <div class="text-center">
                  <button class="btn btn-block btn-loading m-t-5" ng-class="{ 'btn-light': !status , 'btn-success': statusDump === 'success' , 'btn-danger': statusDump === 'failure' }" ng-click="dump()" ng-disabled="!settings.pressclipping_service.apikey || flags.http.checking" type="button">
                    <i class="fa fa-plug m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.checking }"></i>
                    {t}Dump Data{/t}
                    <i class="fa fa-check m-l-5" ng-show="statusDump === 'success'"></i>
                    <i class="fa fa-exclamation-circle m-l-5" ng-show="statusDump === 'failure'"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
