{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_albums_config}" method="POST">
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Albums{/t} :: {t}Configuration{/t}</h2></div>
        <ul class="old-button">
            <li>
                <button type="submit">
                    <img border="0" src="{$params.IMAGE_DIR}save.png"><br />
                    {t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_albums}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <div class="form-horizontal panel">
        <div class="control-group">
            <label for="album_settings_total_widget" class="control-label">{t}Total in widget home{/t}</label>
            <div class="controls">
                <input type="number" name="album_settings_total_widget" id="album_settings_total_widget" value="{$configs['album_settings']['total_widget']|default:"4"}" required/>
                <div class="help-block">
                    {t}Use  total in widget album for define how many videos can see in widgets in newspaper frontpage{/t}
                </div>
            </div>
        </div>

        <div class="control-group">
            <label for="album_settings_crop_width" class="control-label">{t}Cover size in widget album (width x height){/t}</label>
            <div class="controls">
                <div class="form-inline-block">
                    <input type="number" id="name" name="album_settings_crop_width" value="{$configs['album_settings']['crop_width']|default:"300"}" required />
                    x
                    <input type="number" id="name" name="album_settings_crop_height" value="{$configs['album_settings']['crop_height']|default:"240"}" required />
                </div>
            </div>
        </div>

        <div class="control-group">
            <label for="album_settings_orderFrontpage" class="control-label">{t}Order album's frontpage by{/t}</label>
            <div class="controls">
                <select name="album_settings_orderFrontpage" id="album_setting_orderFrontpage" required >
                    <option value="views" {if $configs['album_settings']['orderFrontpage'] eq "views"} selected {/if}>{t}Most views{/t}</option>
                    <option value="favorite" {if $configs['album_settings']['orderFrontpage'] eq "favorite"} selected {/if}>{t}Favorites{/t}</option>
                </select>
                <div class="help-block">
                    {t}Select if order album's frontpage by most views or albums checked as favorites.{/t}
                </div>
            </div>
        </div>
        <div class="control-group">
            <label for="album_time_last" class="control-label">{t}Time of the last album most viewed (days){/t}</label>
            <div class="controls">
                <input type="number" id="name" name="album_settings_time_last" value="{$configs['album_settings']['time_last']|default:"1"}" required />
            </div>
        </div>

        <div class="control-group">
            <label for="album_total_front" class="control-label">{t}Total in album frontpage{/t}</label>
            <div class="controls">
                <input type="number" id="name" name="album_settings_total_front" value="{$configs['album_settings']['total_front']|default:"2"}" required />
                <div class="help-block">
                    {t}If previus select most views for order the album's frontpage. Use this to define the frontpage albums, the time range of the latest albums are the most viewed{/t}
                </div>
            </div>
        </div>
    </div>
</div>
</form>
{/block}
