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


{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks">
        <button class="btn btn-primary btn-block btn-loading" ng-disabled="!template.file || saving" ng-click="import(template)">
          <i class="fa fa-cogs"></i>
          {t}Import{/t}
        </button>
      </li>
    </ul>
  </div>
{/block}

{block name="filters"}{/block}

{block name="list"}
<div class="ng-cloak row" ng-if="saving">
  <div class="col-lg-12">
    <div class="grid simple">
      <div class="grid-body">
        <strong>
          <i class="fa fa-spinner fa-spin text-danger"></i>
          {t}Please wait: The import process is currently running. Do not close this window.{/t}
        </strong>
      </div>
      <div class="grid-footer">
        <div class="row">
            <div class="col-xs-12">
                <small class="text-muted">
                  <i class="fa fa-check-circle"></i> {t}Once the process is complete, this window will close automatically.{/t}</small>
                </small>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="ng-cloak row" ng-if="!http.flags.loading && !saving">
  <div class="col-lg-8">
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
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="grid simple">
      <div class="grid-title">
        <h4>
          <i class="fa fa-info-circle"></i>
          {t}File Information{/t}
        </h4>
      </div>
      <div class="grid-body">
        <!-- Información del archivo cuando está seleccionado -->
        <div ng-show="template.file">
          <div class="file-info">
            <div class="file-info-item m-b-15">
              <div class="info-group m-b-15">
                <label class="text-muted small">{t}File Name{/t}</label>
                <div class="p-5 bg-light rounded">
                  <span id="fileName" class="text-primary">
                    [% template.file.name %]
                  </span>
                </div>
              </div>

                <div class="info-group m-b-15">
                  <label class="text-muted small">{t}File Type{/t}</label>
                  <div class="p-5 bg-light rounded">
                    <span id="fileType" class="text-info">
                      [% template.file.type %]
                    </span>
                  </div>
                </div>

              <div class="info-group m-b-15">
                <label class="text-muted small">{t}Last Modified{/t}</label>
                <div class="p-5 bg-light rounded">
                  <span id="fileDate" class="text-warning">
                    [% template.file.lastModifiedDate | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="ng-cloak row" ng-show="importedData || saving">
  <div class="col-lg-12">
    <div class="grid simple">
      <div class="grid-title">
        <h4>
          <i class="fa fa-cogs"></i>
          {t}Preview{/t}
        </h4>
      </div>
      <div class="grid-body no-padding">
        <div class="table-wrapper ng-cloak">
          <table class="table table-fixed table-hover no-margin">
            <thead>
              <tr>
                <th ng-repeat="col in displayedColumns track by $index"
                    class="v-align-middle [% col.name === 'widget_type' ? 'text-center' : '' %]"
                    width="[% col.with ? col.with + '%' : '150px' %]">
                  <span class="text-uppercase">
                    [% col.display %]
                  </span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr ng-class="{ row_selected: isSelected(getItemId(item)) }"
                  ng-repeat="item in getPaginatedItems()">
                <td class="v-align-middle [% col.name === 'widget_type' ? 'text-center' : '' %]"
                    ng-repeat="col in displayedColumns track by $index">
                  <div class="table-text">
                    <!-- Widget type icons -->
                    <span ng-if="col.name === 'widget_type'">
                      <i class="fa fa-lg fa-code" ng-if="!item.widget_type" uib-tooltip="HTML"></i>
                      <i class="fa fa-lg fa-cog" ng-if="item.widget_type" uib-tooltip="{t}IntelligentWidget{/t}"></i>
                    </span>

                    <!-- Advertisements with_script icons -->
                    <span ng-if="col.name === 'advertisements.0.with_script'">
                      <i class="fa fa-file-picture-o fa-lg m-r-5 text-success"
                        ng-if="getNestedValue(item, col.name) == 0 && item.is_flash != 1"
                        title="{t}Media element (jpg, png, gif){/t}">
                      </i>
                      <i class="fa fa-file-video-o fa-lg m-r-5 text-danger"
                        ng-if="getNestedValue(item, col.name) == 0 && item.is_flash == 1"
                        title="{t}Media flash element (swf){/t}">
                      </i>
                      <i class="fa fa-file-code-o fa-lg m-r-5 text-info"
                        ng-if="getNestedValue(item, col.name) == 1"
                        title="Javascript">
                      </i>
                      <i class="fa fa-gg fa-lg m-r-5 text-info"
                        ng-if="getNestedValue(item, col.name) == 2"
                        title="OpenX">
                      </i>
                      <i class="fa fa-google fa-lg m-r-5 text-danger"
                        ng-if="getNestedValue(item, col.name) == 3"
                        title="Google DFP">
                      </i>
                      <i class="fa fa-plus-square fa-lg m-r-5 text-warning"
                        ng-if="getNestedValue(item, col.name) == 4"
                        title="Smart Adserver">
                      </i>
                    </span>

                    <!-- Generic column values -->
                    <span ng-if="col.name !== 'widget_type' && col.name !== 'advertisements.0.with_script'">
                      [% getNestedValue(item, col.name) %]
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="text-center mt-4 mb-3" ng-if="items && items.length > pagination.itemsPerPage">
            Mostrando [% (pagination.currentPage - 1) * pagination.itemsPerPage + 1 %] -
            [% Math.min(pagination.currentPage * pagination.itemsPerPage, totalItems) %] de [% totalItems %] items

            <div>
              <button class="btn btn-primary btn-sm"
                      ng-click="pagination.currentPage = pagination.currentPage - 1"
                      ng-disabled="pagination.currentPage === 1">
                &lsaquo; {t} Previous {/t}
              </button>

              <span class="page-number">[% pagination.currentPage %]</span>

              <button class="btn btn-primary btn-sm"
                      ng-click="pagination.currentPage = pagination.currentPage + 1"
                      ng-disabled="pagination.currentPage * pagination.itemsPerPage >= totalItems">
                {t} Next {/t} &rsaquo;
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{/block}
