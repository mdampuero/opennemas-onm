{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=backend_newsstands_config}" method="POST">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-newspaper-o m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <a class="no-padding" href="{url name=backend_newsstands}" title="{t}Go back to list{/t}">
              <h4>
                {t}Covers{/t}
              </h4>
            </a>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <h4>{t}Settings{/t}</h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <button type="submit" class="btn btn-primary">
                <span class="fa fa-save"></span>
                {t}Save{/t}
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
          <label for="kiosko_settings[orderFrontpage]" class="form-label">{t}Order newsstand frontpage by{/t}</label>
          <div class="help">
            {t}Select if order newsstand's frontpage by dates or by section.{/t} <br>
            {t}Grouped by date is recommended for weekly newspapers.{/t}
          </div>
          <div class="controls">
            <select name="kiosko_settings[orderFrontpage]" id="kiosko_settings[orderFrontpage]" class="required">
              <option value="sections" {if $configs['kiosko_settings']['orderFrontpage'] eq "sections"} selected {/if}>{t}Sections{/t}</option>
              <option value="dates" {if $configs['kiosko_settings']['orderFrontpage'] eq "dates"} selected {/if}>{t}Dates{/t}</option>
              <option value="grouped" {if $configs['kiosko_settings']['orderFrontpage'] eq "grouped"} selected {/if}>{t}Grouped by date{/t}</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
