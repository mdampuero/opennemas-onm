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
<form action="{url name=admin_specials_config}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Special :: Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" ><br />
                        {t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_specials}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png"><br />{t}Go back to list{/t}
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
                                <label for="special[total_widget]">{t}Total in widget home:{/t}</label>
                                <input type="text" class="required" name="special_settings[total_widget]" value="{$configs['special_settings']['total_widget']|default:"2"}" />
                            </div>
                            <br />

                            <div>
                                <label for="special[time_last]">{t}Time of the last special most viewed (days):{/t}</label>
                                <input type="text" class="required" id="name" name="special_settings[time_last]" value="{$configs['special_settings']['time_last']|default:"100"}" />
                            </div>
                            <br />

                        </div>
                    </td>
                    <td> <br/>
                        <div class="onm-help-block">
								<div class="title"><h4>{t}Definition values{/t}</h4></div>
                                <div class="content">
                                    <ul>
                                        <li>{t} Use  total in widget special for define how many videos can see in widgets in newspaper frontpage{/t}</li>
                                         <li>{t}Used to define the frontpage specials, the time range of the latest specials are the most viewed{/t}</li>
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
