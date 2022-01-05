{extends file="base/admin.tpl"}
{block name="content"}
  <form name="settingForm" ng-controller="GeneralSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-cog fa-lg"></i>
                {t}Settings{/t} > {t}General{/t} > {t}General{/t} & SEO
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-primary" ng-click="save()" ng-disabled="settingForm.$invalid" type="button">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving}"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content ng-cloak no-animate" ng-if="loading">
      <div class="spinner-wrapper">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
    </div>
    <div class="content ng-cloak" ng-if="!loading">
      <div class="grid simple settings">
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-md-6">
              <div class="p-r-15">
                <h4>
                  <i class="fa fa-cog m-r-5"></i>
                  {t}General{/t}
                </h4>

                <div class="form-group">
                  <label class="form-label" for="site-name">
                    {t}Site name{/t}
                  </label>
                  <span class="help">
                    {t}This will be displayed as your site name.{/t}
                  </span>
                  <div class="controls">
                    <input class="form-control" id="site-name" name="site-name" ng-model="settings.site_name" type="text">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="site-footer">
                    {t}Footer text{/t}
                  </label>
                  <span class="help">
                    {t}Text showed at the bottom of your page. Usually used for copyright notice.{/t}
                  </span>
                  <div class="controls">
                    <textarea class="form-control" id="site_footer" name="site-footer" ng-model="settings.site_footer" onm-editor onm-editor-preset="simple"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-l-15">
                <h4>
                  <i class="fa fa-line-chart"></i>
                  {t}SEO options{/t}
                </h4>
                <div class="form-group">
                  <label class="form-label" for="site-title">
                    {t}Site title{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="site-title" name="site-title" ng-model="settings.site_title" type="text">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="site-keywords">
                    {t}Site keywords{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="site-keywords" name="site-keywords" ng-model="settings.site_keywords" type="text">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="site-description">
                    {t}Site description{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="site-description" name="site-description" ng-model="settings.site_description" type="text">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="refresh-interval">
                    {t}Refresh page interval{/t}
                    <small>({t}seconds{/t})</small>
                  </label>
                  <span class="help">
                    {t}When a user visits pages and stay on it for a while, this setting allows to refresh the loaded page for updated it.{/t}
                  </span>
                  <div class="controls">
                    <input class="form-control" id="refresh-interval" name="refresh-interval" ng-model="settings.refresh_interval" type="number">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="webmaster-tools-google">
                    {t}Google Web Master Tools{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="webmaster-tools-google" name="webmaster-tools-google" ng-model="settings.webmastertools_google" type="text">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="webmastertools-bing">
                    {t}Bing Web Master Tools{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control"  id="webmastertools-bing" name="webmastertools-bing" ng-model="settings.webmastertools_bing" type="text">
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
