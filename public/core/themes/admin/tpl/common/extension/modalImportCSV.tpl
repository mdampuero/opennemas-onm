<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">
    <i class="fa fa-upload"></i>
    {t}Import Items{/t} [% subscriber %]
  </h4>
</div>

<div class="modal-body">
  <!-- Paso 1: Subida de fichero -->
  <div ng-show="!template.file">
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

  <!-- Paso 2: Seleccionar lista -->
  <div ng-if="template.file">
    <p>{t}Step 2: Select the list to import to{/t}</p>
    <select ng-model="selectedList" ng-options="list.name for list in availableLists">
      <option value="">{t}Select a list{/t}</option>
    </select>
  </div>
</div>

<div class="modal-footer">
  <!-- Footer para el Paso 1 -->
  <div>
    <button class="btn btn-primary" ng-click="confirm()" ng-show="template.file">{t}Import{/t}</button>
    <button class="btn secondary" ng-click="close()">{t}Cancel{/t}</button>
  </div>
</div>
