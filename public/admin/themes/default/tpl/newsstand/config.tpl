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
            <div class="title"><h2>{t}Newsstand :: Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{$smarty.server.PHP_SELF}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back to list{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
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
                     <th align="left">{t}Information about newsstand module settings{/t}</th>
                 </tr>
            </table>

            <table class="adminform" border="0">
                <tr>
                    <td>
                        <div class="form-wrapper">

                             <div>
                                <label for="kiosko_settings[orderFrontpage]">{t}Order newsstand frontpage by:{/t}</label>
                                 <select name="kiosko_settings[orderFrontpage]" id="kiosko_settings[orderFrontpage]" class="required">
                                    <option value="sections" {if $configs['kiosko_settings']['orderFrontpage'] eq "sections"} selected {/if}>{t}Sections{/t}</option>
                                    <option value="dates" {if $configs['kiosko_settings']['orderFrontpage'] eq "dates"} selected {/if}>{t}Dates{/t}</option>
                                    <option value="grouped" {if $configs['kiosko_settings']['orderFrontpage'] eq "grouped"} selected {/if}>{t}Dates grouped{/t}</option>
                                </select>
                            </div>
                            <br />
                        </div>
                    </td>
                    <td> <br/>
                        <div class="help-block">
								<div class="title"><h4>{t}Definition values{/t}</h4></div>
                                <div class="content">
                                    <ul>
                                        <li>{t}Select if order newsstand's frontpage by dates or by section.{/t} </li>
                                        <li>{t}Dates grouped is recommended for weekly newspapers.{/t} </li>
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
