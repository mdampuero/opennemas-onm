<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
  <h4 class="modal-title">
      <i class="fa fa-sign-in"></i>
      {t}Import Settings{/t}
  </h4>
</div>
<div class="modal-body">
  <div class="form-group">
    <label class="form-label clearfix" for="settings">
      <div class="pull-left">{t}Insert a valid JSON{/t}</div>
    </label>
    <div class="controls">
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
            <input class="hidden" id="file" name="file" file-model="template.file" type="file" accept="application/JSON"/>
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
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="confirm()" ng-disabled="loading">{t}Import{/t}</button>
    <button class="btn secondary" ng-click="close()" ng-disabled="loading">{t}Cancel{/t}</button>
</div>
