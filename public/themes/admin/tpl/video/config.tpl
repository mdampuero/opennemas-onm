{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_videos_config}" method="POST" name="formulario" id="formulario">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-home fa-lg"></i>
              {t}Videos{/t}
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
              <a class="btn btn-link" href="{url name=admin_videos}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
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
          <label for="video[total_widget]" class="form-label">{t}Total in widget home{/t}</label>
          <span class="help">{t}Use total in widget home to define how many videos can be seen through the widget in the newspaper frontpage{/t}</span>
          <div class="controls">
            <input type="number" name="video_settings[total_widget]" value="{$configs['video_settings']['total_widget']|default:"4"}" required />
          </div>
        </div>
        <div class="form-group">
          <label for="video[total_front]" class="form-label">{t}Total in video frontpage column{/t}</label>
          <span class="help">{t}Use total in video frontpage column to define how many videos can be seen in the left column of the category video frontpage{/t}</span>
          <div class="controls">
            <input type="number" name="video_settings[total_front]" value="{$configs['video_settings']['total_front']|default:"2"}" required />
          </div>
        </div>
        <div class="form-group">
          <label for="video[total_front_more]" class="form-label">{t}Total in video frontpage more videos{/t}</label>
          <span class="help">{t}Total number of videos on more videos section in video home frontpage{/t}</span>
          <div class="controls">
            <input type="number" name="video_settings[total_front_more]" value="{$configs['video_settings']['total_front_more']|default:"12"}" required />
          </div>
        </div>
        <div class="form-group">
          <label for="video[front_offset]" class="form-label">{t}Total offset in video frontpage more videos{/t}</label>
          <span class="help">{t}Total number of videos that are placed on top home video frontpage{/t}</span>
          <div class="controls">
            <input type="number" name="video_settings[front_offset]" value="{$configs['video_settings']['front_offset']|default:"3"}" required />
          </div>
        </div>
        <div class="form-group">
          <label for="video[total_widget]" class="form-label">{t}Total in video gallery{/t}</label>
          <span class="help">{t}Use  Total in video gallery for define how many videos can see in the gallery when you edit or create one article{/t}</span>
          <div class="controls">
            <input type="number" name="video_settings[total_gallery]" value="{$configs['video_settings']['total_gallery']|default:"20"}" required />
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
