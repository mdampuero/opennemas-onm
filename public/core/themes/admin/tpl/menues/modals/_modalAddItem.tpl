<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
  <h4 class="modal-title">{t}Add new item{/t}</h4>
</div>
<div class="modal-body clearfix">
  <div class="form-group">
    <label class="form-label" for="item-type">
      {t}Type{/t}
    </label>
    <select class="form-control" id="item-type" ng-model="type">
      <option value="external">{t}External link{/t}</option>

        <option ng-if="template.data.pages" value="internal" ng-cloak>{t}Module{/t}</option>

        <option  ng-if="template.data.categories" value="category">{t}Categories{/t}</option>

        <option ng-if="template.data.categories"  value="blog-category">{t}Category blog{/t}</option>

        <option ng-if="template.data.static_pages" value="static">{t}Static Page{/t}</option>

      {is_module_activated name="SYNC_MANAGER"}

        <option value="syncBlogCategory" ng-if="template.data.sync_sites">{t}Synchronized Category{/t}</option>

      {/is_module_activated}
    </select>
  </div>
  <div ng-if="type == 'external'">
    <p>{t}Fill the below form with the title and the external URL you want to add to the menu.{/t}</p>
    <div class="form-group">
      <label class="form-label" for="external-link-title">
        {t}Title{/t}
      </label>
      <div class="controls">
        <input class="form-control" id="external-link-title" name="external-link-title" ng-model="$parent.externalLinkTitle" type="text">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="external-link-url">
        {t}URL{/t}
      </label>
      <div class="controls">
        <input class="form-control" id="external-link-url" name="external-link-url" ng-model="$parent.externalLinkUrl" type="text">
      </div>
    </div>
  </div>

    <div ng-if="type == 'category' && template.data.categories">
      {* <div class="form-group" ng-repeat="category in template.data.categories">
        <div class="checkbox col-md-6">
          <input id="checkbox-frontpage-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
          <label for="checkbox-frontpage-[% $index %]">
            [% category.title %]
          </label>
        </div>
      </div> *}
      <div class="controls">
        <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" export-model="selectedCategory" locale="template.data.locale.selected" name="template.data.category_id" ng-model="template.data.categories[0]" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
      </div>
    </div>


    <div ng-if="type == 'internal' && template.data.pages">
      <div class="form-group" ng-repeat="page in template.data.pages">
        <div class="checkbox col-md-6">
          <input id="checkbox-module-[% $index %]" checklist-model="selected" checklist-value="page" type="checkbox">
          <label for="checkbox-module-[% $index %]">
            [% page.title %]
          </label>
        </div>
      </div>
    </div>


    <div ng-if="type == 'static' && template.data.static_pages">
      <div class="form-group" ng-repeat="page in template.data.static_pages">
        <div class="checkbox col-md-6">
          <input id="checkbox-static-pages-[% $index %]" checklist-model="selected" checklist-value="page" type="checkbox">
          <label for="checkbox-static-pages-[% $index %]">
            [% page.title %]
          </label>
        </div>
      </div>
    </div>


    <div ng-if="type == 'blog-category' && template.data.categories">
      <div class="form-group" ng-repeat="category in template.data.categories">
        <div class="checkbox col-md-6">
          <input id="checkbox-poll-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
          <label for="checkbox-poll-[% $index %]">
            [% category.title %]
          </label>
        </div>
      </div>
    </div>

  {is_module_activated name="SYNC_MANAGER"}
    <div ng-if="type == 'syncBlogCategory' && template.data.sync_sites">
      <div ng-repeat="(site, params) in template.data.sync_sites" ng-init="siteIndex=$index">
        <h5>[% site %]</h5>
        <div class="form-group" ng-repeat="category in template.data.categories">
          <div class="checkbox col-md-6">
            <input id="checkbox-poll-[% siteIndex %]_[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
            <label for="checkbox-poll-[% siteIndex %]_[% $index %]">
              [% category %]
            </label>
          </div>
        </div>
      </div>
    </div>
  {/is_module_activated}
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">{t}Close{/t}</button>
  <button type="button" class="btn btn-primary" ng-click="addItem()">{t}Add{/t}</button>
</div>
