{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=admin_categories_config}" method="POST" name="formulario" id="formulario">
  <form method="post" name="formulario" ng-controller="ArticleConfCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-home fa-lg"></i>
                {t}Articles{/t}
              </h4>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <h5>{t}Settings{/t}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_categories}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content panel">
      <div class="grid simple">
        <div class="grid-body">
          <div class="form-group">
            <label for="section_settings[allowLogo]" class="form-label">{t}Change image headers and colors in frontpages{/t}</label>
            <div class="help">{t}Enable this if you want to use a custom image and color in your category frontpages{/t}</div>
            <div class="controls">
              <select name="section_settings[allowLogo]" id="section_settings[allowLogo]" class="required">
                <option value="0">{t}No{/t}</option>
                <option value="1" {if $configs['section_settings']['allowLogo'] eq "1"} selected {/if}>{t}Yes{/t}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <input type="hidden" id="action" name="action" value="save_config" />
    </div>
  </form>
{/block}
