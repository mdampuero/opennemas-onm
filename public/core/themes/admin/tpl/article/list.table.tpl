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
    {if !empty({setting name=seo_information})}
      <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('seo_information') && !hasMultilanguage()">
        <input id="seo_information" checklist-model="app.columns.selected" checklist-value="'seo_information'" type="checkbox">
        <label for="seo_information">
          {t}SEO Information{/t}
        </label>
      </div>
    {/if}
  {/acl}
{/block}

{block name="customColumnsHeader"}
  {acl isAllowed="ARTICLE_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
    {if !empty({setting name=seo_information})}
      <th class="text-center v-align-middle" ng-if="isColumnEnabled('seo_information') && !hasMultilanguage()" width="200">
        {t}SEO Score{/t}
      </th>
    {/if}
  {/acl}
{/block}

{block name="customColumnsBody"}
  {acl isAllowed="ARTICLE_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
      </button>
    </td>
    {if !empty({setting name=seo_information})}
      <td class="text-center v-align-middle" ng-if="isColumnEnabled('seo_information') && !hasMultilanguage()">
        <span ng-if="item.text_complexity" class="ng-cloak badge badge-default" ng-class="{ 'badge-danger': item.text_complexity <= 40, 'badge-warning': item.text_complexity > 40 &amp;&amp; item.text_complexity <=60, 'badge-success' : item.text_complexity >60 }">
          <strong>
            [% item.text_complexity %]/100
          </strong>
        </span>
        <small class="text-italic" ng-if="!item.text_complexity">
          &lt;{t}No SEO information{/t}&gt;
        </small>
      </td>
    {/if}
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="ARTICLE_UPDATE"}
    <a class="btn btn-white btn-small" href="[% routing.generate('backend_article_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
      <i class="fa fa-pencil text-success_"></i>
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_article_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" class="btn-group" class="btn-group" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="ARTICLE_ADMIN"}
    <button class="btn btn-white btn-small" ng-click="createCopy(item)" type="button" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Duplicate{/t}" tooltip-placement="top">
      <i class="fa fa-copy"></i>
    </button>
  {/acl}
  {is_module_activated name="es.openhost.module.webpush_notifications"}
    {if !empty({setting name=webpush_apikey}) && empty({setting name=webpush_automatic})}
      <button ng-if="!hasMultilanguage() && item.content_status && (!item.starttime || (item.starttime <= currentDateTime))" class="btn btn-white btn-small" ng-click="sendWPNotification(item)" type="button" uib-tooltip="{t}Send notification{/t}" tooltip-placement="top">
        <i class="fa fa-bell"></i>
      </button>
      <button ng-if="!hasMultilanguage() && (!item.content_status || (item.content_status && item.starttime > currentDateTime))" class="btn btn-white btn-small" ng-click="sendWPNotification(item)" type="button" uib-tooltip="{t}Send notification{/t}" tooltip-placement="top" disabled>
        <i class="fa fa-bell"></i>
      </button>
    {/if}
  {/is_module_activated}
  <a ng-if="item.slug" class="btn btn-white btn-small" href="[% getFrontendUrl(item) %]" target="_blank" uib-tooltip="{t}Link{/t}" tooltip-placement="top">
    <i class="fa fa-external-link"></i>
  </a>
    <a ng-if="!item.slug" class="btn btn-white btn-small" disabled>
    <i class="fa fa-external-link"></i>
  </a>
  {acl isAllowed="ARTICLE_DELETE"}
    <button class="btn btn-white btn-small" ng-click="sendToTrash(item)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
      <i class="fa fa-trash-o text-danger"></i>
    </button>
  {/acl}
{/block}

