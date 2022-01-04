{block name="fields"}
  <div class="column-filters-toggle ng-cloak" ng-click="app.fields.collapsed = !app.fields.collapsed" ng-if="!flags.http.loading && !noFields">
    <span class="column-filters-ellipsis"><i class="fa fa-lg " ng-class="{ 'fa-angle-down': app.fields.collapsed, 'fa-angle-up': !app.fields.collapsed }"></i></span>
  </div>
  <div class="column-filters collapsed ng-cloak" ng-class="{ 'collapsed': app.fields.collapsed }" ng-if="!flags.http.loading">
    <h5>{t}Expand{/t}</h5>
    <div>
    {block name="commonFields"}
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('author')">
        <input id="checkbox-author" checklist-model="app.fields[contentKey].selected" checklist-value="'author'" type="checkbox">
        <label for="checkbox-author">
          {t}Author{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('category')">
        <input id="checkbox-category" checklist-model="app.fields[contentKey].selected" checklist-value="'category'" type="checkbox">
        <label for="checkbox-category">
          {t}Category{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('tags')">
        <input id="checkbox-tags" checklist-model="app.fields[contentKey].selected" checklist-value="'tags'" type="checkbox">
        <label for="checkbox-tags">
          {t}Tags{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('slug')">
        <input id="checkbox-slug" checklist-model="app.fields[contentKey].selected" checklist-value="'slug'" type="checkbox">
        <label for="checkbox-slug">
          {t}Slug{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('bodyLink')">
        <input id="checkbox-bodyLink" checklist-model="app.fields[contentKey].selected" checklist-value="'bodyLink'" type="checkbox">
        <label for="checkbox-bodyLink">
          {t}External link{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('schedule')">
        <input id="checkbox-schedule" checklist-model="app.fields[contentKey].selected" checklist-value="'schedule'" type="checkbox">
        <label for="checkbox-schedule">
          {t}Schedule{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('lists')">
        <input id="checkbox-lists" checklist-model="app.fields[contentKey].selected" checklist-value="'lists'" type="checkbox">
        <label for="checkbox-lists">
          {t}Lists{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('featuredFrontpage')">
        <input id="checkbox-featuredFrontpage" checklist-model="app.fields[contentKey].selected" checklist-value="'featuredFrontpage'" type="checkbox">
        <label for="checkbox-featuredFrontpage">
          {t}Featured in frontpage{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('featuredInner')">
        <input id="checkbox-featuredInner" checklist-model="app.fields[contentKey].selected" checklist-value="'featuredInner'" type="checkbox">
        <label for="checkbox-featuredInner">
          {t}Featured in inner{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('relatedFrontpage')">
        <input id="checkbox-relatedFrontpage" checklist-model="app.fields[contentKey].selected" checklist-value="'relatedFrontpage'" type="checkbox">
        <label for="checkbox-relatedFrontpage">
          {t}Related in frontpage{/t}
        </label>
      </div>
      <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('relatedInner')">
        <input id="checkbox-relatedInner" checklist-model="app.fields[contentKey].selected" checklist-value="'relatedInner'" type="checkbox">
        <label for="checkbox-relatedInner">
          {t}Related in inner{/t}
        </label>
      </div>
    {/block}
    {block name="customFields"}{/block}
    </div>
  </div>
{/block}
