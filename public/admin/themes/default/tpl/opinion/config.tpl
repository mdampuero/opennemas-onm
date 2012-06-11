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
            <div class="title"><h2>{t}Opinion :: Configuration{/t}</h2></div>
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
                     <th align="left">{t}Information about opinion widget settings{/t}</th>
                 </tr>
            </table>

            <table class="adminform" border="0">
                <tr>
                    <td>
                        <div class="form-wrapper">
                             <div>
                                <label for="opinion_settings[total_director]">{t}Number of director articles in the widget:{/t}</label>
                                <input type="text" class="required" name="opinion_settings[total_director]" value="{$configs['opinion_settings']['total_director']|default:"1"}" />
                            </div>
                            <br />
                            <div>
                                <label for="opinion_settings[total_editorial]">{t}Number of editorial articles in the widget:{/t}</label>
                                <input type="text" class="required" name="opinion_settings[total_editorial]" value="{$configs['opinion_settings']['total_editorial']|default:"2"}" />
                            </div>
                            <div>
                                <label for="opinion_settings[total_opinion_authors]">{t}Number of author opinions in the widget:{/t}</label>
                                <input type="text" class="required" name="opinion_settings[total_opinion_authors]" value="{$configs['opinion_settings']['total_opinion_authors']|default:"6"}" />
                            </div>
                            <br />
                        </div>
                    </td>
                </tr>
            </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
                </div><!-- / -->
            </div>
        </div>
    </div>
    <input type="hidden" id="action" name="action" value="save_config" />
</form>

{/block}
