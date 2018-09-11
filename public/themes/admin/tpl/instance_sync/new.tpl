{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script>
      jQuery(document).ready(function($) {
        $('#connect').on('click',function(e){
          e.preventDefault();
          var data = $('#formulario').serialize();

          $.ajax({
            type: 'POST',
            url: '{url name=admin_instance_sync_fetch_categories}',
            data: data,
            dataType: 'html',
            beforeSend: function() {
              $('#loading').show();
              $('.output').hide();
            }
          }).success(function(data) {
            $('#categories').html(data).show();
            $('#loading').hide();
          });
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form action="{url name=admin_instance_sync_create}" method="POST" name="formulario" id="formulario">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-exchange"></i>
                <span class="hidden-xs">{t}Instance Synchronization{/t}</span>
                <span class="visible-xs-inline-block">{t}Ins. Sync.{/t}</span>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>{t}Adding site{/t}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_instance_sync}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}..." id="save-button">
                  <span class="fa fa-save"></span>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple ">
        <div class="grid-body">
          <div class="form-group">
            <label for="site_url" class="form-label">{t}Site URL{/t}</label>
            <div class="controls">
              <input type="text" required name="site_url" id="site_url" value="{$site['site_url']}" placeholder="{t}http://example.com{/t}" class="form-control" {if !empty($site['site_url'])} readonly {/if}>
            </div>
          </div>
          <div class="form-group">
            <label for="username" class="form-label">{t}Username{/t}</label>
            <div class="controls">
              <input type="text" required id="username" name="username" value="{$site['username']}" class="form-control"/>
            </div>
          </div>
          <div class="form-group">
            <label for="password" class="form-label">{t}Password{/t}</label>
            <div class="controls">
              <div class="input-group">
                <input type="password" required id="password" name="password" value="{$site['password']}" class="form-control"/>
                <div class="input-group-btn">
                  <button class="btn check-pass" type="button" id="show-pass-button">
                    <i class="fa fa-lock"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="site_color" class="form-label">{t}Site color{/t}</label>
            <div class="input-group">
              <span class="input-group-addon" ng-style="{ 'background-color': site_color }">
                &nbsp;&nbsp;&nbsp;&nbsp;
              </span>
              <input class="form-control" colorpicker="hex" id="color" name="site_color" ng-init="site_color='{$site['site_color']|default:""}'" ng-model="site_color" type="text">
              <span class="input-group-btn">
                <button class="btn btn-default" ng-click="site_color='{$site['site_color']|default:""}'" id="reset-button">
                  {t}Reset{/t}
                </button>
              </span>
            </div>
          </div>
          <p class="col-md-12">
            <a href="#" id="connect" class="btn btn-primary pull-right pointer">{t}Connect{/t}</a>
          </p>
          <div id="categories">
            {$output}
          </div>
          <div class="spinner-wrapper" id="loading" style="display: none;">
            <div class="loading-spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
