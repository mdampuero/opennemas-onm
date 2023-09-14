{extends file="common/extension/list.table.tpl"}

{* {block name="commonColumns" prepend}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-frontpage" checklist-model="app.columns.selected" checklist-value="'featured_frontpage'" type="checkbox">
    <label for="checkbox-featured-frontpage">
      {t}Featured in frontpage{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-inner" checklist-model="app.columns.selected" checklist-value="'featured_inner'" type="checkbox">
    <label for="checkbox-featured-inner">
      {t}Featured in inner{/t}
    </label>
  </div>
  {is_module_activated name="es.openhost.module.live_blog_posting"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-live-blog-posting" checklist-model="app.columns.selected" checklist-value="'live_blog_posting'" type="checkbox">
      <label for="checkbox-live-blog-posting">
        {t}Live post{/t}
      </label>
    </div>
  {/is_module_activated}
  {is_module_activated name="es.openhost.module.google_news_showcase"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-google-news-showcase" checklist-model="app.columns.selected" checklist-value="'google_news_showcase'" type="checkbox">
      <label for="checkbox-google-news-showcase">
        {t}Showcase{/t}
      </label>
    </div>
  {/is_module_activated}
{/block}

{block name="commonColumnsHeader" prepend}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('featured_frontpage')" width="120">
    {t}Frontpage{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('featured_inner')" width="120">
    {t}Inner{/t}
  </th>
  {is_module_activated name="es.openhost.module.live_blog_posting"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('live_blog_posting')" width="80">
      {t}Live post{/t}
    </th>
  {/is_module_activated}
  {is_module_activated name="es.openhost.module.google_news_showcase"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('google_news_showcase')" width="120">
      {t}Showcase{/t}
    </th>
  {/is_module_activated}
{/block}

{block name="commonColumnsBody" prepend}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('featured_frontpage')">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" type="featured_frontpage" ng-model="item" only-image="true" transform="zoomcrop,220,220">
      <div class="badge badge-default text-bold text-uppercase">
        {include file="common/component/icon/content_type_icon.tpl" iField="getFeaturedMedia(item, 'featured_frontpage')" iFlagName=true iFlagIcon=true}
      </div>
    </dynamic-image>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('featured_inner')">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" type="featured_inner" ng-model="item" only-image="true" transform="zoomcrop,220,220">
      <div class="badge badge-default text-bold text-uppercase">
        {include file="common/component/icon/content_type_icon.tpl" iField="getFeaturedMedia(item, 'featured_inner')" iFlagName=true iFlagIcon=true}
      </div>
    </dynamic-image>
  </td>
  {is_module_activated name="es.openhost.module.live_blog_posting"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('live_blog_posting')">
      <i class="fa fa-podcast fa-2x" ng-if="item.live_blog_posting"></i>
    </td>
  {/is_module_activated}
  {is_module_activated name="es.openhost.module.google_news_showcase"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('google_news_showcase')">
      <i class="fa fa-google fa-2x" ng-if="item.showcase"></i>
    </td>
  {/is_module_activated}
{/block} *}

{block name="customColumns"}
    <div class="checkbox column-filters-checkbox">
      <input id="image" checklist-model="app.columns.selected" checklist-value="'image'" type="checkbox">
      <label for="image">
        {t}Image{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="status" checklist-model="app.columns.selected" checklist-value="'status'" type="checkbox">
      <label for="status">
        {t}Status{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="send_date" checklist-model="app.columns.selected" checklist-value="'send_date'" type="checkbox">
      <label for="send_date">
        {t}Send date{/t}
      </label>
    </div>
{/block}

{block name="customColumnsHeader"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('image')" width="200">
      {t}Image{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('status')" width="200">
      {t}Status{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('send_date')" width="200">
      {t}Send date{/t}
    </th>
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('image')">
 <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.image" transform="zoomcrop,220,220">
      {* <div class="badge badge-default text-bold text-uppercase">
        {include file="common/component/icon/content_type_icon.tpl" iField="getFeaturedMedia(item, 'featured_frontpage')" iFlagName=true iFlagIcon=true}
      </div> *}
    </dynamic-image>
    [% item.image %]
  </td>
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('status')">
      <span class="ng-cloak badge badge-default" ng-class="{ 'badge-danger': item.status == 2, 'badge-warning': item.status == 0, 'badge-success' : item.status == 1 }">
        <strong ng-if="item.status == 0">
          {t}Scheduled{/t}
        </strong>
        <strong ng-if="item.status == 1">
          {t}Sent{/t}
        </strong>
        <strong ng-if="item.status == 2">
          {t}Error{/t}
        </strong>
      </span>
    </td>
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('send_date')">
      <span class="ng-cloak badge badge-default">
        <strong>
          [% item.send_date %]
        </strong>
      </span>
    </td>
{/block}


