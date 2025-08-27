<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
    <i class="fa fa-upload"></i>
    {t}Import subscribers{/t}
  </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-12" ng-show="!loading">
      <div class="panel-heading" ng-if="!template.file">
        <div class="alert alert-warning m-t-15 text-left">
          <i class="fa fa-info-circle m-r-5"></i>
          {t}Expected CSV format{/t}
          <pre class="m-t-10" style="white-space: pre-line;">
            Email, name (optional), signupDate (optional)
            john@example.com, John Doe, 2023-07-15
            jane@example.com, , 2023-08-01
          </pre>
          <small class="text-muted">
            <p>{t}Only CSV files are allowed.{/t}</p>
            <p>{t}The first row of the file will be ignored. These are expected to be the column names.{/t}</p>
            <p>{t}The maximum number of subscribers allowed to be imported into the same file is 1000.{/t}</p>
            <p>{t}If the "name" column is empty, the email address itself will be used as the name. If the "signupDate" column is empty, the registration date will be the current date.{/t}</p>
          </small>
        </div>
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
            <input class="hidden" id="file" name="file" file-model="template.file" type="file" accept="text/csv"/>
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
    <div class="col-md-12 m-t-20" ng-show="!loading" ng-if="template.file && template.type === 2">
      <div class="form-group">
        <label for="newsletterLists" class="control-label">
            <i class="fa fa-envelope"></i>
            {t}Select the lists you want to import subscribers to{/t}
        </label>
        <select class="form-control" multiple size="5" id="newsletterLists" ng-model="template.selectList" ng-options="list as list.name for (key, list) in template.lists" style="min-height: 130px;">
        </select>
      </div>
    </div>
    <div class="col-md-12" ng-if="loading">
      <div>
        <div class="alert alert-info m-t-15 text-left">
          <p>
            <i class="fa fa-info-circle m-r-5"></i>
            {t}Importing{/t}
          </p>
          <p>{t}Please wait: The import process is currently running. Do not close this window.{/t}</p>
          <p>{t}Once the process is complete, this window will close automatically.{/t}</p>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <div>
    <button class="btn btn-primary" ng-click="confirm()" ng-disabled="loading" ng-show="template.file">{t}Import{/t}</button>
    <button class="btn secondary" ng-click="close()"  ng-disabled="loading">{t}Cancel{/t}</button>
  </div>
</div>
