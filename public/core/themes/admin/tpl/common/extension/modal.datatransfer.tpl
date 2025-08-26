<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
    <i class="fa fa-upload"></i>
    {t}Import data to instance{/t}
  </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-12" ng-show="!loading">
      <div class="panel-heading">
          <h4 class="panel-title">
              <span class="badge" style="background: #337ab7; margin-right: 10px;">1</span>
              {t}Select your JSON File{/t}
          </h4>
      </div>
      <div>
        <div class="text-center">
          <i class="fa fa-file-o fa-3x" ng-if="template.file"></i>
          <i class="fa fa-warning fa-3x text-warning" ng-if="!template.file"></i>
          <p class="m-t-15 text-center">
            <strong ng-if="template.file" title="[% getFileName() %]">
              [% template.file.name %]
            </strong>
            <strong ng-if="!template.file">
              {t}No file selected{/t}
            </strong>
          </p>
          <label class="btn btn-default btn-block m-t-15" for="file">
            <input class="hidden" id="file" name="file" file-model="template.file" type="file" accept="application/json"/>
            <span ng-if="!template.file">
              <i class="fa fa-plus m-r-5"></i>
              {t}Add{/t}
            </span>
            <span ng-if="template.file">
              <i class="fa fa-edit m-r-5"></i>
              {t}Change{/t}
            </span>
          </label>
        </div>
      </div>
    </div>
    <div class="col-md-12" ng-if="loading">
      <div class="panel-heading">
          <h4 class="panel-title">
              <span class="badge" style="background: #337ab7; margin-right: 10px;">2</span>
              {t}Import on process{/t}
          </h4>
      </div>
      <div>
        <div class="alert alert-info">
           {t}Please wait: The import process is currently running. Do not close this window.{/t}
        </div>
        <p>{t}Once the process is complete, this window will close automatically.{/t}</p>
      </div>
    </div>
</div>
<div class="modal-footer">
  <div>
    <button class="btn btn-primary" ng-click="confirm()" ng-disabled="loading" ng-show="template.file">{t}Import{/t}</button>
    <button class="btn secondary" ng-click="close()"  ng-disabled="loading">{t}Cancel{/t}</button>
  </div>
</div>
