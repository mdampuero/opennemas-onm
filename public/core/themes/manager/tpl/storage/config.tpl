<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_storage_config') %]">
              <i class="fa fa-file-o"></i>
              {t}Configs{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="promptForm.$invalid || saving">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="grid simple bg-white onm-shadow">
    <div class="grid-body ng-cloak">
      <div class="row">
        <div class="col-sm-6">
          <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
            <div class="form-status text-left">
              <label class="m-b-10"><b>{t}Video compress{/t}</b></label>
              <hr>
              <div class="row">
                <div class="col-xs-12 form-group">
                  <label class="form-label" for="command" >{t}Command{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    ng-model="storage_settings.compress.command">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-6">
              <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
                <div class="form-status text-left">
                  <label class="m-b-10"><b>{t}Thumbnail generate{/t}</b></label>
                  <hr>
                  <div class="row">
                    <div class="col-xs-6 form-group">
                      <label class="form-label" for="seconds" >{t}Seconds{/t}</label>
                      <input
                        type="text"
                        class="form-control"
                        ng-model="storage_settings.thumbnail.seconds">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
                <div class="form-status text-left">
                  <label class="m-b-10"><b>{t}Concurrent tasks{/t}</b></label>
                  <hr>
                  <div class="row">
                    <div class="col-xs-6 form-group">
                      <label class="form-label" for="seconds" >{t}Number of tasks{/t}</label>
                      <input
                        type="text"
                        class="form-control"
                        ng-model="storage_settings.tasks.concurrent">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
            <div class="form-status text-left">
              <label class="m-b-10"><b>{t}S3 Provider{/t}</b></label>
              <hr>
              <div class="row">
                <div class="col-xs-12 form-group">
                  <label class="form-label" for="endpoint">{t}Upload endpoint{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    ng-model="storage_settings.provider.endpoint">
                </div>
                <div class="col-xs-6 form-group">
                  <label class="form-label" for="key">{t}Key{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    ng-model="storage_settings.provider.key">
                </div>
                <div class="col-xs-6 form-group">
                  <label class="form-label" for="secret">{t}Secret{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    ng-model="storage_settings.provider.secret">
                </div>
                <div class="col-xs-6 form-group">
                  <label class="form-label" for="bucket">{t}Bucket{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    ng-model="storage_settings.provider.bucket">
                </div>
                <div class="col-xs-6 form-group">
                  <label class="form-label" for="region">{t}Region{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    ng-model="storage_settings.provider.region">
                </div>
                <div class="col-xs-12 form-group">
                  <label class="form-label" for="public_endpoint">{t}Download endpoint{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    ng-model="storage_settings.provider.public_endpoint">
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-import-settings">
  {include file="common/modalImportSettings.tpl"}
</script>
