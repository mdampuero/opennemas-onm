{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="AlbumConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_albums_list') %]">
                  <i class="fa fa-camera"></i>
                  {t}Albums{/t}
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
                <a class="btn btn-link" href="{url name=backend_albums_list}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
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
          {acl isAllowed="MASTER"}
            <div class="row">
              <div class="col-md-4">
                <h4>Layout</h4>
                <div class="controls">
                  <div class="radio">
                    <input class="form-control" id="layout-slider" ng-model="settings.album_layout" ng-value="'slider'" type="radio"/>
                    <label for="layout-slider">
                      {t}Slider{/t}
                    </label>
                  </div>
                  <div class="radio">
                    <input class="form-control" id="layout-list" ng-model="settings.album_layout" ng-value="'list'" type="radio"/>
                    <label for="layout-list">
                      {t}List{/t}
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <h4>Page view per photo</h4>
                <div class="controls">
                  <div class="checkbox">
                    <input class="form-control" id="stats-per-photo" name="stats-per-photo" ng-false-value="'0'" ng-model="settings.album_stats_photo" ng-true-value="'1'" type="checkbox"/>
                    <label class="form-label" for="stats-per-photo">
                      {t}Disable{/t}
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <h4>Max photos</h4>
                <div class="controls col-xs-4">
                  <input class="form-control" min="0" ng-model="settings.album_max" ng-value="settings.album_max" placeholder="100" type="number">
                </div>
              </div>
            </div>
            <div class="row m-t-30">
              <div class="col-md-6">
                <h4 class="no-margin">Extra fields</h4>
                <autoform-editor ng-model="extraFields"/>
              </div>
            </div>
          {/acl}
        </div>
      </div>
    </div>
  </form>
{/block}
