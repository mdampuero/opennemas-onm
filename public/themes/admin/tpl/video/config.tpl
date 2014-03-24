{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    label {
        width:150px;
        padding-left:10px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    .form-wrapper {
        margin:10px auto;
        width:90%;
    }
     .help-block {
        max-width: 300px;
     }
    </style>
{/block}

{block name="content"}
<form action="{url name=admin_videos_config}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Videos{/t} :: {t}Settings{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png"><br />
                        {t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_videos}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
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
                <label for="video[total_widget]" class="control-label">{t}Total in widget home{/t}</label>
                <div class="controls">
                    <input type="number" name="video_settings[total_widget]" value="{$configs['video_settings']['total_widget']|default:"4"}" required />
                    <div class="help-block">{t} Use  total in widget home for define how many videos can see in widgets in newspaper frontpage{/t}</div>
                </div>
            </div>

            <div class="control-group">
                <label for="video[total_front]" class="control-label">{t}Total in video frontpage column{/t}</label>
                <div class="controls">
                    <input type="number" name="video_settings[total_front]" value="{$configs['video_settings']['total_front']|default:"2"}" required />
                    <div class="help-block">{t}Use  total in video frontpage column for define how many videos can see in the column left in video frontpage categories{/t}</div>
                </div>
            </div>

            <div class="control-group">
                <label for="video[total_front_more]" class="control-label">{t}Total in video frontpage more videos{/t}</label>
                <div class="controls">
                    <input type="number" name="video_settings[total_front_more]" value="{$configs['video_settings']['total_front_more']|default:"12"}" required />
                    <div class="help-block">{t}Total number of videos on more videos section in video home frontpage{/t}</div>
                </div>
            </div>

            <div class="control-group">
                <label for="video[front_offset]" class="control-label">{t}Total offset in video frontpage more videos{/t}</label>
                <div class="controls">
                    <input type="number" name="video_settings[front_offset]" value="{$configs['video_settings']['front_offset']|default:"3"}" required />
                    <div class="help-block">{t}Total number of videos that are placed on top home video frontpage{/t}</div>
                </div>
            </div>

            <div class="control-group">
                <label for="video[total_widget]" class="control-label">{t}Total in video gallery{/t}</label>
                <div class="controls">
                    <input type="number" name="video_settings[total_gallery]" value="{$configs['video_settings']['total_gallery']|default:"20"}" required />
                    <div class="help-block">{t}Use  Total in video gallery for define how many videos can see in the gallery when you edit or create one article{/t}</div>
                </div>
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="save_config" />
   </form>
</div>
{/block}
