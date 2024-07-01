{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-picture" checklist-model="app.columns.selected" checklist-value="'picture'" type="checkbox">
    <label for="checkbox-picture">
      {t}Picture{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="app.columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-email" checklist-model="app.columns.selected" checklist-value="'email'" type="checkbox">
    <label for="checkbox-email">
      {t}Email{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-description" checklist-model="app.columns.selected" checklist-value="'description'" type="checkbox">
    <label for="checkbox-description">
      {t}Biography{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-slug" checklist-model="app.columns.selected" checklist-value="'slug'" type="checkbox">
    <label for="checkbox-slug">
      {t}Slug{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-blog" checklist-model="app.columns.selected" checklist-value="'blog'" type="checkbox">
    <label for="checkbox-blog">
      {t}Blog{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-biography" checklist-model="app.columns.selected" checklist-value="'biography'" type="checkbox">
    <label for="checkbox-biography">
     {t}Short Biography{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('picture')" width="80">
    <i class="fa fa-picture-o"></i>
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('name')" width="400">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('email')" width="400">
    {t}Email{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('description')" width="400">
    {t}Biography{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('slug')" width="400">
    {t}Slug{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('blog')" width="80">
    {t}Blog{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('biography')" width="400">
    {t}Short Biography{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('picture')">
    <dynamic-image class="img-thumbnail img-thumbnail-circle" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[item.avatar_img_id].path" transform="thumbnail,50,50"></dynamic-image>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('name')">
    <div class="table-text">
      [% item.name %]
    </div>
    {block name="itemActions"}
      <div class="listing-inline-actions m-t-10 btn-group">
        {acl isAllowed="AUTHOR_UPDATE"}
          <a class="btn btn-white btn-small" href="[% routing.generate('backend_author_show', { id:  item.id }) %]" title="{t}Edit{/t}" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
            <i class="fa fa-pencil"></i>
          </a>
        {/acl}
        {acl isAllowed="AUTHOR_DELETE"}
          <button class="btn btn-white btn-small" ng-click="delete(item.id)" ng-if="backup.master || item.id != backup.id" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        {/acl}
      </div>
    {/block}
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('email')">
    <div class="table-text">
      [% item.email%]
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('description')">
    <div class="table-text">
      [% item.bio_description%]
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('slug')">
    <div class="table-text">
      [% item.slug%]
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('blog')">
    <i class="fa" ng-class="{ 'fa-check text-success': item.is_blog == 1, 'fa-times text-danger': !item.is_blog || item.is_blog == 0 }"></i>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('biography')">
    <div class="table-text" ng-if="item.bio">
      <span ng-if="item.is_blog == 1">
        <strong>Blog:</strong>
      </span>
      [% item.bio %]
    </div>
    <span class="text-italic" ng-if="!item.bio">
      {t}No biography{/t}
    </span>
  </td>
{/block}
