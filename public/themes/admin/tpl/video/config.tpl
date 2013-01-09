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
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Video :: Configuration{/t}</h2></div>
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

        <div id="{$category}">

            <table class="adminform" border="0">
                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label for="video[total_widget]">{t}Total in widget home:{/t}</label>
                                <input type="text" class="required" name="video_settings[total_widget]" value="{$configs['video_settings']['total_widget']|default:"4"}" />
                            </div>
                            <br />
                            <div>
                                <label for="video[total_front]">{t}Total in video frontpage column:{/t}</label>
                                <input type="text" class="required" id="name" name="video_settings[total_front]" value="{$configs['video_settings']['total_front']|default:"2"}" />
                            </div>
                            <br />
                            <div>
                                <label for="video[total_front]">{t}Total in video gallery:{/t}</label>
                                <input type="text" class="required" id="name" name="video_settings[total_gallery]" value="{$configs['video_settings']['total_gallery']|default:"20"}" />
                            </div>
                            <br />

                        </div>
                    </td>
                    <td> <br/>
                        <div class="onm-help-block">
								<div class="title"><h4>{t}Settings{/t}</h4></div>
                                <div class="content">
                                    <ul>
                                        <li>{t} Use  total in widget home for define how many videos can see in widgets in newspaper frontpage{/t}</li>
                                        <li> {t} Use  total in video frontpage column for define how many videos can see in the column left in video frontpage categories{/t}</li>
                                        <li> {t} Use  Total in video gallery for define how many videos can see in the gallery when you edit or create one article{/t}</li>
                                    </ul>
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <input type="hidden" id="action" name="action" value="save_config" />
   </form>
</div>
{/block}
