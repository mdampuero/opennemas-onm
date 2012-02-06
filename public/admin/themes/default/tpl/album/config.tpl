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
            <div class="title"><h2>{t}Album :: Configuration{/t}</h2></div>
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
                     <th align="left">{t}Information about album module settings{/t}</th>
                 </tr>
            </table>

            <table class="adminform" border="0">
                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label for="album[total_widget]">{t}Total in widget home:{/t}</label>
                                <input type="text" class="required" name="album_settings[total_widget]" value="{$configs['album_settings']['total_widget']|default:"4"}" />
                            </div>
                            <br />
                            <div>
                                <label for="album[crop_width]">{t}Cover width in widget album:{/t}</label>
                                <input type="text" class="required" id="name" name="album_settings[crop_width]" value="{$configs['album_settings']['crop_width']|default:"300"}" />
                            </div>
                            <br />
                            <div>
                                <label for="album[crop_height]">{t}Cover height in widget album:{/t}</label>
                                <input type="text" class="required" id="name" name="album_settings[crop_height]" value="{$configs['album_settings']['crop_height']|default:"240"}" />
                            </div>
                            <br />
                            <hr>
                             <div>
                                <label for="album_settings[orderFrontpage]">{t}Order album's frontpage by:{/t}</label>
                                 <select name="album_settings[orderFrontpage]" id="album_setting[orderFrontpage]" class="required">
                                    <option value="views" {if $configs['album_settings']['orderFrontpage'] eq "views"} selected {/if}>{t}Most views{/t}</option>
                                    <option value="favorite" {if $configs['album_settings']['orderFrontpage'] eq "favorite"} selected {/if}>{t}Favorites{/t}</option>
                                </select>
                            </div>
                            <br />
                            <div>
                                <label for="album[time_last]">{t}Time of the last album most viewed (days):{/t}</label>
                                <input type="text" class="required" id="name" name="album_settings[time_last]" value="{$configs['album_settings']['time_last']|default:"1"}" />
                            </div>
                            <br />
                            <div>
                                <label for="album[total_front]">{t}Total in album frontpage:{/t}</label>
                                <input type="text" class="required" id="name" name="album_settings[total_front]" value="{$configs['album_settings']['total_front']|default:"2"}" />
                            </div>

                            <br />


                        </div>
                    </td>
                    <td> <br/>
                        <div class="help-block">
								<div class="title"><h4>{t}Definition values{/t}</h4></div>
                                <div class="content">
                                    <ul>
                                        <li>{t}Use  total in widget album for define how many videos can see in widgets in newspaper frontpage{/t}</li>
                                        <li>{t}Cover width in widget album  define image width for crop the cover used in widgets{/t}</li>
                                        <li>{t}Cover height in widget album  define image height for crop the cover used in widgets{/t}</li>
                                        <li>{t}Select if order album's frontpage by most views or albums checked as favorites.{/t} </li>
                                        <li>{t}If previus select most views for order the album's frontpage. Use this to define the frontpage albums, the time range of the latest albums are the most viewed{/t}</li>
                                    </ul>
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button red">
                </div><!-- / -->
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="config" />
    </div>
</form>

{/block}
