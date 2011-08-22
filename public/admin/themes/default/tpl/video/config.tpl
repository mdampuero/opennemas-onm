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
        width:50%;
    }
    </style>
{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}video :: Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{$smarty.server.PHP_SELF}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div id="{$category}">

            <table class="adminheading">
                 <tr>
                     <th align="left">{t}Information about video module settings{/t}</th>
                 </tr>
            </table>

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
                </tr>
            </table>
            <div class="action-bar">
                <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="save_config" />
   </form>
</div>
{/block}
