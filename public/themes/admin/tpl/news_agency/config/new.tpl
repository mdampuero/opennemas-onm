{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if array_key_exists('id', $server)}{url name=backend_news_agency_server_update id=$server['id']}{else}{url name=backend_news_agency_server_create}{/if}" method="POST" autocomplete="off" id="formulario">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-microphone fa-lg"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/788682-opennemas-agencias-de-noticias" target="_blank" tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
              {t}News agency{/t}
            </h4>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <h5>
              {if array_key_exists('id', $server)}
                {t}Update source{/t}
              {else}
                {t}Add source{/t}
              {/if}
            </h5>
          </li>
        </ul>
      </div>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" href="{url name=backend_news_agency_servers_list}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
              <span class="fa fa-reply"></span>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}...">
              <span class="fa fa-save"></span>
              <span class="text">{t}Save{/t}</span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="content">

    <div class="grid simple">
      <div class="grid-body">

        <div class="form-group">
          <label for="name" class="form-label">{t}Source name{/t}</label>
          <div class="controls">
            <input type="text" id="server" name="name" value="{$server['name']}" class="form-control" required="required"/>
          </div>
        </div>

        <div class="form-group">
          <label for="activated" class="form-label">{t}Activated{/t}</label>
          <div class="controls">
            <div class="slide-primary">
              <input type="checkbox" name="activated" class="ios" {if $server['activated'] != 0}checked="checked"{/if} value='1' />
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="color" class="form-label">{t}Color{/t}</label>
          <span class="help">{t}Color to distinguish between other agencies{/t}</span>
          <div class="controls">
            <div class="input-group">
              <span class="input-group-addon" ng-style="{ 'background-color': color }">
                &nbsp;&nbsp;&nbsp;&nbsp;
              </span>
              <input class="form-control" colorpicker="hex" id="color" name="color" ng-init="color='{$server['color']|default:"" }'" ng-model="color" type="text">
              <div class="input-group-btn">
                <button class="btn btn-default" ng-click="color='{$server['color']|default:""}'" type="button">{t}Reset{/t}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid simple">
      <div class="grid-title">
        <h4>{t}Connection{/t}</h4>
      </div>
      <div class="grid-body">

        <div class="form-group">
          <label for="url" class="form-label">{t}Url{/t}</label>
          <span class="help">{t}The server url for this source. Example: ftp://server.com/path{/t}</span>
          <div class="controls">
            <input type="text" id="server" name="url" value="{$server['url']}" class="form-control" required="required"/>
          </div>
        </div>

        <div class="form-group">
          <label for="username" class="form-label">{t}Username{/t}</label>
          <div class="controls">
            <input type="text" id="username" name="username" value="{$server['username']}" class="form-control"/>
          </div>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">{t}Password{/t}</label>
          <div class="controls">
            <input type="password" id="password" name="password" value="{$server['password']}" class="form-control"/>
            <button class="check-pass btn">{t}Show password{/t}</button>
          </div>
        </div>
      </div>
    </div>

    <div class="grid simple">
      <div class="grid-title">
        <h4>{t}Sync parameters{/t}</h4>
      </div>
      <div class="grid-body">

        <div class="form-group">
          <label for="agency_string" class="form-label">{t}Agency{/t}</label>
          <span class="help">{t}When importing elements this will be the signature{/t}</span>
          <div class="controls">
            <input type="text" id="agency_string" name="agency_string" value="{$server['agency_string']}" class="form-control" required="required"/>
          </div>
        </div>

        <div class="form-group">
          <label for="author" class="form-label">{t}Import authors{/t}</label>
          <span class="help">{t}Activate this if you want to import the author of the elements if available{/t}</span>
          <div class="controls">
            <input name="author" type="checkbox" {if $server['author'] != 0}checked{/if} value='1'>
          </div>
        </div>

        <div class="form-group">
          <label for="sync_from" class="form-label">{t}Sync elements newer than{/t}</label>
          <span class="help">
            {t escape=off}Set this to you preferences to fetch elements since a fixed date.<br>Less time means faster synchronizations.{/t}
          </span>
          <div class="controls">
            <select name="sync_from" required="required">
              {html_options options=$sync_from selected={$server['sync_from']}}
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

</form>
{/block}

{block name="footer-js" append}
  {javascripts src="@AdminTheme/js/jquery/jquery_simplecolorpicker/jquery.simplecolorpicker.js"}
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('.check-pass').on('click', function(e, ui){
          e.preventDefault();
          var passInput = $('#password');
          var btn = $(this);
          if (passInput.attr('type') == 'password') {
            passInput.prop('type','text');
            btn.html('{t}Hide password{/t}');
          } else {
            passInput.prop('type','password');
            btn.html('{t}Show password{/t}');
          }
        });
      });
    </script>
  {/javascripts}
{/block}
