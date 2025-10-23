<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();">&times;</button>
  <h4 class="modal-title">{t}Settings{/t}</h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-sm-12">
      <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
        <div class="form-status text-left">
          <label class="m-b-10"><b>{t}Video compress{/t}</b></label>
          <hr>
          <div class="row">
            <div class="col-xs-9 form-group">
              <label class="form-label" for="command">{t}Command{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.compress.command">
            </div>
            <div class="col-xs-3 form-group">
              <label class="form-label" for="seconds">{t}Thumbnail{/t} (s)</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.thumbnail.seconds">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-15 bg-light">
        <div class="form-status text-left"
          ng-init="template.storage_settings = template.storage_settings || {}; template.storage_settings.provider = template.storage_settings.provider || {}; template.storage_settings.provider.type = template.storage_settings.provider.type || ((template.storage_settings.provider.api_base_url || template.storage_settings.provider.embed_base_url || template.storage_settings.provider.library_id || template.storage_settings.provider.api_key) ? 'bunny' : 's3')">
          <label class="m-b-10"><b>{t}Storage{/t}</b></label>
          <hr>
          <div class="row">
            <div class="col-xs-12 form-group">
              <label class="form-label" for="modal_provider_type">{t}Provider{/t}</label>
              <select id="modal_provider_type" class="form-control" ng-model="template.storage_settings.provider.type">
                <option value="s3">{t}S3 Provider{/t}</option>
                <option value="bunny">{t}Bunny Stream{/t}</option>
              </select>
            </div>
          </div>
          <div class="row" ng-if="template.storage_settings.provider.type !== 'bunny'">
            <div class="col-xs-12 form-group">
              <label class="form-label" for="endpoint">{t}Upload endpoint{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.endpoint">
            </div>
            <div class="col-xs-6 form-group">
              <label class="form-label" for="key">{t}Key{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.key">
            </div>
            <div class="col-xs-6 form-group">
              <label class="form-label" for="secret">{t}Secret{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.secret">
            </div>
            <div class="col-xs-6 form-group">
              <label class="form-label" for="bucket">{t}Bucket{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.bucket">
            </div>
            <div class="col-xs-6 form-group">
              <label class="form-label" for="region">{t}Region{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.region">
            </div>
            <div class="col-xs-12 form-group">
              <label class="form-label" for="public_endpoint">{t}Download endpoint{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.public_endpoint">
            </div>
          </div>
          <div class="row" ng-if="template.storage_settings.provider.type === 'bunny'">
            <div class="col-xs-12 form-group">
              <label class="form-label" for="api_base_url">{t}API Base URL{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.api_base_url">
            </div>
            <div class="col-xs-12 form-group">
              <label class="form-label" for="embed_base_url">{t}Embed Base URL{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.embed_base_url">
            </div>
            <div class="col-xs-6 form-group">
              <label class="form-label" for="library_id">{t}Library ID{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.library_id">
            </div>
            <div class="col-xs-6 form-group">
              <label class="form-label" for="api_key">{t}API Key{/t}</label>
              <input type="text" class="form-control" ng-model="template.storage_settings.provider.api_key">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button class="btn secondary" ng-click="dismiss()" ng-disabled="loading">{t}Cancel{/t}</button>
  <button class="btn btn-loading btn-success text-uppercase" ng-click="confirm()">
    <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
  </button>
</div>