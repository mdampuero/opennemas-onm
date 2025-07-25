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
                    Importar Archivo
                </h4>
            </div>
            <div class="grid-body">
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
                      <input class="hidden" id="file" name="file" file-model="template.file" type="file" accept="text/json"/>
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

                <div class="file-info" ng-show="template.file">
                    <div class="file-info-item">
                        <strong>Nombre:</strong>
                        <span id="fileName">
                          [% template.file.name %]
                        </span>
                    </div>
                    <div class="file-info-item">
                        <strong>Tipo:</strong>
                        <span id="fileType">
                          [% template.file.type %]
                        </span>
                    </div>
                    <div class="file-info-item">
                        <strong>Última modificación:</strong>
                         <span id="fileDate">
                            [% template.file.lastModifiedDate | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                         </span>
                    </div>
                </div>

                <hr>
                <div class="form-group no-margin">
                  <div class="controls">
                    <button class="btn btn-block btn-loading btn-primary" ng-disabled="!template.file || http.flags.loading" ng-click="import(template)">
                        <i class="fa fa-cogs"></i>
                        Procesar
                    </button>
                    <button class="btn btn-block btn-loading btn-danger" disabled>
                        <i class="fa fa-trash"></i>
                        Limpiar
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
            Vista Previa
          </h4>
        </div>
        <div class="grid-body">
          <div class="preview-content">

            <!-- Loader -->
            <div class="loading" ng-show="!template.file">
              <i class="fa fa-spinner fa-spin fa-3x"></i>
              <p class="m-t-15">{t}Cargando vista previa...{/t}</p>
            </div>

            <!-- Vista previa con scroll limitado -->
            <div ng-if="importedData" class="m-t-20">
              <pre class="json-preview">
    [% (Array.isArray(importedData) ? importedData.slice(0, 50) : importedData) | json:2 %]
              </pre>
              <div class="text-muted small" ng-if="importedData.length > 50">
                {t}Mostrando los primeros 50 elementos{/t}...
              </div>
            </div>

          </div>
        </div>
      </div>
  </div>

  </div>
{/block}
