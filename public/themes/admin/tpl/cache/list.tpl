{extends file="base/admin.tpl"}

{block name="content"}
<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <i class="fa fa-database"></i>
            Cache Manager
          </h4>
        </li>
      </ul>
    </div>
    <div class="all-actions pull-right">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <a class="btn btn-link" href="{url name=admin_cache_manager_config}">
            <i class="fa fa-cog fa-lg"></i>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content">
  <div class="tiles white m-20">
    <div class="tiles-body container-fluid">
      <div class="pull-right b-grey">
        <i class="fa fa-trash-o fa-4x"></i>
      </div>
      <h4 class="alert-heading text-error semi-bold"><i class="icon-warning-sign"></i> Dangerous actions!</h4>
      <p>Clean <strong>edge cache, object and templating files</strong> generated for this instance by pushing buttons below. <br>These actions could take some time depending on the number of present cache/compiled files.</p>
      <hr>
      <a href="{url name=admin_cache_manager_clearcache}" class="btn btn-danger btn-cons btn-large btn-block" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing Order">
        Remove ALL cache <span class="hidden-xs"><small>(Smarty compiles/cache, Varnish{if $redis_enabled}, Redis{/if})</small></span>
      </a>
      <hr>

      <h6 class="text-error semi-bold">Smarty</h6>
      <div class="row">
        <div class="col-xs-6">
          <a href="{url name=admin_cache_manager_clearcache}" class="btn btn-white btn-cons btn-large btn-block" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing Order">
          Remove smarty cache
        </a>
        </div>
        <div class="col-xs-6">
          <a href="{url name=admin_cache_manager_clearcompiled}" class="btn btn-white btn-cons btn-large btn-block">
            Remove smarty compiles
          </a>
        </div>
      </div>

      <h6 class="text-error semi-bold">Edge cache</h6>
      <a href="{url name=admin_cache_manager_banvarnishcache}" class="btn btn-white btn-cons btn-large btn-block">
        Remove varnish cache
      </a>

      <h6 class="text-error semi-bold">Object cache</h6>
      {if $redis_enabled}
      <a href="{url name=admin_cache_manager_clearrediscache}" class="btn btn-white btn-cons btn-large btn-block">
        Remove redis cache
      </a>
      {else}
      <span class="text-center">
        This action is not available as Opennemas is not using Redis cache.
      </span>
      {/if}
    </div>
  </div>
</div>
</div>
</div>
{/block}
