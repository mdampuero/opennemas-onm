{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    label {
        width:150px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    td {
        vertical-align:middle;
    }
    tr {
        padding: 10px;
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

        <table class="adminheading">
             <tr>
                 <th align="left">{t}Information about opinion widget settings{/t}</th>
             </tr>
        </table>

        <table class="adminform">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="opinion_settings[total_director]">
                            {t}Number of director opinions in Opinion frontpage{/t}:
                        </label>
                    </th>
                    <td>
                        <input type="text" class="required" name="opinion_settings[total_director]"
                               value="{$configs['opinion_settings']['total_director']|default:"1"}" />
                    </td>
                    <td rowspan="3">
                        <div class="help-block margin-left-1">
                            <div class="title"><h4>{t}Opinion settings{/t}</h4></div>
                            <div class="content">
                                <dl>
                                    <dt><strong>{t}Number of director/editorial opinions in Opinion frontpage{/t}</strong></dt>
                                    <dd>{t}In this option you can choose how many opinions of director/editorial will be show in opinion frontpage.{/t}</dd>
                                    <dd>{t}You can choose both, director and editoral opinions, so you have to select them with home icon.{/t}</dd>
                                    <dt><strong>{t}Number of author opinions in frontpage widget{/t}</strong></dt>
                                    <dd>{t}Number of author opinions that will be show in frontpage opinion widget.{/t}</dd>
                                    <dd>{t}You have to select them with favorite icon.{/t}</dd>
                                </dl>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="opinion_settings[total_editorial]">
                            {t}Number of editorial opinions in Opinion frontpage{/t};
                        </label>
                    </th>
                    <td>
                        <input type="text" class="required" name="opinion_settings[total_editorial]"
                               value="{$configs['opinion_settings']['total_editorial']|default:"2"}" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="opinion_settings[total_opinion_authors]">
                            {t}Number of author opinions in frontpage opinion widget:{/t}
                        </label>
                    </th>
                    <td>
                        <input type="text" class="required" name="opinion_settings[total_opinion_authors]"
                               value="{$configs['opinion_settings']['total_opinion_authors']|default:"6"}" />
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="action-bar clearfix">
            <div class="right">
                <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
            </div><!-- / -->
        </div>
    </div>
    <input type="hidden" id="action" name="action" value="save_config" />
</form>

{/block}
