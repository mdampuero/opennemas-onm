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
        <h4>{t}Search Fields{/t}</h4>
        <div class="help">{t}Add fields in order to filter on company list page{/t}</div>
        <div class="row m-t-25">
          <div class="col-md-6">
            <button class="btn btn-block btn-success" ng-click="addField()" type="button">
              <i class="fa fa-plus m-r-5"></i>{t}Add field{/t}
            </button>

          </div>
        </div>
        <div class="m-t-50" ng-repeat="field in company_fields track by $index">
          <div class="col-md-6">
            <div class="form-group">
              <label for="fieldKeyName" class="form-label">
                {t}Field name{/t}
              </label>
              <div class="help">{t}Name displayed in form{/t}</div>

              <div class="controls">
                <input type="text" name="fieldKeyName" ng-model="field.key.name">
              </div>
            </div>
            <div class="form-group">
                <label for="fieldValues" class="form-label">
                  {t}Field values{/t}
                </label>
                <div class="help">{t}Words suggested by the form input{/t}</div>
              <div class="controls">
                <div class="tags-input-wrapper">
                  <tags-input display-property="name" on-tag-adding="checkTag($tag)" replace-spaces-with-dashes="false" name="fieldValues" key-property="name" min-length="2" ng-model="field.values" placeholder="{t}Add a value...{/t}">
                  </tags-input>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="controls">
                <div class="col-md-6 col-md-offset-3">
                  <button class="btn btn-block btn-danger" ng-click="removeField($index)" type="button">
                    <i class="fa fa-trash m-r-5"></i>{t}Remove field{/t}
                  </button>
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
