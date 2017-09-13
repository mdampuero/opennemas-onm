{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if array_key_exists('id', $server)}{url name=backend_news_agency_server_update id=$server['id']}{else}{url name=backend_news_agency_server_create}{/if}" method="POST" autocomplete="off" id="formulario" ng-controller="NewsAgencyServerCtrl">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-microphone fa-lg"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/788682-opennemas-agencias-de-noticias" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
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
            <button class="btn btn-primary" data-text="{t}Saving{/t}..." id="save-button" type="submit">
              <span class="fa fa-save"></span>
              <span class="text">{t}Save{/t}</span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-sm-6">
        <div class="grid simple">
          <div class="grid-body">
            <div class="form-group">
              <label for="name" class="form-label">{t}Source name{/t}</label>
              <div class="controls">
                <input type="text" id="server" name="name" value="{$server['name']}" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <div class="controls">
                <div class="checkbox">
                  <input id="activated" name="activated" {if $server['activated'] != 0}checked="checked"{/if} type="checkbox" value='1'>
                  <label class="form-label" for="activated">{t}Activated{/t}</label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="color" class="form-label">{t}Color{/t}</label>
              <span class="help m-l-5">{t}Color to distinguish between other agencies{/t}</span>
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
              <div class="controls">
                <div class="radio">
                  <input class="form-control" id="external-agency" ng-model="type" ng-value="false" type="radio"/>
                  <label for="external-agency">
                    {t}External agency{/t}
                  </label>
                </div>
                <div class="radio">
                  <input class="form-control" id="opennemas-agency" ng-model="type" ng-value="true" type="radio"/>
                  <label for="opennemas-agency">
                    {t}Opennemas News Agency{/t}
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group" ng-init="url = '{$server['url']}'">
              <label for="url" class="form-label">{t}Url{/t}</label>
              <span class="help m-l-5">{t}The server url for this source. Example: ftp://server.com/path{/t}</span>
              <div class="controls">
                <input class="form-control no-animate" id="url" name="url" ng-show="!type" ng-model="url" required type="text">
                <div class="input-group no-animate ng-cloak" ng-show="type">
                  <span class="input-group-addon">
                    https://
                  </span>
                  <input class="form-control no-animate" id=instance" name="instance" ng-disabled="!type" ng-model="instance" required type="text">
                  <span class="input-group-addon">
                    .opennemas.com/ws/agency
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group" ng-init="username = '{$server['username']}'">
              <label for="username" class="form-label">{t}Username{/t}</label>
              <div class="controls">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  <input class="form-control" id="username" name="username" ng-model="username" type="text">
                </div>
              </div>
            </div>
            <div class="form-group" ng-init="password = '{$server['password']}'">
              <label class="form-label" for="password">{t}Password{/t}</label>
              <div class="controls">
                <div class="input-group">
                  <span class="input-group-btn">
                    <button class="btn btn-default check-pass">
                      <i class="fa fa-lock"></i>
                    </button>
                  </span>
                  <input class="form-control" id="password" name="password" ng-model="password" type="password">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
                <button class="btn btn-block btn-loading btn-success" ng-click="check()" type="button">
                  <span class="no-animate" ng-if="checking">
                    <i class="fa fa-absolute fa-circle-o-notch fa-spin m-l-10 m-t-10 ng-cloak"></i>
                  </span>
                  <h5 class="text-uppercase text-white">{t}Test{/t}</h5>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Sync parameters{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="form-group">
              <label for="agency_string" class="form-label">{t}Agency{/t}</label>
              <span class="help m-l-5">{t}When importing elements this will be the signature{/t}</span>
              <div class="controls">
                <input class="form-control" id="agency_string" name="agency_string" required type="text" value="{$server['agency_string']}">
              </div>
            </div>
            <div class="form-group">
              <label for="sync_from" class="form-label">{t}Sync elements newer than{/t}</label>
              <span class="help m-l-5">
                {t escape=off}Set this to you preferences to fetch elements since a fixed date.<br>Less time means faster synchronizations.{/t}
              </span>
              <div class="controls">
                <select name="sync_from" required>
                  {html_options options=$sync_from selected={$server['sync_from']}}
                </select>
              </div>
            </div>
            <div class="form-group" ng-show="auto_import === '0'">
              <div class="checkbox">
                <input id="author" name="author" type="checkbox" {if $server['author'] != 0}checked{/if} value="1">
                <label class="form-label" for="author">{t}Import authors{/t}</label>
                <span class="help m-l-5">{t}Activate this if you want to import the author of the elements if available{/t}</span>
              </div>
            </div>
            <div class="form-group" ng-hide="true">
              <div class="checkbox">
                <input id="source" name="source" {if $server['source'] != 0}checked{/if} type="checkbox" value="1">
                <label class="form-label" for="source">{t}Link to source{/t}</label>
                <span class="help m-l-5">{t}Activate this if you want to add the link to the source at the end of the body{/t}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="grid simple horizontal green">
          <div class="grid-title">
            {t}Automatic import{/t}
          </div>
          <div class="grid-body">
            <div class="form-group" ng-init="auto_import = '{$server['auto_import']}'">
              <div class="checkbox">
                <input id="auto-import" name="auto_import" ng-false-value="'0'" ng-model="auto_import" ng-true-value="'1'" type="checkbox" value="1">
                <label class="form-label" for="auto-import">{t}Enabled{/t}</label>
              </div>
            </div>
            <div ng-show="auto_import === '1'">
              <div class="form-group" ng-init="category = '{$server['category']}'; categories = {json_encode($categories)|clear_json}">
                <label class="form-label" for="category">{t}Category{/t}</label>
                <span class="help m-l-5">{t}Category to import{/t}</span>
                <div class="controls">
                  <select id="category" name="category" ng-disabled="!auto_import">
                    <option value="[% key %]" ng-repeat="(key, value) in categories" ng-selected="category === key">[% value %]</option>
                  </select>
                </div>
              </div>
              <div class="form-group" ng-init="author = '{$server['target_author']}'; authors = {json_encode($authors)|clear_json}">
                <label class="form-label" for="target-author">{t}Author{/t}</label>
                <span class="help m-l-5">{t}Author to import to{/t}</span>
                <div class="controls">
                  <select id="target-author" name="target_author" ng-disabled="!auto_import">
                    <option value="[% key %]" ng-repeat="(key, value) in authors" ng-selected="author === key">[% value %]</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <input id="import-related" {if $server['import_related'] != 0}checked{/if} name="import_related" ng-disabled="!auto_import" type="checkbox" value="1">
                  <label class="form-label" for="import-related">{t}Import related contents{/t}</label>
                  <span class="help m-l-5">{t}If possible, import all related contents for each content{/t}</span>
                </div>
              </div>
              <div class="form-group"{if !empty($server['filters'])}ng-init="filters = {json_encode($server['filters'])|clear_json}"{/if}>
                <label class="form-label">{t}Filtering{/t}</label>
                <span class="help m-l-5">{t}Filter contents that matches one or more list of words{/t}</span>
                <div class="controls">
                  <div class="row m-t-15" ng-repeat="filter in filters track by $index">
                    <div class="col-md-10 col-sm-9">
                      <input class="form-control" name="filters[]" ng-model="filter" placeholder="{t}Comma-separated list of words to match{/t}" type="text">
                    </div>
                    <div class="col-md-2 col-sm-3">
                      <button class="btn btn-block btn-success" ng-click="addFilter()" ng-if="$index === 0" type="button">
                        <i class="fa fa-plus"></i>
                      </button>
                      <button class="btn btn-block btn-danger ng-cloak" ng-click="removeFilter($index)" ng-if="$index > 0" type="button">
                        <i class="fa fa-trash-o"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}

{block name="footer-js" append}
  {javascripts}
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('.check-pass').on('click', function(e, ui){
          e.preventDefault();
          var passInput = $('#password');
          var icon = $(this).find('i');
          if (passInput.attr('type') == 'password') {
            passInput.prop('type','text');
            icon.addClass('fa-unlock');
          } else {
            passInput.prop('type','password');
            icon.removeClass('fa-unlock');
          }
        });
      });
    </script>
  {/javascripts}
{/block}
