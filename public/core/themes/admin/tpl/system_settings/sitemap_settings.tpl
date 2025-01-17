{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="SitemapSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-sitemap fa-lg"></i>
                {t}Sitemap{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-primary" ng-click="save()" type="button">
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
          {if $app.security->hasPermission('MASTER')}
            <div class="row">
              <div class="col-md-4">
                <h4>
                    <i class="fa fa-list"></i>
                    {t}Listing{/t}
                </h4>
                <div class="form-group">
                  <span class="help ">
                    {t escape=off}Indicates the elements that will be displayed in each sitemap.{/t}
                  </span>
                </div>
                <div class="row">
                  <div class="col-md-10">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label" for="sitemap-total">
                            {t}Total sitemap news elements{/t}
                          </label>
                          <span class="help ">
                              (min. 100, máx. 1000)
                          </span>
                          <div class="controls">
                            <input class="form-control" id="sitemap-total" name="sitemap-total" ng-model="settings.sitemap.total" type="number" min="100" max="1000">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label" for="sitemap-perpage">
                            {t}Elements per page{/t}
                          </label>
                          <span class="help ">
                              (min. 100, máx. 1000)
                          </span>
                          <div class="controls">
                            <input class="form-control" id="sitemap-perpage" name="sitemap-perpage" ng-model="settings.sitemap.perpage" type="number" min="100" max="1000">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label" for="sitemap-perpage">
                          {t}Content per year{/t}
                        </label>
                        <select class="form-control" ng-model="settings.sitemap.contentyear" id="sitemap-contentyear" name="sitemap-contentyear">
                            <option value="">{t}All{/t}</option>
                            <option ng-repeat="year in extra.sitemaps.years" value="[% year %]">
                              [% year %]
                            </option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label" for="sitemap-limithours">
                          {t}Limit per hours{/t}
                        </label>
                        <select class="form-control" ng-model="settings.sitemap.limithours" id="sitemap-limithours" name="sitemap-limithours">
                            <option value="">{t}All{/t}</option>
                            <option ng-repeat="hours in ['24','48','72','96']" value="[% hours %]">
                              [% hours %]
                            </option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <h4>
                  <i class="fa fa-eye"></i>
                  {t}Sitemaps enabled{/t}
                </h4>
                <div class="form-group">
                  <span class="help">
                    {t escape=off}Activate the elements that you want to appear in the sitemap.{/t}
                  </span>
                </div>
                <div class="col-md-6">
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-album" name="sitemap-album" ng-false-value="0" ng-model="settings.sitemap.album" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-album">
                      {t}Albums{/t}
                    </label>
                  </div>
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-article" name="sitemap-article" ng-false-value="0" ng-model="settings.sitemap.article" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-article">
                      {t}Articles{/t}
                    </label>
                  </div>
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-letter" name="sitemap-letter" ng-false-value="0" ng-model="settings.sitemap.letter" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-letter">
                      {t}Letter to the editor{/t}
                    </label>
                  </div>
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-event" name="sitemap-event" ng-false-value="0" ng-model="settings.sitemap.event" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-event">
                      {t}Events{/t}
                    </label>
                  </div>
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-kiosko" name="sitemap-kiosko" ng-false-value="0" ng-model="settings.sitemap.kiosko" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-kiosko">
                      {t}Newsstand{/t}
                    </label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-opinion" name="sitemap-opinion" ng-false-value="0" ng-model="settings.sitemap.opinion" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-opinion">
                      {t}Opinions{/t}
                    </label>
                  </div>
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-poll" name="sitemap-poll" ng-false-value="0" ng-model="settings.sitemap.poll" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-poll">
                      {t}Polls{/t}
                    </label>
                  </div>
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-tag" name="sitemap-tag" ng-false-value="0" ng-model="settings.sitemap.tag" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-tag">
                      {t}Tags{/t}
                    </label>
                  </div>
                  <div class="checkbox form-group">
                    <input class="form-control" id="sitemap-video" name="sitemap-video" ng-false-value="0" ng-model="settings.sitemap.video" ng-true-value="1"  type="checkbox"/>
                    <label class="form-label" for="sitemap-video">
                      {t}Videos{/t}
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <h4>
                  <i class="fa fa-file"></i>
                  {t}Sitemaps disk cache{/t}
                </h4>
                <div class="form-group">
                  <span class="help ">
                    {t escape=off}This section allows to remove the sitemaps saved on disk.{/t}
                  </span>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-label" for="sitemap-search">
                        {t}Search for a sitemap by year, month and page{/t}
                      </label>
                      <div class="row">
                        <div class="col-md-4 controls">
                          <select class="form-control" ng-model="criteria.year" name="year">
                            <option value="">{t}All{/t}</option>
                            <option ng-repeat="year in extra.sitemaps.years" value="[% year %]">
                              [% year %]
                            </option>
                          </select>
                        </div>
                        <div class="col-md-4 controls">
                          <select class="form-control" ng-model="criteria.month" name="month">
                            <option value="">{t}All{/t}</option>
                            <option ng-repeat="month in ['01','02','03','04','05','06','07','08','09',10,11,12]" value="[% month %]">
                              [% month %]
                            </option>
                          </select>
                        </div>
                        <div class="col-md-4 controls">
                          <input class="form-control" ng-model="criteria.page" type="number">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3 controls m-r-3">
                          <div class="form-group">
                            <button class="form-control btn-info" ng-click="flags.show = !flags.show">
                              <i ng-show="flags.show" class="fa fa-eye m-r-5"></i>
                              {t}Show{/t}
                            </button>
                          </div>
                        </div>
                        <div class="col-md-3 controls">
                          <div class="form-group">
                            <button class="form-control btn-danger" ng-click="removeSitemaps()">
                              <i class="fa fa-trash m-r-5"></i>
                              {t}Remove{/t}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div ng-if="flags.show" class="row">
                  <div class="col-12 panel-flex">
                    <div ng-repeat="item in extra.sitemaps.items | filter: filterSitemaps(criteria)">
                      <i class="fa fa-file m-r-5"></i>
                      [% item %]
                    </div>
                  </div>
                </div>
              </div>
            </div>
          {/if}
        </div>
      </div>
    </div>
  </form>
{/block}
