{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="CacheManagerCtrl" ng-init="init('caches', { type: -1 }, 'created', 'desc', 'backend_ws_cachemanager_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-database"></i>
                            {t}Cache Manager{/t}
                        </h4>
                    </li>
                </ul>
            </div>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name=admin_tpl_manager_config}" class="btn btn-link">
                            <i class="fa fa-cog fa-lg"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-navbar filters-navbar ng-cloak">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <select name="type" ng-model="type" data-label="{t}Type{/t}" id="type">
                            <option value="">{t}All types{/t}</option>
                            <option value="frontpages">{t}Frontpages{/t}</option>
                            <option value="articles">{t}Article: inner{/t}</option>
                            <option value="mobilepages">{t}Mobile: frontpages{/t}</option>
                            <option value="rss">{t}RSS{/t}</option>
                            <option value="frontpage-opinions">{t}Opinion: Authors{/t}</option>
                            <option value="opinions">{t}Opinion: inner{/t}</option>
                            <option value="video-frontpage"}>{t}Video: frontpage{/t}</option>
                            <option value="video-inner">{t}Video: inner{/t}</option>
                            <option value="gallery-frontpage">{t}Album: frontpage{/t}</option>
                            <option value="gallery-inner">{t}Album: inner{/t}</option>
                            <option value="poll-frontpage">{t}Poll: frontpage{/t}</option>
                            <option value="poll-inner">{t}Poll: inner{/t}</option>
                        </select>
                    </li>
                    <li class="quicklinks hidden-sm hidden-xs">
                      <select name="status" ng-model="pagination.epp" data-label="{t}View:{/t}" class="select2">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                      </select>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right simple-pagination">
                    <li class="quicklinks hidden-xs">
                        <span class="info">
                        [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
                        </span>
                    </li>
                    <li class="quicklinks form-inline pagination-links">
                        <div class="btn-group">
                            <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div id="caches" class="content">

        {render_messages}

        <div class="grid simple">
            <div class="grid-body no-padding">
                <div class="spinner-wrapper" ng-if="loading">
                    <div class="loading-spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
                </div>
                <div class="table-wrapper ng-cloak" ng-if="!loading">
                    <table class="table table-hover no-margin">
                        <thead>
                            <tr>
                                <th style="width:15px;">
                                    <div class="checkbox checkbox-default">
                                        <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                        <label for="select-all"></label>
                                    </div>
                                </th>
                                <th class="left">{t}Resource{/t}</th>
                                <th></th>
                                <th class="left hidden-xs" scope=col style="width:100px;">{t}Valid until{/t}</th>
                                <th class="left hidden-xs" scope=col style="width:40px;">{t}Size{/t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-if="contents.length == 0">
                                <td class="empty" colspan="4">
                                  <h4>
                                      {t escape="no"}No cache files were generated for now.{/t}
                                  </h4>
                                  <p>
                                      {t escape="no" 1=$smarty.const.SITE_URL}Visit some pages in <a href="%1" title="Visit your site">your site</a>  and come back here{/t}
                                  </p>
                                </td>
                            </tr>
                            <tr class="cache-element" ng-repeat="content in contents">
                            {* dont show frontpage parts*}
                            {* if $caches[c].resource eq 0 || $caches[c].template eq 'frontpage' *}
                                <td>
                                    <!-- <input type="checkbox" name="selected[]" value="{$smarty.section.c.index}"  class="minput"/>
                                    <input type="hidden"   name="cacheid[]"  value="{$caches[c].category}|{$caches[c].resource}" />
                                    <input type="hidden"   name="tpl[]"      value="{$caches[c].template}.tpl" /> -->

                                    <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
                                </td>
                                <td class="left">

                                    {assign var="resource" value=$caches[c].resource}

                                    <img ng-src="{$params.IMAGE_DIR}template_manager/elements/[% content.type %].png" alt="[% content.type_explanation %] cache file">
<!--
                                    {assign var="resource" value=$caches[c].resource}
                                    {* Inner Article *}
                                    {if isset($titles.$resource) && ($caches[c].template == 'article')}
                                        <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}"  target="_blank">Inner Article: {$titles.$resource|clearslash}</a>
                                    {* Frontpage mobile *}
                                    {elseif ($caches[c].template == 'mobile-article-inner')}
                                        <a href="{$smarty.const.SITE_URL}mobile/seccion/{$caches[c].category}/"  target="_blank">Frontpage mobile: {$titles.$resource}</a>
                                    {* Video inner *}
                                    {elseif isset($titles.$resource) && ($caches[c].template == 'video_inner')}
                                        <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}" target="_blank">Video inner: {$titles.$resource|clearslash}</a>
                                     {* Video frontpage *}
                                    {elseif ($caches[c].template == 'video_frontpage')}
                                        <a href="{$smarty.const.SITE_URL}video/{$caches[c].category}/" target="_blank">{t 1=$caches[c].category}Video: frontpage %1{/t}</a>
                                    {elseif ($caches[c].template == 'video_main_frontpage')}
                                        <a href="{$smarty.const.SITE_URL}video/" target="_blank">{t}Video: main frontpage{/t}</a>
                                    {* Opinion inner *}
                                    {elseif isset($titles.$resource) && ($caches[c].template == 'opinion')}
                                        {assign var="resName" value='RSS'|cat:$resource}
                                        <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}"  target="_blank">Opinion: {$titles.$resource|clearslash}</a>
                                        {elseif ($caches[c].template == 'opinion_frontpage')}
                                        <a href="{$smarty.const.SITE_URL}opinion/" target="_blank">{t 1=$caches[c].category}Frontpage %1{/t}</a>
                                        {* Opinion author index*}
                                    {elseif ($caches[c].template == 'opinion_author_index')}
                                    {assign var="authorid" value=$caches[c].category|string_format:"%d"}
                                        {capture "uriAuthor"}{generate_uri content_type="opinion_author_frontpage"
                                                    title=$allAuthors.$authorid
                                                    id=$authorid}{/capture}
                                        <a href="{$smarty.const.SITE_URL}{$smarty.capture.uriAuthor|trim}?page={$caches[c].resource}" target="_blank">
                                            {t 1=$allAuthors.$authorid}Opinion of Author "%1"{/t} {t 1=$caches[c].resource}(Page %1){/t}</a>
                                        {* Gallery frontpage *}
                                    {elseif ($caches[c].template == 'album_frontpage')}
                                        <a href="{$smarty.const.SITE_URL}album/{$caches[c].category}/" target="_blank">
                                            {if $caches[c].category neq 'home'}{t 1=$caches[c].category}Album %1{/t} {else} {t}Album Frontpage{/t} {/if}
                                        </a>
                                        {* Gallery inner *}
                                    {elseif isset($titles.$resource) && ($caches[c].template == 'album')}
                                        <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}" target="_blank">{$titles.$resource|clearslash}</a>
                                        {* Polls *}
                                    {elseif ($caches[c].template == 'poll_frontpage')}
                                        <a href="{$smarty.const.SITE_URL}encuesta/{$caches[c].category}"
                                             target="_blank">
                                            {if $caches[c].category neq 'home'}{t 1=$caches[c].category}Poll %1{/t} {else} {t}Poll: frontpage{/t} {/if}
                                        </a>
                                    {elseif isset($titles.$resource) && (($caches[c].template == 'poll') || ($caches[c].template == 'graphic_poll'))}
                                        <a href="{$contentUris.$resource}"
                                             target="_blank">{$titles.$resource|clearslash}</a>
                                        {* RSS opinion *}
                                    {elseif isset($authors.$resource)}
                                        <a href="{$smarty.const.SITE_URL}rss/opinion/{$resource|replace:"RSS":""}/"  target="_blank">{$authors.$resource|clearslash}</a>
                                        {* RSS *}
                                    {elseif $resource eq "RSS"}
                                        <a href="{if $caches[c].category != 'home'}{$smarty.const.SITE_URL}rss/{$caches[c].category}/{else}{$smarty.const.SITE_URL}rss/{/if}" target="_blank"> <strong>{t}RSS:{/t}</strong>
                                            {$ccm->getTitle($caches[c].category)|clearslash|default:"PORTADA"}
                                        </a>
                                        {* Frontpage mobile *}
                                    {elseif not isset($titles.$resource) && not isset($authors.$resource) && ($caches[c].template == 'frontpage-mobile')}
                                        <a href="{$smarty.const.SITE_URL}mobile/seccion/{$caches[c].category}/"  target="_blank">
                                            {t}Mobile frontpage: {/t}{$ccm->getTitle($caches[c].category)|clearslash|default:"Portada"}
                                        </a>
                                        {* Frontpages *}
                                    {elseif ($caches[c].template == 'frontpage')}
                                        {if $caches[c].resource eq 0}
                                        <a href="{$smarty.const.SITE_URL}seccion/{$caches[c].category}/"  target="_blank">
                                            {t}Frontpage: {/t}{$ccm->getTitle($caches[c].category)|clearslash|default:$caches[c].category}
                                        </a>
                                        {/if}
                                        {* Other kind of resources *}
                                    {elseif not isset($titles.$resource) && not isset($authors.$resource)}
                                        <a href="{$smarty.const.SITE_URL}seccion/{$caches[c].category}/{$caches[c].resource}"  target="_blank">
                                            {if $caches[c].resource gt 0}
                                            {$ccm->getTitle($caches[c].category)|clearslash|default:$caches[c].category} {t 1=$caches[c].resource}(Page %1){/t}
                                        </a>
                                    {else}
                                          {$ccm->getTitle($caches[c].category)|clearslash|default:$caches[c].category}
                                    </a>
                                    {/if} -->
                                    [% content.title %]

                                    <div class="listing-inline-actions">
                                      <button class="link link-danger delete-cache-button" ng-click="removePermanently(content)" title="{t}Delete cache file{/t}">
                                          <i class="fa fa-trash-o"></i> {t}Remove{/t}
                                      </button>
                                    </div>
                                </td>

                                <td>
                                  <a class="btn btn-white" href="{$smarty.const.SITE_URL}[% content.url %]" targeẗ="_blank"><span class="fa fa-external-link"></span></a>
                                </td>

                                <td class="left hidden-xs">
                                    <div class="valid-until-date nowrap" ng-class="[% content.expires < date() ? 'expired' : 'valid' %]">
                                        [% content.expires %]
                                    </div>
                                </td>
                                <td class="center nowrap hidden-xs">[% content.size %] KB</td>
                            {/if}
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
                  <div class="pagination-info pull-left" ng-if="contents.length > 0">
                    {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
                  </div>
                  <div class="pull-right pagination-wrapper" ng-if="contents.length > 0">
                    <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
                  </div>
                </div>

            </div>
        </div>
    </div>


    <script type="text/ng-template" id="modal-cache-batch-remove">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
          <h4 class="modal-title">
            <i class="fa fa-trash-o"></i>
            {t}Delete cache elements{/t}
        </h4>
      </div>
      <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete [% template.contents.length %] cache elements?{/t}</p>
      </div>
      <div class="modal-footer">
          <span class="loading" ng-if="deleting == 1"></span>
          <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, remove all{/t}</button>
          <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
      </div>
    </script>

    <script type="text/ng-template" id="modal-cache-remove">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
        <h4 class="modal-title">
            <i class="fa fa-trash-o"></i>
            {t}Delete cache element{/t}
        </h4>
      </div>
      <div class="modal-body">
          <p>{t}Are you sure you want to deleete the cache element [% template.cache.title %].{/t}</p>
      </div>
      <div class="modal-footer">
          <span class="loading" ng-if="deleting == 1"></span>
          <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, remove{/t}</button>
          <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
      </div>

    </script>

</div>
{/block}
{/block}
