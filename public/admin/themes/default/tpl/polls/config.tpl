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
            <div class="title"><h2>{t}Poll :: Configuration{/t}</h2></div>
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
                     <th align="left">{t}Information about poll module settings{/t}</th>
                 </tr>
            </table>

            <table class="adminform" border="0">
                <tr>
                    <td>
                        <div class="form-wrapper">                                                     
                            <div>
                                <label for="poll[typeValue]">{t}Type values:{/t}</label>
                                <select name="poll_settings[typeValue]" id="poll_settings[typeValue]" class="required">
                                    <option value="percent" {if $configs['poll_settings']['typeValue'] eq 'percent'} selected {/if}>{t}Percents{/t}</option>
                                    <option value="vote" {if $configs['poll_settings']['typeValue'] eq 'vote'} selected {/if}>{t}Votes{/t}</option>
                                 </select>
                                 
                            </div>
                            <br />                            
                            <div>
                                <label for="poll[widthPoll]">{t}Charts width:{/t}</label>
                                <input type="text" class="required" id="name" name="poll_settings[widthPoll]" value="{$configs['poll_settings']['widthPoll']|default:"600"}" />
                            </div>
                            <br />
                            <div>
                                <label for="poll[heightPoll]">{t}Charts height:{/t}</label>
                                <input type="text" class="required" id="name" name="poll_settings[heightPoll]" value="{$configs['poll_settings']['heightPoll']|default:"500"}" />
                            </div>
                            <br />
                           
                        </div>
                    </td>
                    <td> <br/>
                        <div class="help-block">
								<div class="title"><h4>Definition values</h4></div>
                                <div class="content">
                                    <ul>
                                        <li>{t} Use type results if you want that results is showed percents or integer values{/t}</li>

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

        <input type="hidden" id="action" name="action" value="save_config" />
   </form>
</div>
{/block}
