{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Datatransfer{/t}
{/block}

{block name="ngInit"}
  ng-controller="DatatransferCtrl"
{/block}

{block name="icon"}
  <i class="fa fa-upload m-r-10"></i>
{/block}

{block name="title"}
  {t}Datatransfer{/t}
{/block}

{block name="filters"}{/block}

{block name="list"}
<div class="ng-cloak row" ng-if="!http.flags.loading">
  <div class="col-lg-6">
    <div class="grid simple">
      <div class="grid-title">
        <h4>
          <i class="fa fa-upload"></i>
          {t}Importar archivo JSON{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div>
          <div class="text-center">
            <i class="fa fa-file-o fa-3x text-info" ng-if="template.file"></i>
            <i class="fa fa-warning fa-3x text-warning" ng-if="!template.file"></i>

            <p class="m-t-15 text-center">
              <strong class="text-success" ng-if="template.file" title="[% getFileName() %]">
                [% template.file.name %]
              </strong>
              <strong class="text-muted" ng-if="!template.file">
                {t}No file selected{/t}
              </strong>
            </p>

            <label class="btn btn-primary btn-block m-t-15" for="file">
              <input class="hidden" id="file" name="file" file-model="template.file" type="file" accept="application/json"/>
              <span ng-if="!template.file">
                <i class="fa fa-plus m-r-5"></i>
                {t}Select JSON file{/t}
              </span>
              <span ng-if="template.file">
                <i class="fa fa-edit m-r-5"></i>
                {t}Change file{/t}
              </span>
            </label>
          </div>
        </div>

        <div class="file-info m-t-20 p-10 bg-light rounded" ng-show="template.file">
          <div class="file-info-item">
            <strong>{t}Name:{/t}</strong>
            <span id="fileName">[% template.file.name %]</span>
          </div>
          <div class="file-info-item">
            <strong>{t}Type:{/t}</strong>
            <span id="fileType">[% template.file.type %]</span>
          </div>
          <div class="file-info-item">
            <strong>{t}Last modified:{/t}</strong>
            <span id="fileDate">
              [% template.file.lastModifiedDate | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
            </span>
          </div>
        </div>

        <hr class="m-t-20 m-b-20"/>

        <div class="form-group no-margin">
          <div class="controls">
            <button class="btn btn-primary btn-block btn-loading" ng-disabled="!template.file || http.flags.loading" ng-click="import(template)">
              <i class="fa fa-cogs"></i>
              {t}Process file{/t}
            </button>
            <button class="btn btn-default btn-block btn-loading" ng-disabled="!template.file">
              <i class="fa fa-trash"></i>
              {t}Clear file{/t}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="grid simple">
      <div class="grid-title">
        <h4>
          <i class="fa fa-eye"></i>
          {t}Preview{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div class="preview-content">

          <!-- Estado: No hay archivo seleccionado -->
          <div class="text-center" ng-show="!template.file">
            <i class="fa fa-file-text-o fa-4x text-muted"></i>
            <p class="m-t-15 text-muted">
              <strong>{t}Select a file to preview{/t}</strong>
            </p>
            <small class="text-muted">{t}The preview will appear here once you select a JSON or CSV file{/t}</small>
          </div>

          <!-- Estado: Archivo seleccionado pero no procesado -->
          <div class="text-center" ng-show="template.file && !importedData && !http.flags.loading && !previewError">
            <i class="fa fa-file-code-o fa-4x text-info"></i>
            <p class="m-t-15">
              <strong class="text-info">{t}File selected but not processed{/t}</strong>
            </p>
            <p class="text-muted">
              <small>{t}Click "Process" to generate the preview{/t}</small>
            </p>
          </div>

          <!-- Estado: Error al procesar -->
          <div ng-if="previewError" class="text-center">
            <i class="fa fa-exclamation-triangle fa-4x text-danger"></i>
            <p class="m-t-15">
              <strong class="text-danger">{t}Error to processing the file{/t}</strong>
            </p>
          </div>

          <!-- Vista previa del contenido -->
          <div ng-if="importedData && !http.flags.loading">
            <!-- Header con información del resultado -->
            <div class="m-b-15">
              <div class="row">
                <div class="col-xs-6">
                  <small class="text-muted" ng-show="filename">
                    <i class="fa fa-info-circle m-r-5"></i>
                    <strong>{t}Name:{/t}</strong>
                    <span class="label label-info">[% filename %]</span>
                  </small>
                </div>
                <div class="col-xs-6 text-right">
                  <small class="text-muted">
                    <i class="fa fa-check-circle text-success m-r-5"></i>
                    {t}Processed successfully{/t}
                    <i class="fa fa-times-circle text-danger m-r-5"></i>
                    {t}Error to processing{/t}
                  </small>
                </div>
              </div>
            </div>

            <div
              class="well"
              style="
              max-height: 400px;
              overflow-y: auto;
              background-color: #f8f8f8;
              border: 1px solid #e3e3e3;"
              ng-show="!previewError"
            >
              <div class="m-b-10">
                <span class="label label-default" ng-if="Array.isArray(importedData)">
                  <i class="fa fa-list"></i> Array
                </span>
                <span class="label label-default" ng-if="!Array.isArray(importedData)">
                  <i class="fa fa-code"></i> Object
                </span>
              </div>
              <pre
                style="
                background: transparent;
                border: none;
                padding: 0;
                margin: 0;
                white-space: pre-wrap;
                word-wrap: break-word;
                font-size: 12px;
                line-height: 1.4;"
              > [% importedData | json:2 %]
              </pre>
            </div>

            <div class="m-t-15">
              <div class="row">
                <div class="col-xs-12 text-right">
                  <small class="text-muted">
                    <i class="fa fa-clock-o m-r-5"></i>
                    {t}File modified:{/t} [% template.file.lastModifiedDate | moment:'HH:mm' %]
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{/block}
