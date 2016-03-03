{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_specials_config}" method="POST" id="formulario">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-home fa-lg"></i>
              {t}Specials{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Settings{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a href="{url name=admin_specials}" class="btn btn-link" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit">
                <i class="fa fa-save"></i>
                <span class="text">{t}Save{/t}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-body">
        <div class="form-group">
          <label for="special[total_widget]" class="form-label">{t}Number of elements in widget home{/t}</label>
          <span class="help">
            {t}Use  total in widget special for define how many videos can see in widgets in newspaper frontpage{/t}
          </span>
          <div class="controls">
            <input type="number" class="required" name="special_settings[total_widget]" value="{$configs['special_settings']['total_widget']|default:"2"}" />
          </div>
        </div>
        <div class="form-group">
          <label for="special[time_last]" class="form-label">{t}Time of the last special most viewed (days):{/t}</label>
          <span class="help">
            {t}Used to define the frontpage specials, the time range of the latest specials are the most viewed{/t}
          </span>
          <div class="controls">
            <input type="number" class="required" id="name" name="special_settings[time_last]" value="{$configs['special_settings']['time_last']|default:"100"}" />
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
