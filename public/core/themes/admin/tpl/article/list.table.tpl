{extends file="common/extension/list.table.tpl"}

{block name="commonColumns" prepend}
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
{/block}

{block name="customColumns"}
  {acl isAllowed="ARTICLE_AVAILABLE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  {/acl}
{/block}

{block name="customColumnsHeader"}
  {acl isAllowed="ARTICLE_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  {acl isAllowed="ARTICLE_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="ARTICLE_UPDATE"}
    <a class="btn btn-default btn-small" href="[% routing.generate('backend_article_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_article_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="ARTICLE_DELETE"}
    <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
      <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
    </button>
  {/acl}
  {if !empty({setting name=webpushr field=webpushrKey})}
    <button ng-if="item.content_status && (item.endtime > item.starttime || !(item.endtime))" class="btn btn-warning btn-small" ng-click="sendWPNotification(item)" type="button">
      <i class="fa fa-bell m-r-5"></i>
    </button>
    <button ng-if="!item.content_status" class="btn btn-warning btn-small" ng-click="sendWPNotification(item)" type="button" disabled>
      <i class="fa fa-bell m-r-5"></i>
    </button>
  {/if}
  <div class="btn-group" ng-class="{ 'dropup': $index >= items.length - 1 }">
    <button class="btn btn-small btn-white dropdown-toggle" data-toggle="dropdown" type="button">
      <i class="fa fa-ellipsis-h"></i>
    </button>
    <ul class="dropdown-menu no-padding">
      <li>
        <a href="[% getFrontendUrl(item) %]" target="_blank">
          <i class="fa fa-external-link m-r-5"></i>
          {t}Link{/t}
          <span class="m-l-5" ng-if="item.params.bodyLink.length > 0">
            <small>
              ({t}External{/t})
            </small>
          </span>
        </a>
      </li>
    </ul>
  </div>
{/block}

