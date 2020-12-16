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
      {if !empty($pages)}
        <option value="internal">{t}Module{/t}</option>
      {/if}
      {if !empty($categories)}
        <option value="category">{t}Categories{/t}</option>
      {/if}
      {if !empty($categories)}
        <option value="blog-category">{t}Category blog{/t}</option>
      {/if}
      {if !empty($static_pages)}
        <option value="static">{t}Static Page{/t}</option>
      {/if}
      {is_module_activated name="SYNC_MANAGER"}
        {if !empty($sync_sites)}
          <option value="syncBlogCategory">{t}Synchronized Category{/t}</option>
        {/if}
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
  {if !empty($categories)}
    <div ng-if="type == 'category'" ng-init="categories = {json_encode($categories)|clear_json}">
      <div class="form-group" ng-repeat="category in categories">
        <div class="checkbox col-md-6">
          <input id="checkbox-frontpage-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
          <label for="checkbox-frontpage-[% $index %]">
            [% category.title %]
          </label>
        </div>
      </div>
    </div>
  {/if}
  {if !empty($pages)}
    <div ng-if="type == 'internal'" ng-init="pages = {json_encode($pages)|clear_json}">
      <div class="form-group" ng-repeat="page in pages">
        <div class="checkbox col-md-6">
          <input id="checkbox-module-[% $index %]" checklist-model="selected" checklist-value="page" type="checkbox">
          <label for="checkbox-module-[% $index %]">
            [% page.title %]
          </label>
        </div>
      </div>
    </div>
  {/if}
  {if !empty($static_pages)}
    <div ng-if="type == 'static'" ng-init="staticPages = {json_encode($static_pages)|clear_json}">
      <div class="form-group" ng-repeat="page in staticPages">
        <div class="checkbox col-md-6">
          <input id="checkbox-static-pages-[% $index %]" checklist-model="selected" checklist-value="page" type="checkbox">
          <label for="checkbox-static-pages-[% $index %]">
            [% page.title %]
          </label>
        </div>
      </div>
    </div>
  {/if}
  {if !empty($categories)}
    <div ng-if="type == 'blog-category'" ng-init="automaticCategories = {json_encode($categories)|clear_json}">
      <div class="form-group" ng-repeat="category in automaticCategories">
        <div class="checkbox col-md-6">
          <input id="checkbox-poll-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
          <label for="checkbox-poll-[% $index %]">
            [% category.title %]
          </label>
        </div>
      </div>
    </div>
  {/if}
  {is_module_activated name="SYNC_MANAGER"}
   {if !empty($sync_sites)}
    <div ng-if="type == 'syncBlogCategory'" ng-init="elements = {json_encode($sync_sites)|clear_json}">
      <div ng-repeat="(site, params) in elements" ng-init="siteIndex=$index">
        <h5>[% site %]</h5>
        <div class="form-group" ng-repeat="category in params.categories">
          <div class="checkbox col-md-6">
            <input id="checkbox-poll-[% siteIndex %]_[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
            <label for="checkbox-poll-[% siteIndex %]_[% $index %]">
              [% category %]
            </label>
          </div>
        </div>
      </div>
    </div>
   {/if}
  {/is_module_activated}
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">{t}Close{/t}</button>
  <button type="button" class="btn btn-primary" ng-click="addItem()">{t}Add{/t}</button>
</div>
