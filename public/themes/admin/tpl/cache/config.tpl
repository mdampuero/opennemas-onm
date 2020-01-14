{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Cache{/t} > Smarty
{/block}

{block name="ngInit"}
  ng-controller="CacheConfigCtrl" ng-init="getConfig()"
{/block}

{block name="icon"}
  <i class="fa fa-database m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_cache_list}">
    {t}Cache{/t}
  </a>
{/block}

{block name="extraTitle"}
  <li class="quicklinks m-l-5 m-r-5">
    <h4>
      <i class="fa fa-angle-right"></i>
    </h4>
  </li>
  <li class="quicklinks">
    <h4>
      Smarty
    </h4>
  </li>
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <button class="btn btn-loading btn-success text-uppercase" ng-click="updateConfig()" type="button">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
      {t}Save{/t}
    </button>
  </li>
{/block}

{block name="filters"}{/block}

{block name="list"}
  {include file="cache/config.table.tpl"}
{/block}
