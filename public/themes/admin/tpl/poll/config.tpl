{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_polls_config}" method="POST" name="formulario" id="formulario" {$formAttrs}>
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-pie-chart"></i>
              {t}Polls{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Settings{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_polls}" title="{t}Go back to list{/t}" value="{t}Go back to list{/t}">
                <i class="fa fa-reply"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="save-button">
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
    <div class="row">
      <div class="col-md-12">
        <div class="grid simple">
          <div class="grid-title">
            <h4>
              {t}Poll section preferences{/t}
            </h4>
          </div>
          <div class="grid-body">
            <div class="form-group">
              <label class="control-label" for="poll_settings[typeValue]">
                {t}Values type{/t}
              </label>
              <div class="controls">
                <select name="poll_settings[typeValue]" id="poll_settings[typeValue]" class="required">
                  <option value="percent" {if $configs['poll_settings']['typeValue'] eq 'percent'} selected {/if}>{t}Percents{/t}</option>
                  <option value="vote" {if $configs['poll_settings']['typeValue'] eq 'vote'} selected {/if}>{t}Vote count{/t}</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="poll[heightPoll]">
                    {t}Charts height{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" name="poll_settings[heightPoll]" type="number" value="{$configs['poll_settings']['heightPoll']|default:"500"}" />
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="poll[widthPoll]">
                    {t}Charts width{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" name="poll_settings[widthPoll]" type="number" value="{$configs['poll_settings']['widthPoll']|default:"600"}" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Poll home widget preferences{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="form-group">
              <label class="form-label" for="poll[total_widget]">
                {t}Elements in frontpage widget{/t}
              </label>
              <div class="controls">
                <input name="poll_settings[total_widget]" type="number" value="{$configs['poll_settings']['total_widget']|default:"1"}" required/>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="poll[widthWidget]">
                    {t}Chart width{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" name="poll_settings[widthWidget]" type="number" value="{$configs['poll_settings']['widthWidget']|default:"240"}" required/>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="poll[heightWidget]">{t}Chart height{/t}</label>
                  <div class="controls">
                    <input class="form-control" id="name" name="poll_settings[heightWidget]" type="number" value="{$configs['poll_settings']['heightWidget']|default:"240"}" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" id="action" name="action" value="save_config" />
  </div>
</form>
{/block}
