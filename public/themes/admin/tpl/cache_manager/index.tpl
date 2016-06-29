{extends file="base/admin.tpl"}

{block name="content"}
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
    <div class="tiles-body">
      <div class="pull-right b-grey">
        <i class="fa fa-trash-o fa-4x"></i>
      </div>
      <h4 class="alert-heading text-error semi-bold"><i class="icon-warning-sign"></i> Dangerous action!</h4>
      <p>Clean internal template files generated for this instance by pushing buttons below. <br>These actions could take some time depending on the number of present cache/compiled files.</p>
      <hr>

      <h6 class="text-error semi-bold">Smarty</h6>
      <a href="{url name=admin_cache_manager_clearcache}" class="btn btn-white btn-cons btn-large btn-block" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing Order">
        <span class="hidden-xs">{t}Remove smarty cache{/t}</span>
      </a>
      <a href="{url name=admin_cache_manager_clearcompiled}" class="btn btn-white btn-cons btn-large btn-block">
        <span class="hidden-xs">{t}Remove smarty compiles{/t}</span>
      </a>

      <h6 class="text-error semi-bold">Edge cache</h6>
      <a href="{url name=admin_cache_manager_banvarnishcache}" class="btn btn-white btn-cons btn-large btn-block">
        <span class="hidden-xs">{t}Remove varnish cache{/t}</span>
      </a>

      <h6 class="text-error semi-bold">Object cache</h6>
      <a href="#" class="btn btn-white btn-cons btn-large btn-block">
        <span class="hidden-xs">{t}Remove redis cache{/t} (not implemented)</span>
      </a>
    </div>
  </div>
</div>
</div>
</div>
{/block}
