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
              <label class="control-label" for="poll_settings[epp]">
                {t}Items per page{/t}
              </label>
              <div class="controls">
                <input name="poll_settings[epp]" type="number" value="{$configs['poll_settings']['epp']|default:10}" required/>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label" for="poll_settings[highlighted]">
                {t}Highlighted{/t}
              </label>
              <div class="controls">
                <input name="poll_settings[highlighted]" type="number" value="{$configs['poll_settings']['highlighted']|default:2}" required/>
              </div>
            </div>
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
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" id="action" name="action" value="save_config" />
  </div>
</form>
{/block}
