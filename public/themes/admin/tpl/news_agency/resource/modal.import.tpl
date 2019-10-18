<div class="modal-body">
  <button class="close" ng-click="close({ success: imported })" aria-hidden="true" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="p-b-30 p-t-30 text-center">{t}Are you sure?{/t}</h3>
  <h4 class="p-b-30 text-center" ng-if="!template.selected || template.selected == 1">{t}Do you want to import the item?{/t}</h4>
  <h4 class="p-b-30 text-center" ng-if="template.selected > 1">{t}Do you want to import the selected items?{/t}</h4>
  <div class="p-l-30 p-r-30 nowrap text-center">
    <strong>
      [% template.item.title %]
    </strong>
    <ul class="no-style">
      <li class="nowrap" ng-repeat="id in template.item.related">
        <small>
          &angrt;
          <i class="fa m-r-5" ng-class="{ 'fa-file-text-o': template.related[id].type === 'text', 'fa-picture-o': template.related[id].type === 'photo', 'fa-film': template.related[id].type === 'video' }"></i>
          [% template.related[id].title %]
        </small>
      </li>
    </ul>
  </div>
  <div class="row p-t-30" ng-show="template.hasTexts(template.items)">
    <div class="col-sm-6 col-sm-offset-3 form-group text-center">
      <label class="form-label text-bold" for="type">
        {t}Import{/t} {t}as{/t}
      </label>
      <div class="controls">
        <select class="form-control" id="type" name="type" ng-model="template.type">
          {is_module_activated name="ARTICLE_MANAGER"}
            <option value="Article">{t}Article{/t}</option>
          {/is_module_activated}
          {is_module_activated name="OPINION_MANAGER"}
            <option value="Opinion">{t}Opinion{/t}</option>
          {/is_module_activated}
        </select>
      </div>
    </div>
  </div>
  <div class="row" ng-show="template.hasTexts(template)">
    <div class="col-sm-6 form-group">
      <label class="form-label text-capitalize">
        {t}by{/t}
      </label>
      <div class="controls">
        <onm-author-selector class="block" default-value-text="{t}Select an author{/t}…" ng-model="template.author" placeholder="{t}Select an author{/t}…" required></onm-author-selector>
      </div>
    </div>
    <div class="col-sm-6 form-group" ng-show="template.type === 'Article'">
      <label class="form-label text-capitalize">
        {t}in{/t}
      </label>
      <div class="controls">
        <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" ng-model="template.category" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer row">
  <div class="m-t-15" ng-class="{ 'col-sm-2': template.isEditable(template), 'col-sm-6': !template.isEditable(template) }">
    <button class="btn btn-block btn-danger btn-loading" ng-click="dismiss()" type="button">
      <h5 class="text-bold text-uppercase text-white">
        <i class="fa fa-times m-r-5"></i>
        {t}No{/t}
      </h5>
    </button>
  </div>
  <div class="m-t-15" ng-class="{ 'col-sm-5': template.isEditable(template), 'col-sm-6': !template.isEditable(template) }">
    <button class="btn btn-block" ng-class="{ 'btn-success': !template.isEditable(template), 'btn-white': template.isEditable(template) }" ng-click="template.edit = 0; confirm()" ng-disabled="(template.type === 'Article' && !template.category) || (template.type === 'Opinion' && !template.author) || loading" type="button">
      <h5 class="text-bold text-uppercase" ng-class="{ 'text-white': !template.isEditable(template) }">
        <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading && !template.edit }"></i>
        {t}Yes, import and publish{/t}
      </h5>
    </button>
  </div>
  <div class="col-sm-5 m-t-15">
    <button class="btn btn-block btn-loading btn-success" ng-click="template.edit = 1; confirm()" ng-disabled="(template.type === 'Article' && !template.category) || (template.type === 'Opinion' && !template.author) || loading" ng-if="template.isEditable(template)" type="button">
      <h5 class="text-bold text-uppercase text-white">
        <i class="fa fa-edit m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading && template.edit }"></i>
        {t}Yes, import and edit{/t}
      </h5>
    </button>
  </div>
</div>
