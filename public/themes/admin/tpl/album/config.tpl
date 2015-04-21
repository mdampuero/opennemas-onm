{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_albums_config}" method="POST">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}Albums{/t}
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
                            <a class="btn btn-link" href="{url name=admin_albums}" >
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i>
                                {t}Save{/t}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
<div class="content">

    {render_messages}

    <div class="grid simple">
        <div class="grid-body">

            <div class="form-group">
                <label for="album_settings_total_widget" class="form-label">{t}Total in widget home{/t}</label>
                <span class="help">{t}Use  total in widget album for define how many albums can see in widgets in frontpage{/t}</span>
                <div class="controls">
                    <input type="number" name="album_settings_total_widget" id="album_settings_total_widget" value="{$configs['album_settings']['total_widget']|default:"4"}" required/>
                </div>
            </div>

            <div class="form-group">
                <label for="album_settings_crop_width" class="form-label">{t}Cover size in widget album (width x height){/t}</label>
                <div class="controls">
                    <div class="form-inline-block">
                        <input type="number" id="name" name="album_settings_crop_width" value="{$configs['album_settings']['crop_width']|default:"300"}" required />
                        x
                        <input type="number" id="name" name="album_settings_crop_height" value="{$configs['album_settings']['crop_height']|default:"240"}" required />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="album_settings_orderFrontpage" class="form-label">{t}Order album's frontpage by{/t}</label>
                <span class="help">{t}Select if order album's frontpage by most views or albums checked as favorites.{/t}</span>
                <div class="controls">
                    <select name="album_settings_orderFrontpage" id="album_setting_orderFrontpage" required >
                        <option value="created" {if $configs['album_settings']['orderFrontpage'] eq "created"} selected {/if}>{t}Created Date{/t}</option>
                        <option value="views" {if $configs['album_settings']['orderFrontpage'] eq "views"} selected {/if}>{t}Most views{/t}</option>
                        <option value="favorite" {if $configs['album_settings']['orderFrontpage'] eq "favorite"} selected {/if}>{t}Favorites{/t}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="album_time_last" class="form-label">{t}Time of the last album most viewed (days){/t}</label>
                <div class="controls">
                    <input type="number" id="name" name="album_settings_time_last" value="{$configs['album_settings']['time_last']|default:"1"}" required />
                </div>
            </div>

            <div class="form-group">
                <label for="album_settings_total_front" class="form-label">{t}Total in album frontpage{/t}</label>
                <span class="help">{t}Use this to define how many albums can see in the album frontpage.{/t}</span>
                <div class="controls">
                    <input type="number" id="name" name="album_settings_total_front" value="{$configs['album_settings']['total_front']|default:"12"}" required />
                </div>
            </div>
            <div class="form-group">
                <label for="album_settings_total_front_more" class="form-label">{t}Total in album frontpage/widget more albums{/t}</label>
                <span class="help">{t}Total number of album on more albums section in album home frontpage{/t}</span>
                <div class="controls">
                    <input type="number" name="album_settings_total_front_more" value="{$configs['album_settings']['total_front_more']|default:"6"}" required />
                </div>
            </div>
        </div>
    </div>
</div>
</form>
{/block}
