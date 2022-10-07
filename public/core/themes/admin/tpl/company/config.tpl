{extends file="base/admin.tpl"}

{block name="content"}
<form name="form" ng-controller="CompanyConfCtrl" ng-init="init()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="[% routing.generate('backend_companies_list') %]">
                <i class="fa fa-building"></i>
                {t}Companies{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>{t}Configuration{/t}</h4>
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
    <div class="grid simple">
      <div class="grid-body ng-cloak">
      <h4>Custom Fields</h4>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="metadata" class="form-label">
                <i class="fa fa-pie-chart m-r-10"></i>{t}Sector{/t}
              </label>
              <div class="controls">
                <div class="tags-input-wrapper">
                  <tags-input display-property="name" key-property="name" min-length="2" ng-model="compay_fields.sectors" placeholder="{t}Add a sector...{/t}">
                  </tags-input>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="metadata" class="form-label">
                <i class="fa fa-line-chart m-r-10"></i>{t}Activity{/t}
              </label>
              <div class="controls">
                <div class="tags-input-wrapper">
                  <tags-input display-property="name" key-property="name" min-length="2" ng-model="compay_fields.activity"  placeholder="{t}Add an activity...{/t}">
                  </tags-input>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="metadata" class="form-label">
                <i class="fa fa-shopping-basket m-r-10"></i>{t}Products{/t}
              </label>
              <div class="controls">
                <div class="tags-input-wrapper">
                  <tags-input display-property="name" key-property="name" min-length="2" ng-model="compay_fields.products"  placeholder="{t}Add a product...{/t}">
                  </tags-input>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
