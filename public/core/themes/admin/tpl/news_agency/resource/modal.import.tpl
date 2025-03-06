<div class="modal-body">
  <button class="close" ng-click="close({ success: imported })" aria-hidden="true" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="p-b-30 p-t-30 text-center">{t}Are you sure?{/t}</h3>
  <h4 class="p-b-30 text-center" ng-if="template.isEditable(template)">{t}Do you want to import the item?{/t}</h4>
  <h4 class="p-b-30 text-center" ng-if="!template.isEditable(template)">{t}Do you want to import the selected items?{/t}</h4>
  <div class="imported-items" style="max-height: 200px; overflow: auto;">
    <ol>
      <li class="m-t-10 p-r-30" ng-repeat="item in template.items">
        <strong class="block nowrap">
          [% item.title %]
        </strong>
        <ul class="no-style">
          <li class="nowrap" ng-repeat="id in item.related track by $index" ng-if="template.related[id]">
            <small>
              &angrt;
              <i class="fa m-r-5" ng-class="{ 'fa-file-text-o': template.related[id].type === 'text', 'fa-picture-o': template.related[id].type === 'photo', 'fa-film': template.related[id].type === 'video' }"></i>
              [% template.related[id].title %]
            </small>
          </li>
        </ul>
      </li>
    </ol>
  </div>
  <div class="row p-t-30" ng-show="template.hasTexts(template.items)">
    <div class="col-sm-6 col-sm-offset-3 form-group text-center">
      <label class="form-label text-bold" for="type">
        {t}Import{/t} {t}as{/t}
      </label>
      <div class="controls">
        <select class="form-control" id="type" name="type" ng-model="template.content_type_name">
          {is_module_activated name="ARTICLE_MANAGER"}
            <option value="article">{t}Article{/t}</option>
          {/is_module_activated}
          {is_module_activated name="OPINION_MANAGER"}
            <option value="opinion">{t}Opinion{/t}</option>
          {/is_module_activated}
        </select>
      </div>
    </div>
  </div>
  <div class="row" ng-show="template.hasTexts(template.items)">
    <div class="col-sm-6 form-group">
      <label class="form-label text-capitalize">
        {t}by{/t}
      </label>
      <div class="controls">
        <onm-author-selector class="block" default-value-text="{t}Select an author{/t}…" ng-model="template.fk_author" placeholder="{t}Select an author{/t}…" required></onm-author-selector>
      </div>
    </div>
    <div class="col-sm-6 form-group" ng-show="template.content_type_name === 'article'">
      <label class="form-label text-capitalize">
        {t}in{/t}
      </label>
      <div class="controls">
        <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" ng-model="template.fk_content_category" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
      </div>
    </div>
  </div>
  {if in_array("es.openhost.module.onmai", $app.instance->activated_modules)}
  <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-t-10">
      <div class="row">
        <div class="col-sm-12">
      <h4>
        {t}Transform with ONM AI{/t}
      </h4>
      <hr class="m-t-0 m-b-10">
      </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <select name="field" id="field" class="form-control" ng-model="template.promptSelected" ng-options="item as item.name for item in template.onmai_prompts">
              <option value="">{t}No, keep the original content{/t}</option>
            </select>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <select name="field" id="field" class="form-control" ng-model="template.toneSelected" ng-if="template.promptSelected" ng-options="item as item.name for item in template.onmai_extras.tones">
              <option value="">{t}Select a tone{/t}</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  {/if}
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
    <button class="btn btn-block" ng-class="{ 'btn-success': !template.isEditable(template), 'btn-white': template.isEditable(template) }" ng-click="template.content_status = 1; confirm()" ng-disabled="template.hasTexts(template.items) && (!template.fk_author || (template.content_type_name === 'article' && !template.fk_content_category)) || loading" type="button">
      <h5 class="text-bold text-uppercase" ng-class="{ 'text-white': !template.isEditable(template) }">
        <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading && !template.content_status }"></i>
        {t}Yes, import and publish{/t}
      </h5>
    </button>
  </div>
  <div class="col-sm-5 m-t-15" ng-if="template.isEditable(template)">
    <button class="btn btn-block btn-loading btn-success" ng-click="template.content_status = 0; confirm()" ng-disabled="!template.fk_author || (template.content_type_name === 'article' && !template.fk_content_category) || loading" type="button">
      <h5 class="text-bold text-uppercase text-white">
        <i class="fa fa-edit m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading && template.content_status }"></i>
        {t}Yes, import and edit{/t}
      </h5>
    </button>
  </div>
</div>
