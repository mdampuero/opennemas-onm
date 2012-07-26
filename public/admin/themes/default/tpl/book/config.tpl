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
<form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Book :: Configuration{/t}</h2></div>
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
                     <th align="left">{t}Information about book module settings{/t}</th>
                 </tr>
            </table>

            <table class="adminform" border="0">
                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label for="book[total_widget]">{t}Total in widget home:{/t}</label>
                                <input type="text" class="required" name="book_settings[total_widget]" value="{$configs['book_settings']['book_widget']|default:"4"}" />
                            </div>
                            <br />
                            <div>
                                <label for="book[crop_width]">{t}Max file size:{/t}</label>
                                <input type="text" class="required" id="name" name="book_settings[size_file]" value="{$configs['book_settings']['size_file']|default:"5000000"}" />
                            </div>
                            <br />
                            <div>
                                <label for="book[time_last]">{t}Time of the last books most viewed (days):{/t}</label>
                                <input type="text" class="required" id="name" name="book_settings[time_last]" value="{$configs['book_settings']['time_last']|default:"1"}" />
                            </div>
                            <br />

                        </div>
                    </td>
                    <td> <br/>
                        <div class="onm-help-block">
								<div class="title"><h4>{t}Definition values{/t}</h4></div>
                                <div class="content">
                                    <ul>
                                        <li>{t} Use  total in widget books for define how many books can see in widgets in newspaper frontpage{/t}</li>
                                        <li>{t} Book file size for define max size in bytes{/t}</li>
                                         <li>{t}Used to define the frontpage books, the time range of the latest albums are the most viewed{/t}</li>
                                    </ul>
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="action-bar">
                <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
            </div>
        </div>
    </div>
    <input type="hidden" id="action" name="action" value="save_config" />
</form>

{/block}
