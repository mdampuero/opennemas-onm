<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <i class="fa fa-sitemap m-r-10"></i>
          </h4>
        </li>
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_sitemap_list') %]">
              <i class="fa fa-bell"></i>
              {t}Sitemap{/t}
            </a>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks" >
            <a class="btn btn-success text-uppercase" ng-click="save();">
              <i class="fa fa-save m-r-5"></i>
              {t}Save{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="grid simple">
    <div class="grid-body">
      <div class="row">
        <div class="col-md-6">
          <h4>
              <i class="fa fa-list"></i>
              {t}Listing{/t}
          </h4>
          <div class="form-group">
            <span class="help ">
              {t escape=off}Indicates the elements that will be displayed in each sitemap.{/t}
            </span>
          </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="sitemap-perpage">
                  {t}Items per page{/t}
                  <span class="help ">
                    (min. 500, máx. 1000)
                  </span>
                </label>
                <div class="controls">
                  <input class="form-control" id="sitemap-perpage" name="sitemap-perpage"
                    ng-model="items.perpage" type="number" min="500" max="1000">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="sitemap-total">
                  {t}Total items{/t}
                  <span class="help ">
                    (min. 100, máx. 1000)
                  </span>
                </label>
                <div class="controls">
                  <input class="form-control" id="sitemap-total" name="sitemap-total"
                    ng-model="items.total" type="number" min="100" max="1000">
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
          <h4>
            <i class="fa fa-eye"></i>
            Sitemap {t}Activated{/t}
          </h4>
          <div class="form-group">
            <span class="help">
              {t escape=off}Activate the elements that you want to appear in the sitemap.{/t}
            </span>
          </div>
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-album" name="sitemap-album" ng-false-value="0"
                    ng-model="items.album" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-album">
                    Sitemap {t}Albums{/t}
                  </label>
                </div>
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-articles" name="sitemap-articles" ng-false-value="0"
                    ng-model="items.articles" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-articles">
                    Sitemap {t}Articles{/t}
                  </label>
                </div>
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-events" name="sitemap-events" ng-false-value="0"
                    ng-model="items.events" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-events">
                    Sitemap {t}Events{/t}
                  </label>
                </div>
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-images" name="sitemap-images" ng-false-value="0"
                    ng-model="items.images" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-images">
                    Sitemap {t}Images{/t}
                  </label>
                </div>
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-kiosko" name="sitemap-kiosko" ng-false-value="0"
                    ng-model="items.kiosko" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-kiosko">
                    Sitemap {t}Newsstand{/t}
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-letters" name="sitemap-letters" ng-false-value="0"
                    ng-model="items.letters" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-letters">
                    Sitemap {t}Letter to the editor{/t}
                  </label>
                </div>
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-opinions" name="sitemap-opinions" ng-false-value="0"
                    ng-model="items.opinions" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-opinions">
                    Sitemap {t}Opinions{/t}
                  </label>
                </div>
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-polls" name="sitemap-polls" ng-false-value="0"
                    ng-model="items.polls" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-polls">
                    Sitemap {t}Polls{/t}
                  </label>
                </div>
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-tags" name="sitemap-tags" ng-false-value="0"
                    ng-model="items.tags" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-tags">
                    Sitemap {t}Tags{/t}
                  </label>
                </div>
                <div class="checkbox form-group">
                  <input class="form-control" id="sitemap-videos" name="sitemap-videos" ng-false-value="0"
                    ng-model="items.videos" ng-true-value="1"  type="checkbox"/>
                  <label class="form-label" for="sitemap-videos">
                    Sitemap {t}Videos{/t}
                  </label>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
