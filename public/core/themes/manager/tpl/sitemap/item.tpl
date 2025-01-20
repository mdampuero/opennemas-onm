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
            {t}Sitemap{/t}
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-loading btn-success text-uppercase" ng-click="save();">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i>
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
              <label class="form-label" for="sitemap-total">
                {t}Total sitemap news elements{/t}
                <span class="help ">
                  (min. 100, máx. 1000)
                </span>
              </label>
              <div class="controls">
                <input class="form-control" id="sitemap-total" name="sitemap-total" ng-model="item.total" type="number" min="100" max="1000">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label" for="sitemap-perpage">
                {t}Elements per page{/t}
                <span class="help ">
                  (min. 100, máx. 1000)
                </span>
              </label>
              <div class="controls">
                <input class="form-control" id="sitemap-perpage" name="sitemap-perpage" ng-model="item.perpage" type="number" min="100" max="1000">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label" for="sitemap-perpage">
                {t}Content per year{/t}
              </label>
              <div class="controls">
                <input class="form-control" id="sitemap-contentyear" name="sitemap-contentyear" ng-model="item.contentyear" type="number">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label" for="sitemap-limitdays">
                {t}Limit per days{/t}
              </label>
              <div class="controls">
                <input class="form-control" id="sitemap-limitdays" name="sitemap-limitdays" ng-model="item.limitdays" type="number" min="1" max="4">
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <h4>
            <i class="fa fa-eye"></i>
            {t}Sitemaps enabled{/t}
          </h4>
          <div class="form-group">
            <span class="help">
              {t escape=off}Activate the elements that you want to appear in the sitemap.{/t}
            </span>
          </div>
          <div class="col-md-12">
            <div class="col-md-6">
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-album" name="sitemap-album" ng-false-value="0" ng-model="item.album" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-album">
                  {t}Albums{/t}
                </label>
              </div>
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-article" name="sitemap-article" ng-false-value="0" ng-model="item.article" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-article">
                  {t}Articles{/t}
                </label>
              </div>
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-letter" name="sitemap-letter" ng-false-value="0" ng-model="item.letter" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-letter">
                  {t}Letter to the editor{/t}
                </label>
              </div>
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-event" name="sitemap-event" ng-false-value="0" ng-model="item.event" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-event">
                  {t}Events{/t}
                </label>
              </div>
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-kiosko" name="sitemap-kiosko" ng-false-value="0" ng-model="item.kiosko" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-kiosko">
                  {t}Newsstand{/t}
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-opinion" name="sitemap-opinion" ng-false-value="0" ng-model="item.opinion" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-opinion">
                  {t}Opinions{/t}
                </label>
              </div>
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-poll" name="sitemap-poll" ng-false-value="0" ng-model="item.poll" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-poll">
                  {t}Polls{/t}
                </label>
              </div>
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-tag" name="sitemap-tag" ng-false-value="0" ng-model="item.tag" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-tag">
                  {t}Tags{/t}
                </label>
              </div>
              <div class="checkbox form-group">
                <input class="form-control" id="sitemap-video" name="sitemap-video" ng-false-value="0" ng-model="item.video" ng-true-value="1"  type="checkbox"/>
                <label class="form-label" for="sitemap-video">
                  {t}Videos{/t}
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
