{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_videos_config}" method="POST" name="formulario" id="formulario">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-film m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_videos_list}">
                  {t}Videos{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>{t}Settings{/t}</h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <button class="btn btn-success text-uppercase" data-text="{t}Saving{/t}..." type="submit">
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
          <label for="video[total_front_more]" class="form-label">{t}Number of videos in frontpage{/t}</label>
          <span class="help">{t}Total number of videos to show per page in the frontpage{/t}</span>
          <div class="controls">
            <input type="number" name="video_settings[total_front_more]" value="{$configs['video_settings']['total_front_more']|default:"12"}" required />
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
